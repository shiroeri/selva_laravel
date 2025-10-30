<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メールアドレス変更</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center py-10">

    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8 md:p-12">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-10 border-b-2 pb-4">メールアドレス変更</h2>

        <!-- DB更新エラーメッセージ表示エリア -->
        @if ($errors->has('db_error') || $errors->has('auth_code') || session('db_error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                @if ($errors->has('db_error'))
                    <span class="block sm:inline">{{ $errors->first('db_error') }}</span>
                @elseif ($errors->has('auth_code'))
                    <!-- 認証コード期限切れエラーなど、特定のエラーはこちらで大きく表示 -->
                    <span class="block sm:inline">{{ $errors->first('auth_code') }}</span>
                @elseif (session('db_error'))
                    <!-- コントローラでフラッシュされたセッションエラーの表示 -->
                    <span class="block sm:inline">{{ session('db_error') }}</span>
                @endif
            </div>
        @endif
        
        <!-- 現在のメールアドレス -->
        <div class="mb-6">
            <p class="block text-sm font-medium text-gray-700 mb-1">現在のメールアドレス</p>
            <p class="text-xl font-semibold text-gray-900 border-b pb-2">{{ $currentEmail ?? '取得できませんでした' }}</p>
        </div>

        <form action="{{ route('member.email.send-auth-code') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 変更後のメールアドレス -->
            <div>
                <label for="new_email" class="block text-sm font-medium text-gray-700 mb-1">変更後のメールアドレス</label>
                <input id="new_email" type="text" name="new_email" value="{{ old('new_email') }}" autocomplete="email"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('new_email') border-red-500 @enderror"
                       placeholder="新しいメールアドレス">
                
                @error('new_email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 「認証メール送信」ボタン -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out">
                    認証メール送信
                </button>
            </div>
        </form>

        <!-- 「マイページに戻る」ボタン -->
        <div class="mt-4">
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
