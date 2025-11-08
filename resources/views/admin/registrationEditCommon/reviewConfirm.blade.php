<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family:'Inter','Noto Sans JP',sans-serif; background:#f3f4f6; }
    </style>
</head>
<body class="min-h-screen flex items-start justify-center pt-10 pb-14">
<div class="w-full max-w-3xl bg-white rounded-xl shadow-2xl overflow-hidden">

    <div class="bg-gray-100 p-6 flex items-center justify-between border-b">
        <h1 class="text-2xl font-bold text-gray-800">{{ $pageTitle }}</h1>
        {{-- 前へ戻る（フォームへ） --}}
        @php
            $backRoute = $isEdit ? route('admin.review.edit', $review->id) : route('admin.review.create');
        @endphp
        <a href="{{ $backRoute }}" class="px-4 py-2 text-sm bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">前に戻る</a>
    </div>

    <div class="p-6 space-y-8">

        {{-- 上段：商品情報 + 総合評価 --}}
        <div class="bg-blue-50 border rounded-lg p-5">
            <div class="flex items-start gap-4">
                <div class="w-28 h-28 border rounded-lg bg-white flex items-center justify-center overflow-hidden">
                    @php
                        $img = $product?->image_1 ? asset('storage/'.ltrim($product->image_1,'/')) : null;
                    @endphp
                    @if($img)
                        <img src="{{ $img }}" alt="商品画像" class="object-contain w-full h-full">
                    @else
                        <span class="text-xs text-gray-400">画像なし</span>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="text-sm text-gray-500">商品ID　{{ $product?->id ?? '—' }}</div>
                    <div class="text-sm text-gray-600 mt-1">会員　{{ $member?->name_sei }} {{ $member?->name_mei }}</div>
                    <div class="text-xl font-semibold text-gray-800 mt-1">{{ $product?->name ?? '—' }}</div>

                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-sm text-gray-600">総合評価</span>
                        @php $avg = (int)($ratingAvgCeil ?? 0); @endphp
                        @for($i=1;$i<=5;$i++)
                            <span class="{{ $i <= $avg ? 'text-yellow-400' : 'text-gray-300' }} text-xl">★</span>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- 入力内容の確認 --}}
        <div class="border rounded-lg overflow-hidden">
            <dl class="divide-y">
                <div class="bg-gray-50 px-4 py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm text-gray-500">ID</dt>
                    <dd class="col-span-2 text-sm text-gray-900">
                        {{ $isEdit ? ($review->id) : '登録後に自動採番' }}
                    </dd>
                </div>

                <div class="bg-white px-4 py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm text-gray-500">商品評価</dt>
                    <dd class="col-span-2 text-sm text-gray-900">{{ $input['evaluation'] }}</dd>
                </div>

                <div class="bg-gray-50 px-4 py-3 grid grid-cols-3 gap-4">
                    <dt class="text-sm text-gray-500">商品コメント</dt>
                    <dd class="col-span-2 text-sm text-gray-900 whitespace-pre-wrap">{{ $input['comment'] }}</dd>
                </div>
            </dl>
        </div>

        {{-- 完了ボタン --}}
        <div class="flex items-center justify-center gap-6">

            <a href="{{ $backRoute }}"
               class="px-10 py-3 bg-white border text-gray-800 font-semibold rounded-lg shadow hover:bg-gray-50">
                前に戻る
            </a>

            <form id="complete-form"
                  action="{{ $isEdit ? route('admin.review.update', $review->id) : route('admin.review.store') }}"
                  method="POST">
                @csrf
                @if($isEdit) @method('PUT') @endif
                <button type="submit" id="submit-button"
                        class="px-10 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700">
                    {{ $isEdit ? '編集完了' : '登録完了' }}
                </button>
            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const f = document.getElementById('complete-form');
    const b = document.getElementById('submit-button');
    if (f && b) {
        f.addEventListener('submit', () => {
            b.disabled = true;
            b.textContent = '処理中…';
            b.classList.add('opacity-70','cursor-not-allowed');
        });
    }
});
</script>
</body>
</html>
