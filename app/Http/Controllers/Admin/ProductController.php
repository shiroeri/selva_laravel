<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Productモデルを仮定
use Illuminate\View\View;

/**
 * 管理者向けの商品管理コントローラ
 */
class ProductController extends Controller
{
    /**
     * 商品一覧を表示する (検索・並べ替え機能付き)
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // ----------------------------------------------------
        // 1. 検索・並べ替えパラメータの取得と初期設定
        // ----------------------------------------------------
        $searchParams = $request->only(['id', 'keyword']);
        
        // ソートカラムのデフォルトは 'id'、方向のデフォルトは 'desc'
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        // 有効なソートカラムをチェック
        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'id';
        }
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';


        // ----------------------------------------------------
        // 2. クエリの構築と検索条件の適用 (AND検索)
        // ----------------------------------------------------
        $query = Product::query();

        // ID検索 (products.id と一致)
        if (!empty($searchParams['id'])) {
            // IDは完全に一致するものを検索
            $query->where('products.id', $searchParams['id']);
        }

        // フリーワード検索 (products.name or products.product_content に部分一致)
        if (!empty($searchParams['keyword'])) {
            $keyword = $searchParams['keyword'];
            $query->where(function ($q) use ($keyword) {
                // 商品名 (products.name)
                $q->where('products.name', 'like', "%{$keyword}%")
                  // 商品説明 (products.product_content) - モデルの fillable に合わせて修正
                  ->orWhere('products.product_content', 'like', "%{$keyword}%");
            });
        }
        
        // ----------------------------------------------------
        // 3. 並べ替えとページネーションの適用
        // ----------------------------------------------------
        // ソートを適用
        $query->orderBy($sortColumn, $sortDirection);

        // ページネーション (1ページあたり10件)
        $products = $query->paginate(10)->withQueryString();


        // ----------------------------------------------------
        // 4. Viewへのデータ渡し
        // ----------------------------------------------------
        return view('admin.product.index', [
            'products' => $products,
            'searchParams' => $searchParams,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }
}