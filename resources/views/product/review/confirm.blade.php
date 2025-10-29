@php
// Controllerから渡されることを想定したダミー/フォールバックデータ

// 1. 商品情報
$product = $product ?? (object)['id' => 1, 'name' => '商品名不明', 'image_1' => 'dummy_path.jpg'];

// 2. 確認するレビューデータ (ユーザーが今回入力したデータ)
$reviewData = $reviewData ?? ['rating' => 5, 'body' => '確認データがありません。'];

// 3. 商品の総合評価情報 (create.blade.phpと共通の表示に必要なため追加)
//    ※Controllerが渡さない場合に備えてダミー値を設定
//    【重要】Controllerでこの変数に「商品の実際の平均評価」を渡しているか確認してください。
$averageEvaluation = $averageEvaluation ?? 4.2;
$reviewCount = $reviewCount ?? 150;
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー登録内容確認</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル: 星の評価表示 */
        .star-rating { color: #f59e0b; font-size: 1.5rem; }
        .star-rating .empty { color: #d1d5db; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4 sm:p-8 font-sans">

<div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 shadow-xl rounded-2xl border border-gray-200">
    
    {{-- ヘッダーとトップに戻るボタン --}}
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー登録確認</h1>
        </div>
        
        {{-- トップに戻るボタン (右上) --}}
        <a href="{{ route('product.review.cancel_to_top', $product) }}" class="flex items-center space-x-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition p-2 rounded-lg bg-gray-50 hover:bg-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2v10a1 1 0 01-1 1h-3"></path></svg>
            <span>トップに戻る</span>
        </a>
    </div>

    {{-- 商品情報カード (写真、名前、総合評価) --}}
    <div class="mb-8 p-6 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
        <div class="flex-shrink-0">
            @php
                // DBに保存されている画像パス (image_1) を使用
                $imagePath = $product->image_1 ?? null;
                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
            @endphp
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg shadow-md" 
                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
        </div>
        <div class="flex-grow text-center sm:text-left">
            <p class="text-2xl font-semibold text-gray-700 mt-1">{{ $product->name ?? '商品名不明' }}</p>
        </div>
        <div class="flex-shrink-0 text-center">
            <p class="text-sm font-medium text-gray-600">総合評価</p>
            {{-- 【商品全体の平均評価】を小数点以下切り上げで表示します --}}
            <p class="text-4xl font-extrabold text-indigo-600 mt-1">{{ ceil($averageEvaluation) }}</p>
            <div class="star-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= ceil($averageEvaluation) ? 'filled' : 'empty' }}">★</span>
                @endfor
            </div>
        </div>
    </div>

    {{-- 確認内容の表示 --}}
    <div class="space-y-6 mb-10">
        {{-- 評価 (ユーザーが今回入力した点数) --}}
        <div class="p-4 border rounded-lg bg-gray-50">
            <p class="text-lg font-bold text-gray-700 mb-1">商品評価</p>
            <div class="text-3xl font-extrabold text-indigo-600">
                {{ $reviewData['rating'] ?? 'N/A' }} 点
            </div>
            <div class="star-rating mt-1">
                @for ($i = 1; $i <= 5; $i++)
                    {{-- ユーザーの評価点数に基づいて星を表示 --}}
                    <span class="{{ $i <= ($reviewData['rating'] ?? 0) ? 'filled' : 'empty' }}">★</span>
                @endfor
            </div>
        </div>

        {{-- コメント --}}
        <div class="p-4 border rounded-lg bg-gray-50">
            <p class="text-lg font-bold text-gray-700 mb-2">商品コメント</p>
            <div class="prose max-w-none p-3 bg-white rounded-lg border border-gray-200 shadow-inner">
                {{-- 改行を反映させるために nl2br を使用 --}}
                @if (!empty($reviewData['body']))
                    <p class="whitespace-pre-wrap text-gray-800 leading-relaxed">{!! (e($reviewData['body'])) !!}</p>
                @else
                    <p class="text-gray-500 italic">コメントなし</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- フォーム: actionはstoreルート --}}
    <form method="POST" action="{{ $product->id ? route('product.review.store', $product) : '#' }}">
        @csrf

        {{-- 送信データを隠しフィールドとして含める --}}
        <input type="hidden" name="rating" value="{{ $reviewData['rating'] ?? '' }}">
        <input type="hidden" name="body" value="{{ $reviewData['body'] ?? '' }}">

        {{-- フッターボタン配置 --}}
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            {{-- 修正ボタン: createルートに戻る --}}
            <a href="{{ $product->id ? route('product.review.create', $product) : '#' }}" 
               class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-lg font-medium rounded-xl shadow-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300">
                前に戻る
            </a>
            
            {{-- 登録確定ボタン --}}
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 border border-transparent text-lg font-medium rounded-xl shadow-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500 transition duration-300 transform hover:scale-[1.02]"
                    {{ $product->id ? '' : 'disabled' }}>
                登録する
            </button>
        </div>
    </form>
</div>
</body>
</html>
