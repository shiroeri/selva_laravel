<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * 商品レビュー一覧（検索・並べ替え・ページャ）
     * GET /admin/review
     */
    public function index(Request $request): View
    {
        // 受け取る検索・並べ替えパラメータ
        $searchParams  = $request->only(['id', 'keyword']);
        $sortColumn    = $request->get('sort_column', 'id');      // 初期：id
        $sortDirection = $request->get('sort_direction', 'desc'); // 初期：降順

        // 並べ替え許可カラムを限定
        $validSortColumns = ['id', 'created_at'];
        if (!in_array($sortColumn, $validSortColumns, true)) {
            $sortColumn = 'id';
        }
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        // クエリ作成（Eloquent）
        $query = ProductReview::query(); // reviews テーブル

        // ── 検索（縦：AND）────────────────────────────
        // ID：完全一致
        if (filled($searchParams['id'] ?? null)) {
            $query->where('reviews.id', $searchParams['id']);
        }

        // フリーワード：comment に対して「横：OR」。空白区切りで複数語を OR。
        if (filled($searchParams['keyword'] ?? null)) {
            // 全角/半角スペースを統一して分割
            $kw = preg_replace('/\x{3000}/u', ' ', $searchParams['keyword']); // 全角→半角
            $terms = array_values(array_filter(array_map('trim', explode(' ', $kw)), fn($v) => $v !== ''));

            if (!empty($terms)) {
                $query->where(function ($q) use ($terms) {
                    foreach ($terms as $i => $t) {
                        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $t) . '%';
                        if ($i === 0) {
                            $q->where('reviews.comment', 'like', $like);
                        } else {
                            $q->orWhere('reviews.comment', 'like', $like);
                        }
                    }
                });
            }
        }

        // 並べ替え
        $query->orderBy($sortColumn, $sortDirection);

        // 1ページ10件
        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.review.index', [
            'reviews'       => $reviews,
            'searchParams'  => $searchParams,
            'sortColumn'    => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }
}
