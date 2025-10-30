<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード変更</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center py-10">

    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8 md:p-12">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-10 border-b-2 pb-4">パスワード変更</h2>

        <!-- DB更新エラーメッセージ表示エリア -->
        @if ($errors->has('db_error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline">{{ $errors->first('db_error') }}</span>
            </div>
        @endif

        <form action="{{ route('member.password.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 新しいパスワード -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">パスワード</label>
                <input id="password" type="text" name="password" autocomplete="new-password"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('password') border-red-500 @enderror">
                
                <!-- password の必須、文字数、半角英数字のエラーはこちらに表示されます -->
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- パスワード確認 -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">パスワード確認</label>
                <input id="password_confirmation" type="text" name="password_confirmation" autocomplete="new-password"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('password_confirmation') border-red-500 @enderror">
                
                <!-- コントローラ側の設定により、パスワード不一致エラーのみがこちらに表示されます -->
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 「パスワードを変更」ボタン -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out">
                    パスワードを変更
                </button>
            </div>
        </form>

        <!-- 「マイページに戻る」ボタン -->
        <div class="mt-4">
            <!-- 仕様書に従い、/mypageへの遷移はGETで行います -->
            <form action="{{ url('/mypage') }}" method="GET">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    マイページに戻る
                </button>
            </form>
        </div>
    </div>

</body>
</html>
