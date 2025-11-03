<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員一覧（管理者）</title>
    <!-- Tailwind CSS CDN --><script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル: 現在のページ番号のデザイン */
        .pagination-active {
            font-weight: bold;
            background-color: #3b82f6; /* Blue 500 */
            color: white;
        }
        .table-header-link {
            display: flex;
            align-items: center;
            white-space: nowrap;
        }
        /* ▲と▼記号のスタイル */
        .sort-icon {
            margin-left: 0.25rem; /* ml-1 */
            font-size: 0.75rem; /* text-xs */
            line-height: 1; /* 行の高さを調整 */
        }
        .sort-icon.active {
            color: white; /* アクティブなソートアイコンの色 */
        }
        .sort-icon.inactive {
            color: rgba(255, 255, 255, 0.5); /* ソートされていないアイコンの色 */
        }
    </style>
</head>
<body class="bg-gray-100 p-8 font-sans">

<div class="max-w-6xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">会員一覧</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.top') }}" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-600 transition duration-150">
                トップへ戻る
            </a>
        </div>
    </div>

    <!-- 検索フォーム --><form action="{{ route('admin.member.index') }}" method="GET" class="p-6 space-y-4">
        <div class="grid grid-cols-1 gap-6 border p-4 rounded-lg bg-gray-50">
            <!-- ID検索 --><div>
                <label for="id" class="block text-sm font-medium text-gray-700">ID</label>
                <input type="text" name="id" id="id" value="{{ $searchParams['id'] ?? '' }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2">
            </div>

            <!-- 性別検索 (チェックボックス) --><div>
                <label class="block text-sm font-medium text-gray-700 mb-2">性別</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="gender[]" value="male"
                               {{ in_array('male', $searchParams['gender'] ?? []) ? 'checked' : '' }}
                               class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-900">男性</span>
                    </label>
                    <label class="flex items-center space-x-1">
                        <input type="checkbox" name="gender[]" value="female"
                               {{ in_array('female', $searchParams['gender'] ?? []) ? 'checked' : '' }}
                               class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-900">女性</span>
                    </label>
                </div>
            </div>

            <!-- フリーワード検索 --><div>
                <label for="freeword" class="block text-sm font-medium text-gray-700">フリーワード</label>
                <input type="text" name="freeword" id="freeword" value="{{ $searchParams['freeword'] ?? '' }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2">
            </div>
        </div>

        <!-- 隠しフィールド: 並べ替えの状態を維持 --><input type="hidden" name="sort_column" value="{{ $sortColumn }}">
        <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">

        <div class="text-center pt-4">
            <button type="submit" class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition duration-150">
                検索する
            </button>
        </div>
    </form>

    <!-- 会員一覧テーブル --><div class="p-6">
        <div class="overflow-x-auto rounded-lg border">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <!-- 1. ID 並べ替えリンク --><th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            @php
                                $newDirection = ($sortColumn == 'id' && $sortDirection == 'desc') ? 'asc' : 'desc'; // 現在降順なら次は昇順
                            @endphp
                            <a href="{{ route('admin.member.index', array_merge($searchParams, ['sort_column' => 'id', 'sort_direction' => $newDirection, 'page' => 1])) }}"
                               class="table-header-link">
                                ID
                                @if($sortColumn == 'id')
                                    <span class="sort-icon active">
                                        @if($sortDirection == 'asc') ▲ @else ▼ @endif
                                    </span>
                                @else
                                    <span class="sort-icon inactive">▲▼</span>
                                @endif
                            </a>
                        </th>
                        <!-- 氏名 --><th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">氏名</th>
                        <!-- メールアドレス --><th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">メールアドレス</th>
                        <!-- 性別 --><th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">性別</th>
                        <!-- 登録日時 並べ替えリンク --><th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            @php
                                $newDirection = ($sortColumn == 'created_at' && $sortDirection == 'desc') ? 'asc' : 'desc'; // 現在降順なら次は昇順
                            @endphp
                            <a href="{{ route('admin.member.index', array_merge($searchParams, ['sort_column' => 'created_at', 'sort_direction' => $newDirection, 'page' => 1])) }}"
                               class="table-header-link">
                                登録日時
                                @if($sortColumn == 'created_at')
                                    <span class="sort-icon active">
                                        @if($sortDirection == 'asc') ▲ @else ▼ @endif
                                    </span>
                                @else
                                    <span class="sort-icon inactive">▲▼</span>
                                @endif
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $member->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->gender_name }}</td>
                        <!-- 修正箇所: 時間表示を削除し、日付のみにする -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">該当する会員情報はありませんでした。</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ページネーション --><div class="p-6 border-t border-gray-200">
        {{ $members->links() }}
    </div>

</div>

</body>
</html>
