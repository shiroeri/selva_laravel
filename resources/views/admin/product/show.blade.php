<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter','Noto Sans JP',sans-serif; background-color:#f3f4f6; }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

<div class="space-y-6 max-w-5xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">

    <!-- ヘッダー -->
    <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-800">商品詳細</h2>
        <a href="{{ route('admin.product.index') }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
            一覧へ戻る
        </a>
    </div>

    <!-- 詳細情報 -->
    <div class="p-8">
        <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
            <dl>
                <!-- 商品ID -->
                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品ID</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0 font-mono">
                        {{ $product->id }}
                    </dd>
                </div>

                <!-- 会員 -->
                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">会員</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        @if($member)
                                {{ $member->name_sei }} {{ $member->name_mei }}
                        @else
                            ー
                        @endif
                    </dd>
                </div>

                <!-- 商品名 -->
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品名</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $product->name }}
                    </dd>
                </div>

                <!-- カテゴリ -->
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品カテゴリ</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $category->name ?? 'ー' }} ＞ {{ $subcategory->name ?? 'ー' }}
                    </dd>
                </div>

                <!-- 画像（縦並び） -->
                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品写真</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        <div class="flex flex-col gap-6">
                            @foreach (['image_1','image_2','image_3','image_4'] as $idx => $f)
                                @php $url = $imageUrls[$f] ?? null; @endphp
                                <div class="flex flex-col items-start">
                                    <span class="mb-1 text-sm text-gray-600 font-medium">写真{{ $idx+1 }}</span>
                                    <div class="w-48 h-48 border rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                        @if($url)
                                            <img src="{{ $url }}" alt="商品画像{{ $idx+1 }}" class="object-contain w-full h-full">
                                        @else
                                            <span class="text-xs text-gray-400">画像なし</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </dd>
                </div>

                <!-- 商品説明 -->
                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品説明</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0 whitespace-pre-wrap">
                        {{ $product->product_content ?: 'ー' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- 総合評価 -->
        <div class="mt-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">総合評価</h3>

            @php
                $avg   = $ratingAvgCeil ?? 0;   // コントローラで evaluation 平均の切り上げ
                $count = $ratingCount   ?? 0;   // 評価件数
            @endphp

            @if ($count > 0 && $avg > 0)
                <div class="flex items-center space-x-2">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $avg)
                            <span class="text-yellow-400 text-3xl">★</span>
                        @else
                            <span class="text-gray-300 text-3xl">★</span>
                        @endif
                    @endfor
                    <span class="text-gray-700 text-lg font-medium ml-2">{{ $avg }} / 5</span>
                    <span class="text-gray-500 text-sm ml-2">（{{ $count }}件）</span>
                </div>
            @else
                <p class="text-gray-600">レビューがまだありません。</p>
            @endif
        </div>

        <!-- レビュー一覧 -->
        <div class="mt-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">商品レビュー</h3>

            @if ($reviews->count() === 0)
                <div class="p-6 bg-gray-50 border rounded-lg text-gray-600">
                    レビューはありません
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($reviews as $rev)
                        @php
                            $rating = $rev->evaluation ?? null;
                            $stars  = is_null($rating) ? 0 : (int)$rating; // 0〜5想定
                        @endphp
                        <div class="p-4 border rounded-lg bg-white">
                            <!-- 1行目: レビューID / 投稿日時 -->
                            <div class="flex items-center justify-between">
                                <div class="text-sm">
                                    商品レビューID：{{ $rev->id }}
                                </div>
                            </div>

                            <!-- 2行目: レビューワー名 / 評価（星＋数値） -->
                            <div class="mt-2 flex items-start justify-between gap-4 flex-col sm:flex-row">
                                <div>
                                    <a href="{{ $rev->member ? route('admin.member.show', $rev->member->id) : '#' }}"
                                       class="text-blue-600 hover:underline">
                                       {{ $rev->member ? ($rev->member->name_mei) : '会員不明' }}さん
                                    </a>
                                </div>
                                <div class="flex items-center">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $stars)
                                            <span class="text-yellow-400 text-2xl leading-none">★</span>
                                        @else
                                            <span class="text-gray-300 text-2xl leading-none">★</span>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-gray-700 text-sm">{{ is_null($rating) ? '-' : $rating }} / 5</span>
                                </div>
                            </div>

                            <!-- 3行目: コメント -->
                            <div class="mt-2">
                                <div class="text-sm">商品コメント：{{ $rev->comment ?? $rev->content ?? $rev->body ?? '（本文なし）' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ページャ（3ページ分ウィンドウ・左右に1ページずつ） -->
                @php
                    $current = $reviews->currentPage();
                    $last    = $reviews->lastPage();
                    $window  = 3;
                    $start   = max(1, $current - 1);
                    $end     = min($last, $start + $window - 1);
                    if (($end - $start + 1) < $window) $start = max(1, $end - $window + 1);
                @endphp

                <div class="mt-6 flex items-center justify-center gap-2">
                    @if ($reviews->previousPageUrl())
                        <a href="{{ $reviews->previousPageUrl() }}" class="px-3 py-1 border rounded hover:bg-gray-100">前へ</a>
                    @endif
                    @for ($p = $start; $p <= $end; $p++)
                        @if ($p == $current)
                            <span class="px-3 py-1 border rounded bg-blue-600 text-white">{{ $p }}</span>
                        @else
                            <a href="{{ $reviews->url($p) }}" class="px-3 py-1 border rounded hover:bg-gray-100">{{ $p }}</a>
                        @endif
                    @endfor
                    @if ($reviews->nextPageUrl())
                        <a href="{{ $reviews->nextPageUrl() }}" class="px-3 py-1 border rounded hover:bg-gray-100">次へ</a>
                    @endif
                </div>
            @endif
        </div>
        <!-- 操作ボタン -->
        <div class="mt-10 flex justify-center space-x-6">
            <a href="{{ route('admin.product.edit', $product->id) }}"
               class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition duration-150 transform hover:scale-105">
                編集
            </a>

            <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-10 py-3 bg-red-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-red-700 transition duration-150 transform hover:scale-105">
                    削除
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
