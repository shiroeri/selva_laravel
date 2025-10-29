{{--
    商品レビューセクション

    使用する変数:
    - $product: \App\Models\Product (商品情報)
    - $reviews: \Illuminate\Pagination\LengthAwarePaginator または \Illuminate\Database\Eloquent\Collection (レビュー一覧)
    - $averageEvaluation: float (平均評価)
    - $reviewCount: int (レビュー件数)
    - $hasReviewed: bool (ログインユーザーが投稿済みか)
--}}
<div class="review-section mt-10 p-6 bg-white shadow-lg rounded-xl">
    <h2 class="text-2xl font-bold text-gray-800 border-b-2 pb-2 mb-6">商品レビュー</h2>

    {{-- 1. 概要と投稿ボタン --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4">
        {{-- 平均評価 --}}
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <span class="">総合評価</span>
            <div class="text-sm">
                {{-- 星の表示 (ここでは簡易的に5段階評価の★を表示) --}}
                <div class="text-yellow-500 text-2xl" style="letter-spacing: 0.1em;">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= ceil($averageEvaluation))
                            ★
                        @else
                            <span class="text-gray-300">★</span>
                        @endif
                    @endfor
                </div>
            </div>
            <span class="text-4xl font-extrabold text-indigo-600 mr-2">
                {{ ceil($averageEvaluation) }}
            </span>
        </div>
    </div>
    {{-- 2. レビュー一覧へのリンクを追加 --}}
    <div class="pt-4">
        <a href="{{ route('product.reviews.index', $product) }}" 
           class="text-lg font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out flex items-center group">
            >> レビューを見る
        </a>
    </div>

</div>
