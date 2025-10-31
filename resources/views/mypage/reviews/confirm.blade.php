<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー編集確認 | マイページ</title>
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
        <div class="flex justify-between items-center mb-8 border-b-4 border-indigo-600 pb-3">
            <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー編集確認</h1>
            <a href="{{ route('top') }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition duration-150 shadow-md transform hover:scale-105">
                トップに戻る
            </a>
        </div>
        
        {{-- 編集対象の商品情報カード (edit.blade.phpと統一) --}}
        {{-- 変数 $review はコントローラから渡され、商品情報 ($review->product) は Eager Load されている前提 --}}
        <div class="mb-10 border-2 border-indigo-200 rounded-xl p-4 sm:p-6 bg-indigo-50/70 shadow-inner">
            <div class="flex items-start space-x-5">
                {{-- 商品画像（DBからの値を参照） --}}
                <img src="{{ $review->product->image_url ?? 'https://placehold.co/100x100/A0AEC0/ffffff?text=No+Image' }}" 
                     alt="{{ $review->product->name ?? '商品名不明' }}" 
                     onerror="this.onerror=null; this.src='https://placehold.co/100x100/A0AEC0/ffffff?text=No+Image';"
                     class="w-24 h-24 object-cover rounded-lg flex-shrink-0 border border-indigo-300 shadow-md">
                
                <div class="mt-1">
                    {{-- 商品名 --}}
                    <h3 class="text-xl font-bold text-gray-900 leading-snug">{{ $review->product->name ?? '該当商品が見つかりません' }}</h3>

                    {{-- 総合評価（コントローラでロードした reviews_avg_evaluation を参照） --}}
                    <div class="flex items-center mt-1">
                        <span class="text-sm font-medium text-gray-500 mr-2">総合評価:</span>
                        <div class="text-yellow-500">
                            {{-- コントローラでロードした reviews_avg_evaluation を使用 --}}
                            @php 
                                // reviews_avg_evaluation はコントローラで loadAvg('reviews', 'evaluation') によってロードされる
                                $avgRating = $review->product->reviews_avg_evaluation ?? 0;
                                // ★修正点1: ceil() を使って小数点以下を切り上げ、星の数を決定
                                $starCount = ceil($avgRating);
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                {{-- ★修正点1適用: $i <= $starCount で星の表示を制御 --}}
                                <span class="inline-block rating-star text-base" style="color: {{ $i <= $starCount ? 'orange' : '#ccc' }};">&#9733;</span>
                            @endfor
                            {{-- ★修正点2: ceil() を使って小数点以下を切り上げた整数を表示 --}}
                            <span class="text-sm font-medium text-gray-700 ml-1">{{ $starCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 入力内容の表示エリア --}}
        <div class="space-y-6">
            
            {{-- 評価の確認 --}}
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">商品評価</p>
                <div class="text-2xl font-bold text-gray-800 flex items-center">
                    {{-- ★確認する入力値: $reviewData['review_evaluation'] を使用 --}}
                    @php 
                        $evaluation = $reviewData['review_evaluation'] ?? 0;
                    @endphp
                    <span class="text-yellow-500 mr-3">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="inline-block rating-star text-2xl" style="color: {{ $i <= $evaluation ? 'orange' : '#ccc' }};">&#9733;</span>
                        @endfor
                    </span>
                    <span class="text-indigo-600">{{ $evaluation }}</span>
                </div>
            </div>

            {{-- コメントの確認 --}}
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">商品コメント</p>
                {{-- ★確認する入力値: $reviewData['review_comment'] を使用し、改行を維持して表示 --}}
                <p class="text-base text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $reviewData['review_comment'] ?? '入力されていません' }}</p>
            </div>
        </div>

        {{-- アクションボタン --}}
        {{-- update メソッドに送信する際に、Hiddenフィールドの name 属性をコントローラで処理しているキー名に合わせる --}}
        <form method="POST" action="{{ route('mypage.reviews.update', $review) }}">
            @csrf
            {{-- PATCHメソッドで更新を行う --}}
            @method('PUT')
            
            {{-- フォームからの入力値をHiddenフィールドで引き継ぐ --}}
            {{-- Hidden フィールドの名前をコントローラ側で取得しやすいように 'review_evaluation' と 'review_comment' のままにする --}}
            <input type="hidden" name="review_evaluation" value="{{ $reviewData['review_evaluation'] ?? '' }}">
            <input type="hidden" name="review_comment" value="{{ $reviewData['review_comment'] ?? '' }}">
            
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 pt-8 border-t border-gray-100 mt-10">
                
                {{-- 前に戻るボタン (編集フォームに戻る) --}}
                <button type="button" 
                        onclick="history.back()"
                        class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-700 bg-white hover:bg-gray-100 rounded-xl shadow-md text-base font-bold text-center transition duration-150 ease-in-out transform hover:scale-[1.02]">
                    前に戻る
                </button>

                {{-- 更新するボタン (DB更新を実行) --}}
                <button type="submit" 
                        class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out transform hover:scale-[1.02]">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
