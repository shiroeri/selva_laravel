@php
// Controllerから渡されることを想定したダミー/フォールバックデータ
// 実際にはControllerでProductモデルをロードし、評価を計算して渡します。
$product = $product ?? (object)[
    'id' => 1,
    'name' => '【最新】高性能ワイヤレスイヤホン PRO-X',
    'image_1' => 'dummy_path.jpg', // ダミー画像パス
];

// 現時点での商品総合評価 (Controllerから渡されることを想定)
$averageEvaluation = $averageEvaluation ?? 4.2;
$reviewCount = $reviewCount ?? 150;
$oldData = $oldData ?? []; // Controllerから渡されるセッションデータ
@endphp

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>商品レビュー投稿 - {{ $product->name ?? '商品' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* カスタムスタイル: 星の評価表示 */
.star-rating {
    font-size: 1.5rem;
    color: #f59e0b; /* Amber-500 */
}
.star-rating .empty {
    color: #d1d5db; /* Gray-300 */
}
</style>
</head>
<body class="bg-gray-100 min-h-screen p-4 sm:p-8 font-sans">

<div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 shadow-xl rounded-2xl border border-gray-200">
    
    {{-- ヘッダーとトップに戻るボタン --}}
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-extrabold text-gray-900">
                商品レビュー登録
            </h1>
        </div>
        
        {{-- トップに戻るボタン (右上) --}}
        <a href="{{ route('top') }}" class="flex items-center space-x-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition p-2 rounded-lg bg-gray-50 hover:bg-gray-200">
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
            <p class="text-4xl font-extrabold text-indigo-600 mt-1">{{ ceil($averageEvaluation) }}</p>
            <div class="star-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= ceil($averageEvaluation) ? 'filled' : 'empty' }}">★</span>
                @endfor
            </div>
        </div>
    </div>

    {{-- フォーム: actionをconfirmルートに変更 --}}
    <form method="POST" action="{{ $product->id ? route('product.review.confirm', $product) : '#' }}">
        @csrf

        {{-- 評価 (Select Box) --}}
        <div class="mb-6">
            <label class="block text-xl font-bold text-gray-700 mb-2" for="rating">商品評価</label>
            {{-- name属性は 'rating' --}}
            <select id="rating" name="rating"
                    class="mt-1 block w-full sm:w-1/2 border border-gray-300 rounded-lg shadow-sm p-3 text-lg focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                <option value="" disabled selected>-- 選択してください --</option>
                @for ($i = 5; $i >= 1; $i--)
                    {{-- old('rating') または $oldData['rating'] を使用 --}}
                    <option value="{{ $i }}" {{ old('rating', $oldData['rating'] ?? '') == $i ? 'selected' : '' }}>
                        {{ $i }}点
                    </option>
                @endfor
            </select>

            {{-- @error ディレクティブは 'rating' --}}
            @error('rating')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- コメント (Textarea) --}}
        <div class="mb-8">
            {{-- ReviewRequest.phpの変更に合わせて「(必須)」を削除 --}}
            <label class="block text-xl font-bold text-gray-700 mb-2" for="body">商品コメント</label>
            {{-- name属性は 'body' --}}
            <textarea id="body" name="body" rows="6"
                      class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm p-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
                      >{{ old('body', $oldData['body'] ?? '') }}</textarea>

            {{-- @error ディレクティブは 'body' --}}
            @error('body')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- フッターボタン配置 --}}
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            {{-- 商品詳細に戻るボタン --}}
            <a href="{{ $product->id ? route('product.show', $product) : '#' }}" 
               class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-lg font-medium rounded-xl shadow-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300">
                商品詳細に戻る
            </a>
            
            {{-- 登録確認ボタン --}}
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 border border-transparent text-lg font-medium rounded-xl shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 transform hover:scale-[1.02]"
                    {{ $product->id ? '' : 'disabled title="商品情報がないため投稿できません"' }}>
                商品レビュー登録確認
            </button>
        </div>
    </form>
</div>
</body>
</html>
