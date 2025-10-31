<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー削除確認 | マイページ</title>
    <!-- Tailwind CSS CDNを読み込み -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Interフォントを使用するための設定 -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
        .rating-star {
            font-size: 1.5rem;
            line-height: 1;
        }
    </style>
</head>
<body>

<div class="container mx-auto py-8 sm:py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden p-6 sm:p-10 border border-gray-100">
        
        <!-- ヘッダーとトップに戻るボタン -->
        <div class="flex justify-between items-center mb-8 border-b-4 border-red-600 pb-3">
            <h1 class="text-3xl font-extrabold text-red-700">商品レビュー削除確認</h1>
            <a href="{{ route('top') }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition duration-150 shadow-md transform hover:scale-105">
                トップに戻る
            </a>
        </div>

        {{-- 削除対象の商品情報カード --}}
        {{-- 変数 $review はコントローラから渡され、商品情報 ($review->product) は Eager Load されている前提 --}}
        <div class="mb-10 border-2 border-gray-200 rounded-xl p-4 sm:p-6 bg-white shadow-inner">
            <div class="flex items-start space-x-5">
                {{-- 商品画像（DBからの値を参照） --}}
                <img src="{{ $review->product->image_url ?? 'https://placehold.co/100x100/A0AEC0/ffffff?text=No+Image' }}" 
                     alt="{{ $review->product->name ?? '商品名不明' }}" 
                     onerror="this.onerror=null; this.src='https://placehold.co/100x100/A0AEC0/ffffff?text=No+Image';"
                     class="w-24 h-24 object-cover rounded-lg flex-shrink-0 border border-gray-300 shadow-md">
                
                <div class="mt-1">
                    {{-- 商品名 --}}
                    <h3 class="text-xl font-bold text-gray-900 leading-snug">{{ $review->product->name ?? '該当商品が見つかりません' }}</h3>

                    {{-- 総合評価（小数点切り上げで整数表示） --}}
                    <div class="flex items-center mt-1">
                        <span class="text-sm font-medium text-gray-500 mr-2">総合評価:</span>
                        <div class="text-yellow-500">
                            @php 
                                // reviews_avg_evaluation はコントローラで loadAvg('reviews', 'evaluation') によってロードされる
                                $avgRating = $review->product->reviews_avg_evaluation ?? 0;
                                // 小数点以下を切り上げ、星の数を決定し、表示する
                                $starCount = ceil($avgRating);
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="inline-block rating-star text-base" style="color: {{ $i <= $starCount ? 'orange' : '#ccc' }};">&#9733;</span>
                            @endfor
                            <span class="text-sm font-medium text-gray-700 ml-1">{{ $starCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 削除されるレビュー内容の表示エリア --}}
        <div class="space-y-6">

            {{-- 評価の確認 --}}
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">商品評価</p>
                <div class="text-2xl font-bold text-gray-800 flex items-center">
                    @php 
                        $evaluation = $review->evaluation ?? 0;
                    @endphp
                    <span class="text-yellow-500 mr-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="inline-block rating-star text-2xl" style="color: {{ $i <= $evaluation ? 'orange' : '#ccc' }};">&#9733;</span>
                        @endfor
                    </span>
                    <span class="text-gray-700">{{ $evaluation }}</span>
                </div>
            </div>

            {{-- コメントの確認 --}}
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">商品コメント</p>
                {{-- 改行を維持して表示 --}}
                <p class="text-base text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $review->comment ?? 'コメントなし' }}</p>
            </div>
        </div>

        {{-- アクションボタン --}}
        {{-- destroy メソッドに送信。このアクションでDBからレビューを削除します。 --}}
        {{-- $review を引数に渡すことで、URLは /mypage/reviews/{review_id} となり、DELETEメソッドでルーティングされます。 --}}
        <form method="POST" action="{{ route('mypage.reviews.destroy', $review) }}">
            @csrf
            {{-- DELETEメソッドで削除を実行 --}}
            @method('DELETE')
            
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 pt-8 border-t border-gray-100 mt-10">
                
                {{-- キャンセルボタン (マイページレビュー一覧に戻る) --}}
                <a href="{{ route('mypage.reviews.index') }}"
                   class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-100 rounded-xl shadow-md text-base font-bold text-center transition duration-150 ease-in-out transform hover:scale-[1.02]">
                    前に戻る
                </a>

                {{-- 削除実行ボタン --}}
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-bold rounded-xl shadow-xl hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-red-500/50 transition duration-150 ease-in-out transform hover:scale-[1.02]">
                    レビューを削除する
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
