<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
    <!-- Tailwind CSS CDN (開発用) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル */
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white p-8 rounded-xl shadow-2xl border border-gray-200">
            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-8">
                管理画面
            </h2>

            <!-- 認証ロジックはコントローラー側で実装が必要です -->
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                @csrf

                <!-- ログインID入力 (email から変更) -->
                <div>
                    <label for="login_id" class="block text-sm font-medium text-gray-700">ログインID</label>
                    <div class="mt-1">
                        <input id="login_id" name="login_id" type="text" autocomplete="username" 
                               value="{{ old('login_id') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm 
                               @error('login_id') border-red-500 @enderror">
                    </div>
                    @error('login_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- パスワード入力 -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm 
                               @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ログインボタン -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        ログイン
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
