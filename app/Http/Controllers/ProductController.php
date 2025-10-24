<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;       // ログインユーザーID取得用
use Illuminate\Support\Facades\Log;         // Log::error() の使用のため
use Illuminate\Support\Facades\Storage;     // ファイル保存のため
use Illuminate\Support\Str;                 // ユニークなファイル名生成のため
use App\Models\ProductCategory;             
use App\Models\ProductSubcategory;          
use App\Models\Product;                     
use Illuminate\Support\Facades\Config;      // assetヘルパーの動作確認のため
use Illuminate\Support\Facades\Validator;   // バリデーション手動実行のため
use Illuminate\Validation\Rule;           // 【追加】カスタムRuleの使用のため

class ProductController extends Controller
{
    
    /**
     * ⑨ 商品登録フォームを表示する (product/create.blade.php)
     * 【修正】バリデーションエラー時のフラッシュデータ（initialImageData）を優先して取得するように変更
     */
    public function create(Request $request) 
    {
        /** @var \Illuminate\Session\SessionManager $session */
        $session = $request->session();

        // POSTリクエストの場合（確認画面から「前に戻る」ボタンが押された場合）
        // Ajax方式では、セッションに一時ファイル情報が入っているため、それを取得する
        if ($request->isMethod('post')) {
            // 入力値をセッションからOldInputに再フラッシュして、フォームに値を戻す
            $input = $session->get('product_input', []);
            $session->flashInput($input);
            
            // GETリダイレクトを行うことで、フォームリフレッシュ後の二重送信を防止
            return redirect()->route('product.create');
        }

        // 1. バリデーションエラーからの復元データ（フラッシュデータ）を優先的に取得
        //    confirmメソッドで with('initialImageData', ...) されている場合
        $initialImageData = $session->get('initialImageData', []);


        // ★★★ 修正箇所: トップページ等からの通常のGETアクセス時にセッションをクリア ★★★
        
        // フラッシュデータがなく（バリデーションエラー後のリダイレクトではない）、
        // かつ、OldInput（withInput()によるデータ）もない場合
        if (empty($initialImageData) && !$session->hasOldInput()) {
            
            // `product_input` (テキスト入力値) と `product_images` (一時画像情報) を完全に破棄
            // これにより、トップページからの新規アクセス時にフォームがリセットされる
            $session->forget('product_input');
            $session->forget('product_images');
            
            Log::info("ProductController@create: トップページからのアクセスを検知し、セッションデータ(product_input, product_images)をクリアしました。");
        }
        
        // ★★★ 修正箇所 ここまで ★★★


        $categories = ProductCategory::all();
        
        // 2. フラッシュデータがない場合（通常のアクセス、または「前に戻る」ボタンが押された場合）は、永続セッションデータを取得
        if (empty($initialImageData)) {
            // 確認画面からのリダイレクトやセッションに残っている一時ファイル情報を取得
            $initialImageData = $session->get('product_images', []);
        }

        // 遷移元 (リファラー) を判定
        $referer = url()->previous();
    
        // 商品一覧画面からの遷移か判定（URLに /products または /product/list が含まれるか）
        if (Str::contains($referer, route('product.list', [], false)) || Str::contains($referer, route('product.list.legacy', [], false))) {
            $source = 'list'; // 商品一覧からの遷移
        } else {
            $source = 'top';  // それ以外（トップなど）からの遷移
        }

        return view('product.create', [
            'categories' => $categories,
            'initialImageData' => $initialImageData, // ビューに一時ファイル情報（パスとURL）を渡す
            'source' => $source,
        ]);

        
    }
    
    /**
     * 【変更】Ajaxで大カテゴリIDに基づいた小カテゴリリストを返す (変更なし)
     * * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategories(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        if (empty($categoryId) || !is_numeric($categoryId)) {
             return response()->json([]);
        }

        // 該当するproduct_category_idを持つ小カテゴリを取得
        $subcategories = ProductSubcategory::where('product_category_id', $categoryId)
                                            ->select('id', 'name')
                                            ->get();

        return response()->json($subcategories);
    }
    
    /**
     * 【新規 Ajax】画像ファイルを一時保存する (変更なし)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        // 1. バリデーションルール定義 (10MBまで, jpg/jpeg/png/gifのみ)
        $rules = [
            'image_file' => ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'], 
            'image_index' => ['required', 'integer', 'between:1,4'], 
        ];

        // 2. カスタムメッセージ定義
        $messages = [
            'image_file.required' => '画像ファイルが選択されていません。',
            'image_file.image' => 'アップロードできるのは画像ファイルのみです。',
            'image_file.mimes' => 'アップロードできる画像の形式は、JPG、JPEG、PNG、GIFのみです。',
            'image_file.max' => '画像ファイルサイズは、:max KB（10MB）までにしてください。',
        ];
        
        // 3. バリデーション実行
        $validator = Validator::make($request->all(), $rules, $messages, ['image_file' => '商品写真']);

        if ($validator->fails()) {
            // バリデーション失敗時、最初のエラーメッセージを抽出して返す
            $errorMessage = $validator->errors()->first('image_file');
            Log::warning('Ajax画像バリデーションエラー: ' . $errorMessage);
            
            return response()->json([
                'success' => false, 
                // 【修正点】エラーメッセージをフロントエンドに渡す
                'message' => $errorMessage, 
            ], 422); // HTTP 422 Unprocessable Entity
        }
        
        // バリデーションが成功した場合
        $memberId = Auth::id() ?? 1; // 認証が未実装の場合のフォールバック
        $uploadedFile = $request->file('image_file');
        $originalExtension = $uploadedFile->getClientOriginalExtension();

        // 4. 一時ファイル名生成と保存
        try {
            // ファイル名生成: memberID_timestamp_random.ext
            $fileName = "{$memberId}_" . time() . '_' . Str::random(10) . '.' . $originalExtension;
            $newPath = "tmp/{$fileName}"; 

            // Storage::disk('public') の tmp フォルダに一時保存
            // putFileAs は成功した場合にパスを返す
            $path = Storage::disk('public')->putFileAs('tmp', $uploadedFile, $fileName, 'public');
            
            // ★★★ 権限強制変更の処理とデバッグ情報取得 (ここから) ★★★
            $fullPath = Storage::disk('public')->path($path);
            $chmodResult = null;
            
            // ファイルが物理的に存在する場合、パーミッションを0777に強制変更
            if (file_exists($fullPath)) {
                $chmodResult = @chmod($fullPath, 0777); 
            }
            // ★★★ 権限強制変更の処理とデバッグ情報取得 (ここまで) ★★★

            // ★★★ デバッグ用ログ ★★★
            // ホスト名とプロトコルの問題を回避するため、asset()で絶対URLを生成
            $storagePath = 'storage/' . $path; // storage/tmp/filename.ext の形
            $publicUrl = asset($storagePath); 
            
            Log::info("--- 画像アップロード成功 ---");
            Log::info("一時保存パス: " . $path);
            Log::info("生成されたURL: " . $publicUrl);
            // ★★★ ここまで ★★★

            // 5. 成功レスポンスを返す
            return response()->json([
                'success' => true,
                'path' => $path, // 例: tmp/1_1678888888_random.jpg
                'url' => $publicUrl, // プレビュー用の公開URL
                'extension' => $originalExtension,
                
                // --- 【重要】デバッグ情報 ---
                'debug' => [
                    'message' => '画像アップロード成功。以下の情報を確認してください。',
                    'fullPath' => $fullPath, // Webサーバー上の絶対パス
                    'fileExists' => file_exists($fullPath), // ファイルが存在するか
                    'chmodResult' => $chmodResult, // chmodの結果 (true=成功, false=失敗)
                    'diskRoot' => Storage::disk('public')->path(''), // publicディスクのルート
                    'assetUrlUsed' => $publicUrl, // asset()で生成されたURL
                ],
            ]);

        } catch (\Exception $e) {
            // エラー時も詳細なデバッグ情報を返す
            Log::error('Ajax画像アップロードエラー: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'ファイルの保存に失敗しました。時間をおいて再度お試しください。',
                // --- 【重要】デバッグ情報 ---
                'debug' => [
                    'errorMessage' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            ], 500);
        }
    }
    
    /**
     * 【修正】商品登録の確認処理 (入力値と一時ファイル情報のセッション保存)
     * バリデーション失敗時に一時画像データをビューに渡し、プレビューを復元できるように修正済み。
     */
    public function confirm(Request $request)
    {
        /** @var \Illuminate\Session\SessionManager $session */
        $session = $request->session();

        // 1. バリデーションルール、メッセージ、属性の定義
        $rules = [
            'name' => ['required', 'string', 'max:100'], 
            'product_category_id' => ['required', 'integer', 'exists:product_categories,id'], 
            
            // 【修正箇所】小カテゴリのバリデーションルールを変更
            'product_subcategory_id' => [
                'required', 
                'integer', 
                // Rule::existsで、product_subcategoriesテーブルに存在し、
                // かつ product_category_id がリクエストで送られた値と一致するレコードのみを許可
                Rule::exists('product_subcategories', 'id')->where(function ($query) use ($request) {
                    return $query->where('product_category_id', $request->input('product_category_id'));
                }),
            ], 
            
            'product_content' => ['required', 'string', 'max:500'], 
            
            // Ajaxで保存された一時ファイル情報をチェックする hidden field
            'image_1_temp_path' => ['nullable', 'string', 'starts_with:tmp/'],
            'image_2_temp_path' => ['nullable', 'string', 'starts_with:tmp/'],
            'image_3_temp_path' => ['nullable', 'string', 'starts_with:tmp/'],
            'image_4_temp_path' => ['nullable', 'string', 'starts_with:tmp/'],
            'image_1_ext' => ['nullable', 'string'], // 拡張子
            'image_2_ext' => ['nullable', 'string'],
            'image_3_ext' => ['nullable', 'string'],
            'image_4_ext' => ['nullable', 'string'],
        ];
        
        $messages = [
            'product_category_id.required' => ':attribute を選択してください。',
            'product_category_id.exists' => ':attribute の値が不正です。存在するカテゴリを選択してください。',
            'product_subcategory_id.required' => ':attribute を選択してください。',
            // 【修正箇所】カスタムRuleによるエラーメッセージ。このメッセージが表示されます。
            'product_subcategory_id.exists' => ':attribute の値が不正です。選択された大カテゴリに紐づくサブカテゴリを選択してください。',
            'name.required' => ':attribute は必須項目です。',
            'product_content.required' => ':attribute は必須項目です。',
        ];

        $attributes = [
            'name' => '商品名',
            'product_category_id' => '商品カテゴリ大', 
            'product_subcategory_id' => '商品カテゴリ小', 
            'product_content' => '商品説明',
        ];
        
        // 2. Validator::make() で手動バリデーションを実行
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        // 3. バリデーション失敗時の処理
        if ($validator->fails()) {
            
            // A. リクエストデータから一時画像情報配列を再構築
            $tempImagePaths = [];
            for ($i = 1; $i <= 4; $i++) {
                $pathKey = 'image_' . $i . '_temp_path';
                $extKey = 'image_' . $i . '_ext';
                
                $path = $request->input($pathKey);
                $ext = $request->input($extKey);

                if (!empty($path)) {
                    // asset() ヘルパーを使用して公開URLを生成
                    $storagePath = 'storage/' . $path;
                    $publicUrl = asset($storagePath);
                    
                    $tempImagePaths['image_' . $i] = [
                        'path' => $path,
                        'extension' => $ext ?? 'png',
                        'url' => $publicUrl,
                    ];
                } else {
                    $tempImagePaths['image_' . $i] = null;
                }
            }

            // B. エラーメッセージ、入力値、そして再構築した一時画像データをフラッシュしてリダイレクト
            // with('initialImageData', ...) で、ビューに $initialImageData として渡す
            return redirect()->back()
                ->withInput()
                ->withErrors($validator)
                // ★ バリデーションエラー時、このフラッシュデータでプレビューを復元する
                ->with('initialImageData', $tempImagePaths); 
        }

        // 4. バリデーション成功時の処理
        $validatedData = $validator->validated();

        // データの整形とセッション保存
        $inputData = [
            'name' => $validatedData['name'],
            'product_category_id' => $validatedData['product_category_id'],
            'product_subcategory_id' => $validatedData['product_subcategory_id'],
            'product_content' => $validatedData['product_content'],
        ];

        // 一時ファイル情報をセッションに保存するための配列を構築
        $tempImagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            $pathKey = 'image_' . $i . '_temp_path';
            $extKey = 'image_' . $i . '_ext';
            
            if (!empty($validatedData[$pathKey])) {
                $path = $validatedData[$pathKey];

                // セッションに保存するURLも asset() を使用して修正
                $storagePath = 'storage/' . $path; // storage/tmp/filename.ext の形
                $publicUrl = asset($storagePath);
                
                $tempImagePaths['image_' . $i] = [
                    'path' => $path,
                    'extension' => $validatedData[$extKey] ?? 'png',
                    // 確認画面での表示用にURLも生成
                    'url' => $publicUrl,
                ];
            } else {
                $tempImagePaths['image_' . $i] = null;
            }
        }
        
        $request->session()->put('product_input', $inputData);
        $request->session()->put('product_images', $tempImagePaths); // 一時ファイル情報を保存

        // 5. 確認画面へリダイレクト
        return redirect()->route('product.show_confirm'); 
    }
    
    /**
     * 【新規】商品登録の確認画面表示 (Ajax一時ファイル情報を使用) (変更なし)
     */
    public function showConfirm(Request $request)
    {
        /** @var \Illuminate\Session\SessionManager $session */
        $session = $request->session();

        if (!$session->has('product_input')) {
            return redirect()->route('product.create')->withErrors(['error' => 'セッションの有効期限切れ、または不正なアクセスです。再度入力してください。']);
        }
        
        $input = $session->get('product_input');
        
        $category = ProductCategory::find($input['product_category_id']);
        $subcategory = ProductSubcategory::find($input['product_subcategory_id']);
        
        // ★一時ファイル情報（URL含む）をビューに渡す
        $imageData = $session->get('product_images');

        return view('product.confirm', [
            'input' => $input,
            'categoryName' => $category ? $category->name : '不明',
            'subcategoryName' => $subcategory ? $subcategory->name : '不明',
            'imageData' => $imageData, // 一時ファイル情報 (path, url, extension を含む)
        ]);
    }

    /**
     * 【修正】商品登録処理を実行する (確認画面から実行される) (変更なし)
     */
    public function executeStore(Request $request)
    {
        /** @var \Illuminate\Session\SessionManager $session */
        $session = $request->session();

        // 1. セッションからデータと一時ファイル情報を取得
        $input = $session->get('product_input');
        $tempImagePaths = $session->get('product_images'); // 一時ファイル情報を含む配列

        if (!$input) {
            return redirect()->route('product.create')->withErrors(['error' => '登録情報が見つかりませんでした。再度入力してください。']);
        }

        $memberId = Auth::id() ?? 1; // 認証が未実装の場合のフォールバック
        $finalImagePaths = [];
        
        // 2. 一時ファイルを永続ストレージに移動 (move)
        try {
            for ($i = 1; $i <= 4; $i++) {
                $imageKey = 'image_' . $i;
                $imageDetail = $tempImagePaths[$imageKey] ?? null;

                if ($imageDetail && !empty($imageDetail['path'])) {
                    $tempPath = $imageDetail['path']; // 例: tmp/1_1678888888_random.jpg
                    $originalExtension = $imageDetail['extension'];

                    // 新しい永続ファイル名生成
                    $fileName = "{$memberId}_" . time() . '_' . Str::random(10) . '.' . $originalExtension;
                    $newPath = "products/{$fileName}"; // 例: products/1_1678888888_random.jpg
                    
                    // ★ 一時ファイル（tmp/*）を永続ディレクトリ（products/*）へ移動
                    if (Storage::disk('public')->exists($tempPath)) {
                        // move() は元のファイルを削除し、新しいパスに移動する
                        Storage::disk('public')->move($tempPath, $newPath);
                        $finalImagePaths[$imageKey] = $newPath; // 永続パス全体を保存
                        Log::info("永続保存成功: {$tempPath} から {$newPath} へ移動完了。");
                    } else {
                        Log::warning("永続保存エラー: 一時ファイルが見つかりません。Path: {$tempPath}");
                        $finalImagePaths[$imageKey] = null;
                    }
                } else {
                    $finalImagePaths[$imageKey] = null;
                }
            }
        } catch (\Exception $e) {
            // ファイル移動処理でエラーが発生した場合
            Log::error('永続ファイル移動エラー: ' . $e->getMessage());
            // セッション情報をクリアせずにフォームに戻すことで、ユーザーの再入力を減らす
            return redirect()->route('product.create')->withErrors(['error' => 'ファイル移動中に予期せぬエラーが発生しました。再度入力してください。']);
        }

        // 3. データのDB保存
        try {
            Product::create(array_merge([
                'member_id' => $memberId,
                'product_category_id' => $input['product_category_id'],
                'product_subcategory_id' => $input['product_subcategory_id'],
                'name' => $input['name'],
                'product_content' => $input['product_content'],
            ], $finalImagePaths));
            
            // 4. セッションクリア (変更なし)
            $session->forget('product_input');
            $session->forget('product_images');

            // 5. 完了後のリダイレクト (変更なし)
            return redirect()->route('product.list')->with('status', '商品が正常に登録されました。'); 

        } catch (\Exception $e) {
            Log::error('商品登録エラー: ' . $e->getMessage());
            
            $session->forget('product_input');
            $session->forget('product_images');
            
            return redirect()->route('product.create')->withErrors(['error' => '商品登録中にエラーが発生しました。再度入力してください。']);
        }
    }
    
    // ★★★ 商品一覧・検索機能の実装 ★★★

    /**
     * 商品一覧と検索結果の表示
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function list(Request $request)
    {
        // 1. 検索条件の取得 (クエリパラメータ)
        $search = $request->only([
            'product_category_id', 
            'product_subcategory_id', 
            'free_word'
        ]);
        
        // 2. 全カテゴリデータの取得 (検索フォーム用)
        $categories = ProductCategory::all();
        // 検索時に小カテゴリを初期表示させるために、選択されている大カテゴリIDを取得
        $selectedCategoryId = $search['product_category_id'] ?? null;
        
        // 3. 検索クエリの構築
        // Productモデルは、カテゴリとサブカテゴリにリレーションが定義されている前提です
        $query = Product::with(['category', 'subcategory']);

        // カテゴリ検索
        if (!empty($search['product_category_id'])) {
            $query->where('product_category_id', $search['product_category_id']);
        }

        // 小カテゴリ検索 (大カテゴリが選択されている前提)
        if (!empty($search['product_subcategory_id'])) {
            $query->where('product_subcategory_id', $search['product_subcategory_id']);
        }
        
        // フリーワード検索 (商品名または商品説明)
        if (!empty($search['free_word'])) {
            // 【重要】OR検索を行うため、クロージャでwhere条件をグループ化
            $query->where(function ($q) use ($search) {
                // LIKE検索用のキーワードをエスケープ
                $keyword = preg_replace('/([%_])/', '\\\$1', $search['free_word']);
                
                // 商品名での部分一致検索
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  // または商品説明での部分一致検索
                  ->orWhere('product_content', 'LIKE', '%' . $keyword . '%');
            });
        }
        
        // 4. 登録順の逆順 (新しいもの順) でソート
        $query->orderBy('created_at', 'desc');

        // 5. 10件ずつのページネーションを実行
        // appends($search) で検索条件をページネーションリンクに引き継ぐ
        $products = $query->paginate(10)->appends($search);

        return view('product.list', [
            'products' => $products,             // 検索結果 (ページネーション済み)
            'categories' => $categories,         // 検索フォーム用カテゴリリスト
            'search' => $search,                 // 現在の検索条件
            'selectedCategoryId' => $selectedCategoryId, // 小カテゴリ初期表示用
        ]);
    }
}
