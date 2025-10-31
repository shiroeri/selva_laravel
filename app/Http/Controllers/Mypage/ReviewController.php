<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mypage\ReviewRequest; 
use App\Models\ProductReview; // ProductReviewモデルを使用
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Database\Eloquent\SoftDeletes; // トレイトのuseはモデルで行うため、コントローラでは不要

class ReviewController extends Controller
{
    /**
     * レビュー一覧・管理ページ表示
     */
    public function index()
    {
        $userId = Auth::id();

        // ProductReview モデルを使用
        // モデルに SoftDeletes トレイトが使用されていれば、削除済みデータは自動的に除外される。
        $reviews = ProductReview::where('member_id', $userId) 
                         // product -> subcategory -> category のリレーションをEager Load
                         ->with(['product.subcategory.category']) 
                         ->latest() 
                         ->paginate(5); 

        return view('mypage.reviews.index', compact('reviews'));
    }

    /**
     * レビュー編集フォーム表示
     */
    public function edit(ProductReview $review) 
    {
        // 認証チェック: ログインユーザーのレビューか確認
        if ($review->member_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // 商品情報とカテゴリをEager Load
        $review->load('product.subcategory.category');

        // ロードした商品に対して、関連する全レビューの平均評価をロード
        $review->product->loadAvg('reviews', 'evaluation'); 

        return view('mypage.reviews.edit', compact('review'));
    }

    /**
     * レビュー編集確認画面表示
     */
    public function confirm(ReviewRequest $request, ProductReview $review) 
    {
        // 認証チェック: ログインユーザーのレビューか確認
        if ($review->member_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // バリデーション済みのデータ
        $reviewData = $request->validated();
        
        // 商品情報と平均評価をロード
        $review->load('product.subcategory.category');
        $review->product->loadAvg('reviews', 'evaluation');

        // 確認画面へデータを渡す
        return view('mypage.reviews.confirm', compact('review', 'reviewData')); 
    }

    /**
     * レビュー更新実行
     */
    public function update(Request $request, ProductReview $review) 
    {
        // 認証チェック: ログインユーザーのレビューか確認
        if ($review->member_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // 確認画面から渡される Hidden フィールドのデータを処理
        $review->evaluation = $request->input('review_evaluation');
        $review->comment = $request->input('review_comment');
        
        $review->save(); 

        // レビュー管理ページへ遷移
        return redirect()->route('mypage.reviews.index')->with('status', 'レビューを更新しました。');
    }

    /**
     * レビュー削除確認画面表示
     */
    public function deleteConfirm(ProductReview $review) 
    {
        // 認証チェック: ログインユーザーのレビューか確認
        if ($review->member_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // 商品情報と平均評価をロード (削除確認画面でも表示するため)
        $review->load('product.subcategory.category');
        $review->product->loadAvg('reviews', 'evaluation');

        return view('mypage.reviews.delete_confirm', compact('review'));
    }

    /**
     * レビュー削除実行 (ソフトデリート)
     */
    public function destroy(ProductReview $review) 
    {
        // 認証チェック: ログインユーザーのレビューか確認
        if ($review->member_id !== Auth::id()) {
            abort(403, '権限がありません。');
        }

        // Eloquentのdelete()メソッドを使用
        // ProductReviewモデルで SoftDeletes トレイトが使われている場合、
        // この処理は自動的に deleted_at カラムにタイムスタンプをセットし、ソフトデリートになります。
        $review->delete();

        // レビュー管理ページへ遷移
        return redirect()->route('mypage.reviews.index')->with('status', 'レビューを削除しました。');
    }
}
