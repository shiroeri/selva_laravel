<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Http\Requests\Admin\CategoryRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Logファサードを追加

/**
 * 管理者向け商品カテゴリ管理コントローラー
 */
class CategoryController extends Controller
{
    /**
     * 商品カテゴリ一覧を表示し、検索・並べ替えの処理を行います。
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // ==================================================================
        // 【デバッグ用】実行されるSQLをログに出力 (CategoryControllerに残します)
        // ==================================================================
        DB::listen(function ($query) {
             Log::debug('Category Query', [
                 'sql' => $query->sql,
                 'bindings' => $query->bindings,
                 'time' => $query->time
             ]);
        });

        // ★修正点1: カテゴリー登録・編集フローから外れた場合、セッションデータをクリアする
        // 新規登録用のセッションデータをクリア
        if (session()->has('admin.category.create')) {
            session()->forget('admin.category.create');
        }
        
        // 編集用のセッションデータもクリア
        // 'admin.category.edit.' で始まるキーをすべて探して削除する
        $editSessionKeys = collect(session()->all())->keys()->filter(function ($key) {
            // ★変更点：プレフィックスを 'admin.category.edit.' に変更
            return str_starts_with($key, 'admin.category.edit.');
        });
        
        if ($editSessionKeys->isNotEmpty()) {
            session()->forget($editSessionKeys->toArray());
        }

        // ==================================================================
        
        // ------------------------------------------------------------------
        // 1. 検索条件、並べ替え条件の取得
        // ------------------------------------------------------------------
        // MemberControllerの例に合わせて、すべての検索・ソート関連パラメータを取得
        $searchParams = $request->only(['id', 'keyword', 'sort_column', 'sort_direction']);
        
        // ------------------------------------------------------------------
        // 2. クエリの構築 (ProductCategoryがベース)
        // ------------------------------------------------------------------
        $query = ProductCategory::query(); 

        // 1. ID検索 (ProductCategoryのIDに対して完全一致)
        if (!empty($searchParams['id'])) {
            $query->where('product_categories.id', $searchParams['id']);
        }

        // 2. フリーワード検索 (大カテゴリ名または関連する小カテゴリ名に部分一致)
        if (!empty($searchParams['keyword'])) {
            $keyword = '%' . $searchParams['keyword'] . '%';
            
            $query->where(function ($q) use ($keyword) {
                // 大カテゴリ名 (ProductCategory.name)
                $q->where('name', 'LIKE', $keyword);

                // 関連する小カテゴリ名 (ProductSubcategory.name)
                $q->orWhereHas('subcategories', function ($q2) use ($keyword) {
                    $q2->where('name', 'LIKE', $keyword);
                });
            });
        }
        
        // ------------------------------------------------------------------
        // 3. 並べ替え機能のロジック (MemberControllerのロジックを適用)
        // ------------------------------------------------------------------
        $sortColumn = $searchParams['sort_column'] ?? 'id';
        $sortDirection = $searchParams['sort_direction'] ?? 'desc'; // 初期表示はIDの降順

        $validSortColumns = ['id', 'created_at', 'name']; // 'name'もソート対象に追加
        
        // ソートカラムの検証
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'id';
        }
        
        // ソート方向の検証
        $direction = strtolower($sortDirection);
        if (!in_array($direction, ['asc', 'desc'])) {
            $sortDirection = 'desc'; // 不正な値はデフォルトに戻す
        } else {
            $sortDirection = $direction;
        }
        
        // クエリに並べ替えを適用
        $query->orderBy($sortColumn, $sortDirection);


        // ------------------------------------------------------------------
        // 4. ページネーションとデータ取得
        // ------------------------------------------------------------------

        // 1ページあたり10件表示
        // withQueryString() に相当する appends(request()->except('page')) を使用
        $perPage = 10;
        $categories = $query->paginate($perPage)->appends(request()->except('page'));

        // ビューにデータを渡す
        return view('admin.category.index', [
            'categories' => $categories, 
            'searchParams' => $searchParams, // MemberControllerに合わせて変数名を変更
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * 新規登録フォームを表示する
     * GET /admin/category/create
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        // 新規登録セッションキー
        $sessionKey = 'admin.category.create';
        
        // セッションに保存された古い入力データがあれば取得
        $input = $request->session()->get($sessionKey, []);
        
        // セッションデータがない場合（初回アクセス、または一覧画面から遷移した場合）
        if (empty($input)) {
            // old() ヘルパやデフォルト値で初期化
            $input['category_name'] = old('category_name', '');
            
            // 小カテゴリのoldデータを10個分セット
            for ($i = 0; $i < 10; $i++) {
                // old()はネストされた配列に対応するためキーをドット区切りで指定
                $input['subcategories'][$i] = old("subcategories.{$i}", '');
            }
        }
        
        // 編集フローではないため、$categoryはnull
        return view('admin.category.create', [
            'category' => null, 
            'input' => $input, 
        ]);
    }

    /**
     * 新規登録確認画面を表示する
     * POST /admin/category/confirm
     * * @param CategoryRequest $request
     * @return \Illuminate\View\View
     */
    public function confirm(CategoryRequest $request)
    {
        // 新規登録セッションキー
        $sessionKey = 'admin.category.create';

        // バリデーション済みのデータを取得
        $data = $request->validated();
        
        // 小カテゴリの空データをフィルタリングし、必要なキーに整形
        $subcategories = collect($data['subcategories'])
            ->filter(fn ($name) => !is_null($name) && $name !== '')
            ->values()
            ->toArray();
            
        // 最終的なセッション保存データ
        $input = [
            'category_name' => $data['category_name'],
            'subcategories' => $subcategories, // フィルタリング後のデータ
        ];

        // データをセッションに保存
        $request->session()->put($sessionKey, $input);

        // 確認画面へ
        return view('admin.category.confirm', [
            'input' => $input,
        ]);
    }

    /**
     * 新規登録をDBに保存し、一覧画面へリダイレクトする
     * POST /admin/category/store
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 新規登録セッションキー
        $sessionKey = 'admin.category.create';
        
        // セッションからデータを取得
        $input = $request->session()->get($sessionKey);

        // セッションデータがない場合は入力画面へ戻す
        if (empty($input)) {
            return redirect()->route('admin.category.create')->with('error', '登録データが見つかりません。再度入力してください。');
        }

        DB::transaction(function () use ($input, $request, $sessionKey) {
            // 1. 商品大カテゴリの登録
            $category = ProductCategory::create([
                'name' => $input['category_name'],
            ]);

            // 2. 商品小カテゴリの登録
            if (!empty($input['subcategories'])) {
                $subcategoriesData = [];
                foreach ($input['subcategories'] as $subName) {
                    $subcategoriesData[] = new ProductSubcategory(['name' => $subName]);
                }
                // リレーションシップを使用して一括登録
                $category->subcategories()->saveMany($subcategoriesData);
            }

            // 登録完了後、セッションデータをクリア
            $request->session()->forget($sessionKey);
        });

        // 完了メッセージをセッションに保存して一覧画面へリダイレクト
        return redirect()->route('admin.category.index')->with('status', '商品カテゴリを登録しました。');
    }

    /**
     * 編集フォームを表示する (初期表示またはエラー時の戻り)
     * GET /admin/category/{id}/edit
     * * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, int $id)
    {
        $category = ProductCategory::findOrFail($id);
        
        // ★変更点：編集セッションキーを 'admin.category.edit.' . $id に短縮
        $sessionKey = 'admin.category.edit.' . $id;
        
        // セッションから編集データを取得。なければDBデータを初期値とする
        $input = $request->session()->get($sessionKey, []);

        if (empty($input)) {
            // DBデータから初期値を作成
            $input['id'] = $category->id;
            $input['category_name'] = $category->name;
            
            // 既存のサブカテゴリ名を取得
            $subNames = $category->subcategories->pluck('name')->toArray();
            
            // 10個の入力フィールドに対応するため、残りを空文字列で埋める
            $subcategoriesArray = array_pad($subNames, 10, '');
            $input['subcategories'] = $subcategoriesArray;
            
        } else {
            // セッションにデータがある場合（確認画面から「戻る」など）
            // サブカテゴリがフィルタリングされている可能性があるので、10個のフォームに対応する形式に再整形
            $subNames = $input['subcategories'] ?? [];
            $input['subcategories'] = array_pad($subNames, 10, '');
        }

        return view('admin.category.edit', [
            'category' => $category,
            'input' => $input,
        ]);
    }
    
    /**
     * 編集確認画面を表示する
     * POST /admin/category/{id}/update_confirm
     * * @param CategoryRequest $request
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function updateConfirm(CategoryRequest $request, int $id)
    {
        $category = ProductCategory::findOrFail($id);
        
        // ★変更点：編集セッションキーを 'admin.category.edit.' . $id に短縮
        $sessionKey = 'admin.category.edit.' . $id;

        // バリデーション済みのデータを取得
        $data = $request->validated();
        
        // 小カテゴリの空データをフィルタリングし、必要なキーに整形
        $subcategories = collect($data['subcategories'])
            ->filter(fn ($name) => !is_null($name) && $name !== '')
            ->values()
            ->toArray();
            
        // 最終的なセッション保存データ
        $input = [
            'id' => $id, // IDもセッションに保存
            'category_name' => $data['category_name'],
            'subcategories' => $subcategories, // フィルタリング後のデータ
        ];

        // データをセッションに保存
        $request->session()->put($sessionKey, $input);

        // 確認画面へ
        return view('admin.category.update_confirm', [
            'category' => $category,
            'input' => $input,
        ]);
    }

    /**
     * 編集内容をDBに保存し、一覧画面へリダイレクトする
     * PUT/PATCH /admin/category/{id}
     * * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        // ★変更点：編集セッションキーを 'admin.category.edit.' . $id に短縮
        $sessionKey = 'admin.category.edit.' . $id;
        
        $category = ProductCategory::findOrFail($id);

        // セッションからデータを取得
        $input = $request->session()->get($sessionKey);

        // セッションデータがない場合は編集画面へ戻す
        if (empty($input)) {
            return redirect()->route('admin.category.edit', $id)->with('error', '更新データが見つかりません。再度入力してください。');
        }

        DB::transaction(function () use ($input, $category, $sessionKey, $request) {
            // 1. 商品大カテゴリの更新
            $category->update([
                'name' => $input['category_name'],
            ]);

            // 2. 商品小カテゴリの更新（現在のものを物理削除し、新しいものを登録）
            $category->subcategories()->forceDelete(); // 物理削除
            
            if (!empty($input['subcategories'])) {
                $subcategoriesData = [];
                foreach ($input['subcategories'] as $subName) {
                    $subcategoriesData[] = new ProductSubcategory(['name' => $subName]);
                }
                // リレーションシップを使用して一括登録
                $category->subcategories()->saveMany($subcategoriesData);
            }
            
            // 編集完了後、セッションデータをクリア
            $request->session()->forget($sessionKey);
        });

        // 完了メッセージをセッションに保存して一覧画面へリダイレクト
        return redirect()->route('admin.category.index')->with('status', '商品カテゴリを更新しました。');
    }
    
    /**
     * 削除処理（論理削除）
     * DELETE /admin/category/{id}
     * * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $category = ProductCategory::findOrFail($id);

        DB::transaction(function () use ($category) {
            // 大カテゴリを論理削除
            $category->delete(); 
            // 関連する小カテゴリも物理削除
            $category->subcategories()->forceDelete();
        });

        return redirect()->route('admin.category.index')->with('status', '商品カテゴリを削除しました。');
    }
}