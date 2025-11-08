<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - 確認</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter','Noto Sans JP',sans-serif; background-color:#f3f4f6; }
        .confirm-dt { min-width:140px; }
        .photo-preview { width:96px; height:96px; border-radius:8px; object-fit:cover; border:2px solid #d1d5db; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    </style>
</head>
<body>
@php
    $product     = $product ?? null;
    $input       = $input ?? [];
    $routePrefix = $routePrefix ?? 'admin.product';
    $isEdit      = $isEdit ?? false;

    // IDの安全な取得
    $productId = optional($product)->id ?: ($input['id'] ?? null);

    // 戻る先
    $backRouteName   = $routePrefix . '.create';
    $backRouteParams = [];
    if ($isEdit && $productId) {
        $backRouteName   = $routePrefix . '.edit';
        $backRouteParams = ['product' => $productId];
    }

    // 完了先
    $completeRouteName   = $isEdit ? $routePrefix . '.update' : $routePrefix . '.store';
    $completeRouteParams = $productId ? ['product' => $productId] : [];
@endphp

<div class="max-w-4xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <div class="flex justify-between items-center border-b pb-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $pageTitle }}</h1>
        <a href="{{ route($routePrefix . '.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-150">
            一覧へ戻る
        </a>
    </div>

    @if ($isEdit && !$productId)
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <p class="font-bold">警告: 商品IDが取得できませんでした。</p>
            <p class="text-sm">コントローラー側で `$product` または `$input['id']` をビューに渡しているか確認してください。</p>
        </div>
    @endif

    <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
        <dl>
            {{-- 商品ID --}}
            <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 confirm-dt">商品ID</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0">
                    @if ($isEdit) {{ $productId ?? 'エラー: IDなし' }} @else 登録後に自動採番 @endif
                </dd>
            </div>

            {{-- 会員 --}}
            <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 confirm-dt">会員</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0">
                    {{ $input['member_name'] ?? '未選択' }}
                </dd>
            </div>

            {{-- 商品名 --}}
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 confirm-dt">商品名</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0">
                    {{ $input['name'] ?? '未入力' }}
                </dd>
            </div>

            {{-- 商品カテゴリ --}}
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 confirm-dt">商品カテゴリ</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0">
                    {!! e($input['category_name'] ?? '未選択') . ' &nbsp;&gt;&nbsp; ' . e($input['subcategory_name'] ?? '未選択') !!}
                </dd>
            </div>

            {{-- 商品写真 1〜4 --}}
            @for ($i = 1; $i <= 4; $i++)
                @php
                    $photoKeyBase     = "image_{$i}";
                    $photoKeyFilename = $photoKeyBase . "_filename";
                    $photoKeyUrl      = $photoKeyBase . "_url";
                    $photoKeyDataUrl  = $photoKeyBase . "_data_url";

                    $photoDisplay     = $input[$photoKeyFilename] ?? 'なし';
                    $photoPreviewSrc  = $input[$photoKeyDataUrl] ?? $input[$photoKeyUrl] ?? null;

                    $isNewUpload = str_contains($photoDisplay, '(新規アップロード)');
                    $isCleared   = str_contains($photoDisplay, '削除（');
                @endphp
                <div class="px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <dt class="text-sm font-medium text-gray-500 confirm-dt">商品写真</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0 flex items-start space-x-6">
                        <p class="text-gray-900">写真 {{ $i }}</p>
                        @if ($photoPreviewSrc && !$isCleared)
                            <div class="flex-shrink-0">
                                <img src="{{ $photoPreviewSrc }}" alt="商品写真{{ $i }} プレビュー"
                                     class="photo-preview"
                                     onerror="this.parentElement.innerHTML = '<span class=\'text-red-500 text-xs\'>画像読み込みエラー</span>';">
                            </div>
                        @else
                            <div class="flex-shrink-0 w-24 h-24 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-300">
                                <span class="text-xs text-gray-400">{{ $isCleared ? '削除' : 'なし' }}</span>
                            </div>
                        @endif
                    </dd>
                </div>
            @endfor

            {{-- 商品説明 --}}
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500 confirm-dt">商品説明</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-3 sm:mt-0 whitespace-pre-wrap">
                    {{ $input['product_content'] ?? '未入力' }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="mt-10 flex justify-center space-x-8">
        <a href="{{ route($backRouteName, $backRouteParams) }}"
           id="back-button"
           class="px-10 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition duration-150 shadow-sm text-lg flex items-center justify-center">
            前に戻る
        </a>

        <form id="complete-form" action="{{ route($completeRouteName, $completeRouteParams) }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
                <input type="hidden" name="id" value="{{ $productId }}">
            @endif
            <button type="submit" id="submit-button"
                    class="px-10 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 text-lg">
                {{ $isEdit ? '編集完了' : '登録完了' }}
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const completeForm = document.getElementById('complete-form');
    const submitButton = document.getElementById('submit-button');
    const backButton   = document.getElementById('back-button');
    const originalText = submitButton ? submitButton.textContent : '';

    if (completeForm && submitButton) {
        completeForm.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.textContent = '処理中...';
            submitButton.classList.remove('bg-blue-600','hover:bg-blue-700');
            submitButton.classList.add('bg-gray-400','cursor-not-allowed');
            if (backButton) backButton.classList.add('pointer-events-none','opacity-50');
        });

        // 履歴キャッシュで戻ってきた場合の再活性化
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                submitButton.classList.remove('bg-gray-400','cursor-not-allowed');
                submitButton.classList.add('bg-blue-600','hover:bg-blue-700');
                if (backButton) backButton.classList.remove('pointer-events-none','opacity-50');
            }
        });
    }
});
</script>
</body>
</html>
