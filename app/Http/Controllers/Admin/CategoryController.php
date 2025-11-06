<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
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
}
