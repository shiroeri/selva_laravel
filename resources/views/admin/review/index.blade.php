<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品レビュー一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter','Noto Sans JP',sans-serif; background-color:#f3f4f6; }
        .header-link { cursor:pointer; transition: color .15s; }
        .header-link:hover { color:#3b82f6; }
        .arrow-icon { margin-left:.25rem; width:.75rem; height:.75rem; display:inline-block; }
        .header-static { cursor: default; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">商品レビュー一覧</h1>
        <a href="{{ route('admin.top') }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
            トップへ戻る
        </a>
    </div>

    {{-- 検索フォーム（縦並び） --}}
    <div class="bg-white shadow-xl rounded-xl p-6 mb-8">
        <form action="{{ route('admin.review.index') }}" method="GET" class="space-y-6">
            {{-- ID（縦並び1行） --}}
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                <input type="text" id="id" name="id" value="{{ $searchParams['id'] ?? '' }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
            </div>

            {{-- フリーワード（縦並び1行） --}}
            <div>
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">フリーワード</label>
                <input type="text" id="keyword" name="keyword" value="{{ $searchParams['keyword'] ?? '' }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
            </div>

            {{-- 検索ボタン --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                    <i class="fas fa-search mr-2"></i> 検索する
                </button>
            </div>

            {{-- 並べ替え条件は維持 --}}
            <input type="hidden" name="sort_column" value="{{ $sortColumn }}">
            <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
        </form>
    </div>

    {{-- 一覧テーブル --}}
    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    {{-- レビューID（ソート可） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @php $dir = ($sortColumn==='id' && $sortDirection==='desc') ? 'asc' : 'desc'; @endphp
                        <a href="{{ route('admin.review.index', array_merge($searchParams, ['sort_column'=>'id','sort_direction'=>$dir,'page'=>1])) }}"
                           class="header-link flex items-center">
                            ID
                            @if($sortColumn==='id')
                                <span class="arrow-icon">@if($sortDirection==='asc')<i class="fas fa-sort-up"></i>@else<i class="fas fa-sort-down"></i>@endif</span>
                            @else
                                <span class="arrow-icon text-gray-400"><i class="fas fa-sort"></i></span>
                            @endif
                        </a>
                    </th>

                    {{-- 商品ID（表示のみ） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider header-static">商品ID</th>

                    {{-- 評価（表示のみ） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider header-static">評価</th>

                    {{-- コメント（表示のみ） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider header-static">商品コメント</th>

                    {{-- 登録日時（ソート可） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        @php $dir = ($sortColumn==='created_at' && $sortDirection==='desc') ? 'asc' : 'desc'; @endphp
                        <a href="{{ route('admin.review.index', array_merge($searchParams, ['sort_column'=>'created_at','sort_direction'=>$dir,'page'=>1])) }}"
                           class="header-link flex items-center">
                            登録日時
                            @if($sortColumn==='created_at')
                                <span class="arrow-icon">@if($sortDirection==='asc')<i class="fas fa-sort-up"></i>@else<i class="fas fa-sort-down"></i>@endif</span>
                            @else
                                <span class="arrow-icon text-gray-400"><i class="fas fa-sort"></i></span>
                            @endif
                        </a>
                    </th>

                    {{-- 編集リンク（表示のみ） --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider header-static">編集</th>
                </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $rev)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $rev->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $rev->product_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            @php $score = (int)($rev->evaluation ?? 0); @endphp
                            <span class="inline-flex items-center">
                                <span class="ml-2 text-gray-700">{{ $rev->evaluation ?? '-' }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 break-all">{{ $rev->comment }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($rev->created_at)->format('Y/m/d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">編集</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-base">
                            該当する商品レビューは見つかりませんでした。
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ページャ：3ページ分だけ＋前へ/次へ --}}
        @php
            $current = $reviews->currentPage();
            $last    = $reviews->lastPage();
            $window  = 3;

            $start = max(1, $current - 1);
            $end   = min($last, $start + $window - 1);
            if (($end - $start + 1) < $window) {
                $start = max(1, $end - $window + 1);
            }
        @endphp

        <div class="p-6 border-t border-gray-200">
            <div class="flex justify-center items-center gap-2">
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
        </div>
    </div>
</div>

</body>
</html>
