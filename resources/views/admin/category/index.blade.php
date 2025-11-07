<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品カテゴリ一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
        }
        .header-link {
            cursor: pointer;
            transition: color 0.15s;
        }
        .header-link:hover {
            color: #3b82f6; /* Blue 500 */
        }
        .arrow-icon {
            margin-left: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            display: inline-block;
        }
        /* ソート不可のヘッダーはカーソルをデフォルトに戻す */
        .header-static {
            cursor: default;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">商品カテゴリ一覧</h1>
            <a href="{{ route('admin.top') }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
                トップへ戻る
            </a>
        </div>

        <!-- 商品カテゴリ登録ボタン -->
        <div class="p-6 pb-0">
            <a href="{{ route('admin.category.create') }}" class="inline-block px-6 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition duration-150">
                商品カテゴリ登録
            </a>
        </div>

        <br>
        
        <!-- 検索フォーム -->
        <div class="bg-white shadow-xl rounded-xl p-6 mb-8">
            <form action="{{ route('admin.category.index') }}" method="GET">
                <div class="space-y-6"> 
                    
                    <!-- ID検索 -->
                    <div>
                        <label for="id" class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <input type="text" name="id" id="id" value="{{ $searchParams['id'] ?? '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>

                    <!-- フリーワード検索 -->
                    <div>
                        <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">フリーワード</label>
                        <input type="text" name="keyword" id="keyword" value="{{ $searchParams['keyword'] ?? '' }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5">
                    </div>

                    <!-- 検索ボタン -->
                    <div class="pt-2"> 
                        <button type="submit"
                                class="w-full px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 transform hover:scale-[1.01]">
                            <i class="fas fa-search mr-2"></i> 検索する
                        </button>
                    </div>

                </div>
                
                <!-- 並べ替え条件を隠しフィールドとして含める -->
                <input type="hidden" name="sort_column" value="{{ $sortColumn }}">
                <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
            </form>
            
        </div>

        <!-- カテゴリ一覧テーブル -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <!-- IDヘッダー: ソート可能 (初期降順) -->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                @php
                                    $newDirection = ($sortColumn == 'id' && $sortDirection == 'desc') ? 'asc' : 'desc'; 
                                @endphp
                                <a href="{{ route('admin.category.index', array_merge($searchParams, ['sort_column' => 'id', 'sort_direction' => $newDirection, 'page' => 1])) }}"
                                   class="header-link flex items-center">
                                    ID
                                    @if($sortColumn == 'id')
                                        <span class="arrow-icon">
                                            @if($sortDirection == 'asc') <i class="fas fa-sort-up"></i> @else <i class="fas fa-sort-down"></i> @endif
                                        </span>
                                    @else
                                        <span class="arrow-icon text-gray-400"><i class="fas fa-sort"></i></span>
                                    @endif
                                </a>
                            </th>
                            
                            <!-- 商品大カテゴリ名ヘッダー: 静的 (ソート機能は削除) -->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/6 header-static">
                                商品大カテゴリ名
                            </th>
                            
                            <!-- 登録日時ヘッダー: ソート可能 -->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                @php
                                    $newDirection = ($sortColumn == 'created_at' && $sortDirection == 'desc') ? 'asc' : 'desc'; 
                                @endphp
                                <a href="{{ route('admin.category.index', array_merge($searchParams, ['sort_column' => 'created_at', 'sort_direction' => $newDirection, 'page' => 1])) }}"
                                   class="header-link flex items-center">
                                    登録日時
                                    @if($sortColumn == 'created_at')
                                        <span class="arrow-icon">
                                            @if($sortDirection == 'asc') <i class="fas fa-sort-up"></i> @else <i class="fas fa-sort-down"></i> @endif
                                        </span>
                                    @else
                                        <span class="arrow-icon text-gray-400"><i class="fas fa-sort"></i></span>
                                    @endif
                                </a>
                            </th>
                            
                            <!-- 編集ヘッダー -->
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                編集
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                詳細
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- $categories (ProductCategory) をループ -->
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $category->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"> 
                                    <a href="{{ route('admin.category.show', $category->id) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <!-- 日付のみを Y/m/d 形式で表示 -->
                                    {{ $category->created_at->format('Y/m/d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <!-- ★修正: 'admin.category.edit' ルートを使用し、IDを渡す -->
                                    <a href="{{ route('admin.category.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">
                                        編集
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('admin.category.show', $category->id) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-base">
                                    該当する商品カテゴリは見つかりませんでした。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- ページネーションリンク -->
            <div class="p-6 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
    
</body>
</html>
