<!DOCTYPE html>

<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>レビュー編集 | マイページ</title>
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
cursor: pointer;
transition: color 0.1s;
}

.star-rating .filled {
color: #FFC107; 
font-size: 1.5rem;
}
.star-rating .empty {
color: #D1D5DB; 
font-size: 1.5rem;
}
</style>
</head>
<body>

<div class="container mx-auto py-8 sm:py-12 px-4">
<div class="max-w-3xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden p-6 sm:p-10 border border-gray-100">

<!-- ヘッダーとトップに戻るボタン -->
<div class="flex justify-between items-center mb-8 border-b-4 border-indigo-600 pb-3">
    <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー編集</h1>
    <a href="{{ route('top') }}" 
       class="px-4 py-2 text-sm font-medium text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition duration-150 shadow-md transform hover:scale-105">
        トップに戻る
    </a>
</div>

{{-- 変数準備: $review->product から $product と $averageEvaluation を取得 --}}
@php
    // コントローラで load('product') しているため、リレーションはロード済み
    $product = $review->product;
    
    // loadAvg() でロードされた属性名 (reviews_avg_evaluation) を使用
    $averageEvaluation = $product->reviews_avg_evaluation ?? 0;
    
    // Productモデルのアクセサ image_url を使用して画像URLを取得
    $imageUrl = $product->image_url ?? 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
@endphp

{{-- ★★★ 商品情報カード ★★★ --}}
<div class="mb-10 p-6 bg-indigo-50/70 border-2 border-indigo-200 rounded-xl flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6 shadow-inner">
    <div class="flex-shrink-0">
        <img src="{{ $imageUrl }}" alt="{{ $product->name ?? '商品名不明' }}" 
             class="w-20 h-20 object-cover rounded-lg shadow-md border border-gray-300" 
             onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
    </div>
    
    <div class="flex-grow text-center sm:text-left">
        <p class="text-xl sm:text-2xl font-semibold text-gray-800 leading-snug">{{ $product->name ?? '商品名不明' }}</p>
    </div>
    
    <div class="flex-shrink-0 text-center">
        <p class="text-sm font-medium text-gray-600">総合評価</p>
        {{-- 総合評価を小数点第一位まで表示 --}}
        <p class="text-4xl font-extrabold text-indigo-600 mt-1">{{ ceil($averageEvaluation) }}</p> 
        
        {{-- 星表示 --}}
        <div class="star-rating flex justify-center mt-1">
            @for ($i = 1; $i <= 5; $i++)
                {{-- ceil() を使って平均評価に基づいて星を塗る判定 --}}
                <span class="{{ $i <= ceil($averageEvaluation) ? 'filled' : 'empty' }}">★</span>
            @endfor
        </div>
    </div>
</div>
{{-- ★★★ 商品情報カード終了 ★★★ --}}

{{-- レビュー編集フォーム --}}
<form method="POST" action="{{ route('mypage.reviews.confirm', $review) }}">
    @csrf
    
    {{-- 評価 (review_evaluation) --}}
    <div class="mb-8">
        <label for="review_evaluation" class="block text-base font-bold text-gray-700 mb-3">商品評価</label>
        <select id="review_evaluation" name="review_evaluation" 
                class="mt-1 block w-full pl-4 pr-10 py-3 text-lg border-gray-300 rounded-xl shadow-lg appearance-none transition duration-150 ease-in-out focus:outline-none focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500 @error('review_evaluation') border-red-500 ring-red-100 @enderror">
            
            {{-- old() ヘルパーまたは既存データからの値を取得 --}}
            @php
                // LaravelのBlade変数 $review->evaluation が正しいカラム名であると仮定
                $currentEvaluation = old('review_evaluation', $review->evaluation); 
            @endphp
            
            <option value=""  @if (is_null($currentEvaluation)) selected @endif class="text-gray-400">評価を選択してください</option>
            {{-- 選択肢は数字のみ --}}
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @if ((string)$currentEvaluation === (string)$i) selected @endif>
                    {{ $i }}
                </option>
            @endfor
        </select>
        @error('review_evaluation')
            <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- コメント (review_comment) --}}
    <div class="mb-10">
        <label for="review_comment" class="block text-base font-bold text-gray-700 mb-3">商品コメント</label>
        <textarea id="review_comment" name="review_comment" rows="6" 
                  class="mt-1 block w-full border-2 border-gray-300 rounded-xl shadow-lg p-4 focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-base @error('review_comment') border-red-500 ring-red-100 @enderror"
        >{{ old('review_comment', $review->comment) }}</textarea>
        @error('review_comment')
            <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- 送信ボタンと戻るボタン --}}
    <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-6 pt-4 border-t border-gray-100">
        
        {{-- 画面構成図に合わせて「レビュー管理に戻る」ボタンを配置 --}}
        <a href="{{ route('mypage.reviews.index') }}" 
           class="w-full sm:w-auto px-8 py-3 border-2 border-blue-500 text-blue-600 bg-white hover:bg-blue-50 rounded-xl shadow-md text-base font-bold text-center transition duration-150 ease-in-out transform hover:scale-[1.02]">
            レビュー管理に戻る
        </a>

        {{-- 「商品レビュー編集確認」ボタン --}}
        <button type="submit" 
                class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500/50 transition duration-150 ease-in-out transform hover:scale-[1.02]">
            商品レビュー編集確認
        </button>
    </div>
</form>


</div>

</div>

</body>
</html>