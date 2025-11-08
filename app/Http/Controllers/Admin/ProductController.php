<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Member;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductReview;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Http\UploadedFile;

/**
 * 管理者向けの商品管理コントローラ
 */
class ProductController extends Controller
{
    /**
     * 商品一覧を表示する (検索・並べ替え機能付き)
     */
    public function index(Request $request): View
    {
        // 新規登録用のセッションデータをクリア
        if (session()->has('admin.product.input')) {
            session()->forget('admin.product.input');
        }
        // 編集用のセッションデータもクリア
        $editSessionKeys = collect(session()->all())->keys()->filter(function ($key) {
            return str_starts_with($key, 'admin.product.edit.');
        });
        if ($editSessionKeys->isNotEmpty()) {
            session()->forget($editSessionKeys->toArray());
        }

        // 検索・並べ替え
        $searchParams = $request->only(['id', 'keyword']);
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'id';
        }
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $query = Product::query();

        if (!empty($searchParams['id'])) {
            $query->where('products.id', $searchParams['id']);
        }

        if (!empty($searchParams['keyword'])) {
            $keyword = $searchParams['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('products.name', 'like', "%{$keyword}%")
                  ->orWhere('products.product_content', 'like', "%{$keyword}%");
            });
        }

        $query->orderBy($sortColumn, $sortDirection);
        $products = $query->paginate(10)->withQueryString();

        return view('admin.product.index', [
            'products'      => $products,
            'searchParams'  => $searchParams,
            'sortColumn'    => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * 新規登録フォームを表示する
     * GET /admin/product/create
     */
    public function create(Request $request): View
    {
        $inputData = $request->session()->get('admin.product.input', []);

        $members       = Member::all();
        $categories    = ProductCategory::all();
        $subcategories = ProductSubcategory::all();

        return view('admin.product.create', [
            'input'         => $inputData,
            'members'       => $members,
            'categories'    => $categories,
            'subcategories' => $subcategories,
        ]);
    }

    /**
     * 新規登録確認画面を表示する
     * POST /admin/product/confirm
     */
    public function confirm(ProductRequest $request): View|RedirectResponse
    {
        // 1. バリデーション
        $validated = $request->validated();

        // 2. 入力を基に整形
        $data = $validated;

        // 画像（Ajax一時パス or 直接アップロード）の取り扱い
        $imageSessionData = [];
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];

        foreach ($imageFields as $field) {
            $fileInputName  = $field . '_file';
            $hiddenPathKey  = $field . '_path';

            if ($request->hasFile($fileInputName)) {
                // ① 直接ファイルが来た場合
                $file = $request->file($fileInputName);
                try {
                    $tempPath   = Storage::disk('public')->putFile('tmp', $file);
                    $base64Data = base64_encode(Storage::disk('public')->get($tempPath));
                    $mimeType   = $file->getMimeType();
                    $dataUrl    = "data:{$mimeType};base64,{$base64Data}";

                    $imageSessionData[$field] = [
                        'original_name' => $file->getClientOriginalName(),
                        'temp_path'     => $tempPath,
                        'mime_type'     => $mimeType,
                        'data_url'      => $dataUrl,
                        'uploaded'      => true,
                        'is_db_image'   => false,
                        'cleared'       => false,
                    ];
                    // 入力データは「一時パス(文字列)」に正規化
                    $data[$field] = $tempPath;
                } catch (\Exception $e) {
                    Log::error('新規登録確認: Base64/一時保存エラー: ' . $e->getMessage());
                    $imageSessionData[$field] = null;
                    $data[$field] = null;
                }

            } elseif ($request->filled($hiddenPathKey)) {
                // ② Ajaxで一時保存済み（hidden に tmp パスが入っている）
                $tempPath = $request->input($hiddenPathKey);
                if (Storage::disk('public')->exists($tempPath)) {
                    try {
                        $full     = Storage::disk('public')->get($tempPath);
                        $mimePath = Storage::disk('public')->path($tempPath);
                        $mimeType = @\Illuminate\Support\Facades\File::mimeType($mimePath) ?: 'image/*';
                        $dataUrl  = "data:{$mimeType};base64," . base64_encode($full);
                    } catch (\Exception $e) {
                        Log::error("新規登録確認: Ajax一時画像の読込失敗: {$tempPath} / " . $e->getMessage());
                        $dataUrl = null;
                        $mimeType = null;
                    }

                    $imageSessionData[$field] = [
                        'original_name' => basename($tempPath),
                        'temp_path'     => $tempPath,
                        'mime_type'     => $mimeType,
                        'data_url'      => $dataUrl,
                        'uploaded'      => true,
                        'is_db_image'   => false,
                        'cleared'       => false,
                    ];
                    $data[$field] = $tempPath;
                } else {
                    Log::warning("新規登録確認: hidden一時パスが存在しません: {$tempPath}");
                    $imageSessionData[$field] = null;
                    $data[$field] = null;
                }

            } else {
                // ③ 画像なし
                $imageSessionData[$field] = null;
                $data[$field] = null;
            }
        }

        // ★ 直列化できない値を除去（特に UploadedFile）
        $data = $this->sanitizeSessionPayload($data);

        // セッション保存
        $request->session()->put('admin.product.input', $data);
        $request->session()->put('admin.product.images.create', $imageSessionData);

        // 3. 確認画面表示用データ
        $viewData = $this->prepareConfirmData(null, $data, $imageSessionData, '商品登録確認');

        // 4. 表示
        return view('admin.product.confirm', $viewData);
    }

    /**
     * 新規登録（完了）
     * POST /admin/product (resource: store)
     */
    public function store(Request $request): RedirectResponse
    {
        $inputData        = $request->session()->get('admin.product.input');
        $imageSessionData = $request->session()->get('admin.product.images.create');

        if (!$inputData || !$imageSessionData) {
            return redirect()->route('admin.product.create')
                ->with('error', 'セッションの有効期限が切れました。再度入力してください。');
        }

        $dataToStore = $inputData;
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];

        try {
            // DBに無いキーを削除
            unset($dataToStore['member_name'], $dataToStore['category_name'], $dataToStore['subcategory_name']);
            foreach ($imageFields as $field) {
                unset($dataToStore[$field . '_filename'], $dataToStore[$field . '_data_url']);
            }

            // 先にレコード作成（一時パスのまま入る）
            $product = Product::create($dataToStore);

            // 一時→恒久保存
            $imageUpdates = [];
            foreach ($imageFields as $field) {
                $sessionImage = $imageSessionData[$field];
                if ($sessionImage && ($sessionImage['uploaded'] ?? false) && Storage::disk('public')->exists($sessionImage['temp_path'])) {
                    $originalFileName = basename($sessionImage['temp_path']);
                    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                    $newFileName = $product->id . '_' . time() . '_' . Str::random(5) . '.' . $extension;
                    $targetPath = 'products/' . $newFileName;

                    $success = Storage::disk('public')->move($sessionImage['temp_path'], $targetPath);
                    if ($success) {
                        $imageUpdates[$field] = $targetPath;
                    } else {
                        Log::error("新規登録: 画像移動失敗: {$sessionImage['temp_path']} -> {$targetPath}");
                        $imageUpdates[$field] = null;
                    }
                } else {
                    $imageUpdates[$field] = null;
                }
            }

            $product->update($imageUpdates);

            // セッションクリア
            $request->session()->forget('admin.product.input');
            $request->session()->forget('admin.product.images.create');

            return redirect()->route('admin.product.index')
                ->with('success', '商品「' . $product->name . '」を登録しました。');

        } catch (\Exception $e) {
            Log::error('商品登録エラー: ' . $e->getMessage());
            return redirect()->route('admin.product.create')
                ->with('error', '登録処理中にエラーが発生しました。再度入力してください。');
        }
    }

    /**
     * 商品詳細を表示する
     * GET /admin/product/{product}
     */
    public function show(Product $product, Request $request): View
    {
        // 関連マスタ取得
        $member      = Member::find($product->member_id);
        $category    = ProductCategory::find($product->product_category_id);
        $subcategory = ProductSubcategory::find($product->product_subcategory_id);

        // 画像URL（storage/products/... を想定）
        $imageFields = ['image_1','image_2','image_3','image_4'];
        $imageUrls   = [];
        foreach ($imageFields as $f) {
            $p = $product->$f;
            $imageUrls[$f] = $p ? asset('storage/' . ltrim($p, '/')) : null;
        }

        // 商品レビュー（1ページ3件、最新順）
        $reviews = ProductReview::with(['member:id,name_sei,name_mei'])
            ->where('product_id', $product->id)
            ->orderByDesc('id')
            ->paginate(3)
            ->withQueryString();

        // ★ 総合評価（全件の evaluation の平均を切り上げ）
        $ratingAvgRaw   = ProductReview::where('product_id', $product->id)->avg('evaluation'); // 小数（null可）
        $ratingCount    = ProductReview::where('product_id', $product->id)->whereNotNull('evaluation')->count();
        $ratingAvgCeil  = $ratingCount ? (int)ceil($ratingAvgRaw) : 0;

        return view('admin.product.show', [
            'product'     => $product,
            'member'      => $member,
            'category'    => $category,
            'subcategory' => $subcategory,
            'imageUrls'   => $imageUrls,
            'reviews'     => $reviews,
            'ratingAvgCeil' => $ratingAvgCeil,
            'ratingCount'   => $ratingCount,

        ]);
    }

    /**
     * 商品のソフトデリート（関連レビューもソフトデリート）
     * DELETE /admin/product/{product}
     */
    public function destroy(Product $product, Request $request): RedirectResponse
    {
        try {
            // 関連レビューもソフトデリート
            ProductReview::where('product_id', $product->id)->delete();

            // 商品ソフトデリート
            $product->delete();

            return redirect()
                ->route('admin.product.index')
                ->with('success', '商品を削除しました。');
        } catch (\Throwable $e) {
            Log::error('商品削除エラー: ' . $e->getMessage());
            return back()->with('error', '削除に失敗しました。時間をおいて再度お試しください。');
        }
    }

    /**
     * 編集フォームを表示
     * GET /admin/product/{product}/edit
     */
    public function edit(Request $request, Product $product): View|RedirectResponse
    {
        $sessionKey = 'admin.product.edit.' . $product->id;
        $inputData  = $request->session()->get($sessionKey, $product->toArray());
        $inputData['id'] = $product->id; // BladeでのID取得を保証

        $members       = Member::all();
        $categories    = ProductCategory::all();
        $subcategories = ProductSubcategory::all();

        return view('admin.product.edit', [
            'product'       => $product,
            'input'         => $inputData,
            'members'       => $members,
            'categories'    => $categories,
            'subcategories' => $subcategories,
        ]);
    }

    /**
     * 確認画面表示用の共通整形
     */
    private function prepareConfirmData(?Product $product, array $inputData, array $imageSessionData, string $pageTitle): array
    {
        $data = $inputData;
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];

        // 会員名は姓＋名で表示
        if (!empty($data['member_id']) && ($m = Member::find($data['member_id']))) {
            $sei = $m->name_sei ?? '';
            $mei = $m->name_mei ?? '';
            $data['member_name'] = trim("{$sei} {$mei}") ?: '不明';
        } else {
            $data['member_name'] = '不明';
        }

        $data['category_name']    = ProductCategory::find($data['product_category_id'])->name    ?? '不明';
        $data['subcategory_name'] = ProductSubcategory::find($data['product_subcategory_id'])->name ?? '不明';

        foreach ($imageFields as $field) {
            $currentData = $imageSessionData[$field] ?? null;
            $data[$field . '_filename'] = 'なし';
            $data[$field . '_data_url'] = null;
            $data[$field . '_url']      = null;

            if (!$currentData) {
                continue;
            } elseif ($currentData['cleared'] ?? false) {
                $originalPath = $product->$field ?? null;
                $data[$field . '_filename'] = '削除（' . basename($originalPath ?? 'ファイルなし') . '）';
            } elseif ($currentData['uploaded'] ?? false) {
                $data[$field . '_filename'] = ($currentData['original_name'] ?? basename($currentData['temp_path'] ?? '')) . ' (新規アップロード)';
                $data[$field . '_data_url'] = $currentData['data_url'] ?? null;
            } elseif ($currentData['is_db_image'] ?? false) {
                $dbPath = $currentData['path'] ?? null;
                $data[$field . '_filename'] = basename($dbPath) . ' (既存維持)';

                if ($dbPath) {
                    $relativePath = str_replace('storage/', '', $dbPath);
                    $relativePath = ltrim($relativePath, '/');
                    if (Storage::disk('public')->exists($relativePath)) {
                        $localUrl = Storage::disk('public')->url($relativePath);
                        $publicBaseUrl = 'https://laravel.erika.study.icoma.jp';
                        $generatedUrl = Str::replaceFirst(config('app.url') . '/storage', $publicBaseUrl . '/storage', $localUrl);
                        $generatedUrl = Str::replaceFirst('http://127.0.0.1:8000/storage', $publicBaseUrl . '/storage', $generatedUrl);
                        $data[$field . '_url'] = $generatedUrl;
                    } else {
                        $data[$field . '_url'] = null;
                    }
                }
            }
        }

        return [
            'id'          => $product ? $product->id : null,
            'product'     => $product,
            'input'       => $data,
            'pageTitle'   => $pageTitle,
            'routePrefix' => 'admin.product',
            'isEdit'      => (bool)$product,
        ];
    }

    /**
     * 編集確認（GET: 戻る等）
     * GET /admin/product/{product}/confirm
     */
    public function showUpdateConfirm(Request $request, Product $product): View|RedirectResponse
    {
        $sessionKey       = 'admin.product.edit.' . $product->id;
        $inputData        = $request->session()->get($sessionKey);
        $imageSessionData = $request->session()->get($sessionKey . '.images');

        if (!$inputData || !$imageSessionData) {
            return redirect()->route('admin.product.edit', $product->id)
                ->with('error', '確認セッションが見つかりませんでした。再度編集してください。');
        }

        $inputData['id'] = $product->id;

        $viewData = $this->prepareConfirmData($product, $inputData, $imageSessionData, '商品編集確認');
        return view('admin.product.update_confirm', $viewData);
    }

    /**
     * 編集確認（PUT/PATCH: フォームから）
     * PUT/PATCH /admin/product/{product}/confirm
     */
    public function updateConfirm(Request $request, Product $product): View|RedirectResponse
    {
        $productRequest = new ProductRequest();
        $rules = $productRequest->rules();

        $messages = [
            'image_1_file.image' => ':attribute は画像ファイルを選択してください。',
            'image_1_file.mimes' => ':attribute のファイル形式は、JPG、JPEG、PNG、GIFのいずれかにしてください。',
            'image_1_file.max'   => ':attribute のファイルサイズは、10MB以下にしてください。',
            'required'           => ':attribute は必須項目です。',
            'integer'            => ':attribute は数値で入力してください。',
            'string'             => ':attribute は文字列で入力してください。',
            'max'                => ':attribute は:max文字以下で入力してください。',
            'min'                => ':attribute は:min以上の値を入力してください。',
            'exists'             => '選択された:attribute は存在しません。',
        ];
        $attributes = $productRequest->attributes();

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return redirect()->route('admin.product.edit', $product->id)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $idValidator = Validator::make(['id' => $product->id], [
            'id' => ['required', 'integer', Rule::exists('products', 'id')->where('id', $product->id)]
        ]);
        if ($idValidator->fails()) {
            return redirect()->route('admin.product.index')
                ->with('error', '編集対象の商品IDが見つかりませんでした。');
        }

        $data = $validated;
        $sessionKey = 'admin.product.edit.' . $product->id;

        $imageSessionData = $request->session()->get($sessionKey . '.images', []);
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];

        foreach ($imageFields as $field) {
            $isCleared     = $request->has($field . '_clear');
            $fileInputName = $field . '_file';

            if ($request->hasFile($fileInputName)) {
                // a) 新規アップロード
                $file = $request->file($fileInputName);
                try {
                    $tempPath   = Storage::disk('public')->putFile('tmp', $file);
                    $base64Data = base64_encode(Storage::disk('public')->get($tempPath));
                    $mimeType   = $file->getMimeType();
                    $dataUrl    = "data:{$mimeType};base64,{$base64Data}";

                    $imageSessionData[$field] = [
                        'original_name' => $file->getClientOriginalName(),
                        'temp_path'     => $tempPath,
                        'mime_type'     => $mimeType,
                        'data_url'      => $dataUrl,
                        'uploaded'      => true,
                        'is_db_image'   => false,
                        'cleared'       => false,
                    ];
                    // 入力データは「一時パス(文字列)」に正規化
                    $data[$field] = $tempPath;
                } catch (\Exception $e) {
                    Log::error('編集確認: Base64/一時保存エラー: ' . $e->getMessage());
                    $imageSessionData[$field] = null;
                    $data[$field] = null;
                }
                continue;

            } elseif ($isCleared) {
                // b) 削除指示
                $imageSessionData[$field] = [
                    'original_name' => '削除',
                    'path'          => $product->$field ?? null,
                    'uploaded'      => false,
                    'is_db_image'   => (bool)$product->$field,
                    'cleared'       => true,
                    'data_url'      => null,
                ];
                $data[$field] = null;

            } elseif ($product->$field) {
                // c) 既存維持
                $imageSessionData[$field] = [
                    'original_name' => basename($product->$field),
                    'path'          => $product->$field,
                    'uploaded'      => false,
                    'is_db_image'   => true,
                    'cleared'       => false,
                    'data_url'      => null,
                ];
                // hidden の保持パスを優先して戻す
                $data[$field] = $request->input($field . '_path', $product->$field);

            } else {
                // d) もともと無し
                $imageSessionData[$field] = null;
                $data[$field] = null;
            }
        }

        // ★ 直列化できない値を除去（特に UploadedFile）
        $data = $this->sanitizeSessionPayload($data);

        // セッション保存
        $request->session()->put($sessionKey, $data);
        $request->session()->put($sessionKey . '.images', $imageSessionData);

        $viewData = $this->prepareConfirmData($product, $data, $imageSessionData, '商品編集確認');
        return view('admin.product.update_confirm', $viewData);
    }

    /**
     * 編集完了（更新）
     * PUT/PATCH /admin/product/{product}
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $sessionKey       = 'admin.product.edit.' . $product->id;
        $inputData        = $request->session()->get($sessionKey);
        $imageSessionData = $request->session()->get($sessionKey . '.images');

        if (!$inputData || !$imageSessionData) {
            return redirect()->route('admin.product.edit', ['product' => $product->id])
                ->with('error', 'セッションの有効期限が切れました。再度入力してください。');
        }

        $dataToStore = $inputData;

        try {
            $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];
            $imageUpdates = [];

            // DBに無いキーを削除
            unset($dataToStore['member_name'], $dataToStore['category_name'], $dataToStore['subcategory_name']);
            foreach ($imageFields as $field) {
                unset($dataToStore[$field . '_filename'], $dataToStore[$field . '_data_url']);
            }
            unset($dataToStore['id']);

            foreach ($imageFields as $field) {
                $currentData = $imageSessionData[$field];
                $currentDbPath = $product->$field;

                if (!$currentData) {
                    // 何もしない（元の値を維持）
                }

                if (($currentData['cleared'] ?? false) && $currentDbPath) {
                    Storage::disk('public')->delete($currentDbPath);
                    $imageUpdates[$field] = null;

                } elseif ($currentData['uploaded'] ?? false) {
                    if ($currentDbPath) {
                        Storage::disk('public')->delete($currentDbPath);
                    }
                    if (Storage::disk('public')->exists($currentData['temp_path'])) {
                        $fileName   = basename($currentData['temp_path']);
                        $targetPath = 'products/' . $fileName;

                        $success = Storage::disk('public')->move($currentData['temp_path'], $targetPath);
                        if ($success) {
                            $imageUpdates[$field] = $targetPath;
                        } else {
                            Log::error("編集更新: 画像移動失敗: {$currentData['temp_path']} -> {$targetPath}");
                            $imageUpdates[$field] = null;
                        }
                    }

                } else {
                    // 既存維持：$dataToStore の値をそのまま（hiddenで保持）
                }
            }

            $product->update(array_merge($dataToStore, $imageUpdates));

            $request->session()->forget($sessionKey);
            $request->session()->forget($sessionKey . '.images');

            return redirect()->route('admin.product.index')
                ->with('success', '商品「' . $product->name . '」を更新しました。');

        } catch (\Exception $e) {
            Log::error('商品編集エラー: ' . $e->getMessage());
            return redirect()->route('admin.product.edit', ['product' => $product->id])
                ->with('error', '更新処理中にエラーが発生しました。再度入力してください。');
        }
    }

    /**
     * Ajax: 大カテゴリに基づく小カテゴリ一覧
     * GET /admin/product/subcategories
     */
    public function getSubcategories(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');
        if (empty($categoryId) || !is_numeric($categoryId)) {
            return response()->json([]);
        }

        $subcategories = ProductSubcategory::where('product_category_id', $categoryId)
            ->select('id', 'name')
            ->get();

        return response()->json($subcategories);
    }

    /**
     * Ajax: 画像の一時アップロード
     * POST /admin/product/upload-image
     */
    public function ajaxUploadImage(Request $request): JsonResponse
    {
        // ★ クライアント側でどの拡張子でも選べるようにするため、ここでは拡張子バリデーションを厳密にせず
        //   サイズ上限のみ基本チェック。拡張子はアプリ独自ロジックで警告返却（テストしやすい仕様）。
        $rules = [
            'image_file'  => ['required', 'file', 'max:10240'], // 10MB
            'image_index' => ['required', 'integer', 'between:1,4'],
        ];
        $messages = [
            'image_file.required' => '画像ファイルが選択されていません。',
            'image_file.max'      => '画像ファイルサイズは、10MB以下にしてください。',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, ['image_file' => '商品写真']);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('image_file');
            Log::warning('Admin/ProductController@uploadImage: Ajax画像バリデーションエラー: ' . $errorMessage);
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 200);
        }

        $memberId = Auth::id() ?? 1;
        $uploadedFile = $request->file('image_file');

        // 許可拡張子 & 10MB以内チェック（拡張子は警告として返すだけ）
        $originalExtension = strtolower($uploadedFile->getClientOriginalExtension() ?: '');
        $fileSizeBytes     = $uploadedFile->getSize();
        $allowed           = ['jpg', 'jpeg', 'png', 'gif'];
        $withinLimit       = $fileSizeBytes <= 10 * 1024 * 1024;

        if (!$withinLimit) {
            return response()->json([
                'success' => false,
                'message' => '画像ファイルサイズは、10MB以下にしてください。',
            ], 200);
        }

        if (!in_array($originalExtension, $allowed)) {
            // 非対応拡張子：アップロード自体は成功させない（プレビューも出さない）
            return response()->json([
                'success' => false,
                'message' => 'アップロードできるのは jpg / jpeg / png / gif のみです。',
            ], 200);
        }

        try {
            $fileName = "{$memberId}_" . time() . '_' . Str::random(10) . '.' . $originalExtension;
            // tmp に統一
            $path = Storage::disk('public')->putFileAs('tmp', $uploadedFile, $fileName, 'public');

            $fullPath = Storage::disk('public')->path($path);
            $chmodResult = null;
            if (file_exists($fullPath)) {
                $chmodResult = @chmod($fullPath, 0777);
            }

            $storagePath = 'storage/' . $path;
            $publicUrl = asset($storagePath);

            Log::info("--- Admin/ProductController@uploadImage: 画像アップロード成功 ---");
            Log::info("一時保存パス: " . $path);

            return response()->json([
                'success'   => true,
                'path'      => $path,      // tmp/xxxxx.ext
                'url'       => $publicUrl, // /storage/tmp/xxxxx.ext
                'extension' => $originalExtension,
                'debug'     => [
                    'message'     => '画像アップロード成功。以下の情報を確認してください。',
                    'fullPath'    => $fullPath,
                    'fileExists'  => file_exists($fullPath),
                    'chmodResult' => $chmodResult,
                    'diskRoot'    => Storage::disk('public')->path(''),
                    'assetUrlUsed'=> $publicUrl,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Admin/ProductController@uploadImage: Ajax画像アップロードエラー: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ファイルの保存に失敗しました。時間をおいて再度お試しください。',
            ], 200);
        }
    }

    /**
     * セッション保存前に UploadedFile など直列化できない値を除去
     */
    private function sanitizeSessionPayload(array $data): array
    {
        // 典型的なファイルキーを明示的に除去
        foreach (['image_1_file','image_2_file','image_3_file','image_4_file'] as $fileKey) {
            if (array_key_exists($fileKey, $data)) {
                unset($data[$fileKey]);
            }
        }
        // 念のため、値が UploadedFile / resource / クロージャなど非スカラーなら除去
        foreach ($data as $k => $v) {
            if ($v instanceof UploadedFile) {
                unset($data[$k]);
            }
            // 他の非直列化要素が入っていないか最低限チェック（配列・スカラー・null はOK）
            if (is_object($v) && !($v instanceof \Stringable)) {
                // Stringable 以外のオブジェクトは危険なので落とす
                unset($data[$k]);
            }
            if (is_resource($v)) {
                unset($data[$k]);
            }
        }
        return $data;
    }
}
