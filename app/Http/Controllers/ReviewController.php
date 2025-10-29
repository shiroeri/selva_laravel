<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * レビュー投稿フォームを表示する
     * GET /products/{product}/reviews/create
     *
     * @param Product $product 商品ID（ルートモデルバインディングを使用）
     * @return View
     */
    public function create(Product $product): View
    {
        // 総合評価とレビュー件数を取得してビューに渡す
        $reviewData = self::getReviewDataForProductShow($product->id);
        
        // セッションにデータが残っている場合は、それを初期値として使用するために取得
        $oldData = session()->get('review_data') ?? [];

        // 確認画面から「戻る」ボタンで戻った際に、セッションデータが保持されたままになります。
        
        return view('product.review.create', [
            'product' => $product,
            'averageEvaluation' => $reviewData['averageEvaluation'],
            'reviewCount' => $reviewData['reviewCount'],
            'oldData' => $oldData, // 確認画面から「前に戻る」で来た場合、このデータで入力欄が保持される
        ]);
    }
    
    /**
     * レビュー投稿内容を確認する
     * POST /products/{product}/reviews/confirm
     *
     * @param ReviewRequest $request
     * @param Product $product
     * @return View|RedirectResponse
     */
    public function confirm(ReviewRequest $request, Product $product): View|RedirectResponse
    {
        // ログインチェック (ミドルウェアで対応するのが一般的だが、念のため)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'レビュー投稿にはログインが必要です。');
        }

        // バリデーション済みのデータをセッションに一時保存
        $data = $request->validated();
        $request->session()->put('review_data', $data);

        // 総合評価データを取得
        $productReviewData = self::getReviewDataForProductShow($product->id);

        // 確認画面にデータを渡す
        return view('product.review.confirm', [
            'product' => $product,
            'reviewData' => $data,
            'averageEvaluation' => $productReviewData['averageEvaluation'],
            'reviewCount' => $productReviewData['reviewCount'],
        ]);
    }


    /**
     * レビュー投稿処理（確認後）
     * POST /products/{product}/reviews/store
     *
     * @param Request $request LaravelのRequestオブジェクトを使用し、セッションにアクセス
     * @param Product $product URLから受け取る商品モデル
     * @return View|RedirectResponse // 戻り値の型をViewを追加して更新
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $memberId = Auth::id();
        $productId = $product->id;
        
        // セッションからレビューデータを取得し、セッションから削除 (投稿完了のため)
        $reviewData = $request->session()->get('review_data');
        $request->session()->forget('review_data');

        // データがない、またはログイン状態がおかしい場合は入力画面へ戻す
        if (!$reviewData || !$memberId) {
             return redirect()->route('product.review.create', $product)
                              ->with('error', 'レビューデータが失効しました。再度入力してください。');
        }
        
        // セッションデータがReviewRequestの形式と一致するか基本的なチェック (ratingとbodyのみ)
        if (!isset($reviewData['rating']) || !isset($reviewData['body'])) {
             return redirect()->route('product.review.create', $product)
                              ->with('error', 'レビューデータが不正です。再度入力してください。');
        }

        // レビューの保存
        Review::create([
            'member_id' => $memberId,
            'product_id' => $productId,
            'evaluation' => $reviewData['rating'],
            'title' => '',
            'comment' => $reviewData['body'],
        ]);

        // --- PRGパターン適用: 新しいGETルートにリダイレクト ---
        return redirect()->route('product.review.complete', $product)
                         ->with('success', 'レビューを投稿しました。');
        // --------------------------------------------------------
    }
    
    /**
     * レビュー投稿フローを中断し、セッションデータをクリアしてトップ画面へリダイレクトする
     * GET /products/{product}/reviews/cancel-to-top (仮のルート)
     * * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function cancelAndRedirect(Request $request, Product $product): RedirectResponse
    {
        // 新規追加メソッド: セッションデータを明示的にクリアする
        $request->session()->forget('review_data');
        
        // トップ画面へリダイレクトします。
        // ここでは仮に 'top' という名前のルートがあると想定しています。
        // 実際のトップ画面のルート名に置き換えてください。
        return redirect()->route('top')
                         ->with('message', 'レビュー投稿を中断しました。');
    }
    
    /**
     * レビュー投稿完了画面を表示する
     * GET /products/{product}/reviews/complete
     *
     * @param Product $product
     * @return View
     */
    public function complete(Product $product): View
    {
        // レビューデータがセッションに存在しない場合（直接アクセスなど）の処理を
        // 追加することも可能ですが、ここではシンプルにビューを返します。
        return view('product.review.complete', [
            'product' => $product,
        ]);
    }


    /**
     * 商品詳細ページに必要なレビューデータを取得
     *
     * @param int $productId
     * @return array
     */
    public static function getReviewDataForProductShow(int $productId): array
    {
        $memberId = Auth::id();

        // ここでは 'member' リレーションを使用している
        $reviews = Review::with('member')
                         ->where('product_id', $productId)
                         ->orderBy('created_at', 'desc')
                         ->get();

        // evaluation カラムの平均値を取得
        $averageEvaluation = Review::where('product_id', $productId)
                                   ->avg('evaluation');
        
        // ★★★ 修正点: 総合評価の計算はレビューの平均値として、小数点以下は切り上げ（ceil） ★★★
        $averageEvaluationCeiled = ceil($averageEvaluation ?? 0);

        $reviewCount = $reviews->count();

        // ログインしているユーザーが既にレビュー済みかチェック
        $hasReviewed = $memberId ? Review::where('product_id', $productId)->where('member_id', $memberId)->exists() : false;

        return [
            'reviews' => $reviews,
            'averageEvaluation' => $averageEvaluationCeiled, // 切り上げ後の整数を渡す
            'reviewCount' => $reviewCount,
            'hasReviewed' => $hasReviewed,
        ];
    }

    /**
     * 特定の商品のレビュー一覧を表示します。
     *
     * @param  Product  $product  (ルートモデルバインディングにより自動的に取得)
     * @return \Illuminate\View\View
     */
    public function index(Product $product)
    {
        // 1. レビューデータを取得
        // ★ ページネーションを5件
        $reviews = $product->reviews()
                           ->with('member') 
                           ->orderBy('created_at', 'desc')
                           ->paginate(5);

        // 2. 平均評価情報を含む商品情報に、平均評価をロードします。
        $product->loadAvg('reviews', 'evaluation');

        // ★★★ 修正点: 総合評価の計算はレビューの平均値として、小数点以下は切り上げ（ceil） ★★★
        $rawAvg = $product->reviews_avg_evaluation;
        // 小数点以下を切り上げ
        $averageEvaluationCeiled = ceil($rawAvg ?? 0);
        // --------------------------------------------------------------------------
        
        // 3. ビューにデータを渡す
        return view('product.review.index', [
            'product' => $product,
            'reviews' => $reviews,
            'averageEvaluationCeiled' => $averageEvaluationCeiled, // 切り上げ後の値をBladeに渡す
        ]);
    }
}
