<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面トップメニュー</title>
    <!-- Tailwind CSS CDN (開発用) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* bodyから背景色を削除し、font-familyのみ指定 */
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body>
    <!-- min-h-screenのdiv全体に水色背景 (bg-blue-50) を適用 -->
    <div class="min-h-screen bg-blue-50">
        
        <!-- 管理画面メインメニュー (ヘッダー部分) -->
        <!-- ヘッダーはグレーのまま (bg-gray-100) -->
        <header class="bg-gray-100 shadow-md border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
                
                <!-- 管理画面タイトル -->
                <h1 class="text-xl font-bold text-gray-800">
                    管理画面メインメニュー
                </h1>

                <!-- ログイン情報とログアウトボタン -->
                <div class="flex items-center space-x-4">
                    <!-- ログイン者の氏名を表示 (コントローラーから渡される $admin を使用) -->
                    <span class="text-gray-700 text-base font-medium">
                        ようこそ<span class="font-semibold text-indigo-600">{{ $admin->name ?? 'ゲスト' }}</span>さん
                    </span>

                    <!-- ログアウトフォーム -->
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="py-1 px-3 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 shadow-sm 
                                       hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- メインコンテンツ領域 -->
        <main class="py-10">
            
            <div class="max-w-md mx-auto px-4">
                <a href="{{ route('admin.member.index') }}"
                   class="block w-full text-center px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-xl shadow-xl hover:bg-blue-700 transition duration-300 transform hover:scale-[1.02] border-b-4 border-blue-800 hover:border-blue-900">
                    会員一覧
                </a>
            </div>

            <br>

            <div class="max-w-md mx-auto px-4">
                <a href="{{ route('admin.category.index') }}"
                   class="block w-full text-center px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-xl shadow-xl hover:bg-blue-700 transition duration-300 transform hover:scale-[1.02] border-b-4 border-blue-800 hover:border-blue-900">
                    商品カテゴリ一覧
                </a>
            </div>
            
        </main>

    </div>
</body>
</html>
