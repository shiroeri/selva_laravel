<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品レビュー詳細</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter','Noto Sans JP',sans-serif; background-color:#f3f4f6; }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

<div class="space-y-6 max-w-3xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">

    <!-- ヘッダー -->
    <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-800">商品レビュー詳細</h2>
        <a href="{{ route('admin.review.index') }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
            一覧へ戻る
        </a>
    </div>

    <div class="p-8 space-y-8">

        <!-- 上部：商品情報＋総合評価 -->
        <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
            <div class="flex items-start gap-6">
                <div class="w-40 h-40 border rounded-lg bg-white flex items-center justify-center overflow-hidden">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="商品画像" class="object-contain w-full h-full">
                    @else
                        <span class="text-xs text-gray-400">画像なし</span>
                    @endif
                </div>
                <div class="flex-1 space-y-2">
                    <div class="text-sm text-gray-500">商品ID {{ $product->id ?? '-' }}</div>
                    <div class="text-lg font-bold text-gray-900">{{ $product->name ?? '商品不明' }}</div>
                    <div class="mt-2">
                        <span class="text-sm text-gray-500">総合評価</span>
                        <div class="inline-flex items-center ml-2">
                            @php $avg = (int)($ratingAvgCeil ?? 0); @endphp
                            @for($i=1;$i<=5;$i++)
                                @if($i <= $avg)
                                    <span class="text-yellow-400 text-2xl leading-none">★</span>
                                @else
                                    <span class="text-gray-300 text-2xl leading-none">★</span>
                                @endif
                            @endfor
                            <span class="ml-2 text-gray-700">{{ $avg }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 区切り線 -->
        <hr class="border-gray-200">

        <!-- レビュー詳細 -->
        <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
            <dl>
                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0 font-mono">{{ $review->id }}</dd>
                </div>

                <!-- <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">会員</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $member ? ($member->name_sei.' '.$member->name_mei) : '会員不明' }}
                    </dd>
                </div> -->

                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品評価</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                        <span class="inline-flex items-center">
                            <span class="ml-2">{{ $review->evaluation }}</span>
                        </span>
                    </dd>
                </div>

                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-base font-medium text-gray-500">商品コメント</dt>
                    <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0 whitespace-pre-wrap">{{ $review->comment }}</dd>
                </div>
            </dl>
        </div>

        <!-- 操作ボタン -->
        <div class="mt-6 flex justify-center gap-6">
            <a href="{{ route('admin.review.edit', $review->id) }}"
               class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition">
                編集
            </a>

            <form action="{{ route('admin.review.destroy', $review->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-10 py-3 bg-red-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-red-700 transition">
                    削除
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
