<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>認証コード入力</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center py-10">

    <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8 md:p-12">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-10 border-b-2 pb-4">メールアドレス変更 認証コード入力</h2>

        <!-- 成功メッセージの表示 (認証コード送信成功時) -->
        <!-- @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif -->

        <!-- エラーメッセージ表示エリア -->
        <!-- @if ($errors->has('db_error') || $errors->has('auth_code'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                <span class="block sm:inline">{{ $errors->first('db_error') ?: $errors->first('auth_code') }}</span>
            </div>
        @endif -->

        <p class="text-gray-600 mb-6">
            （※メールアドレスの変更はまだ完了していません。）<br>
            変更後のメールアドレスにお送りしましたメールに記載されている「認証コード」を入力してください。
        </p>

        <form action="{{ route('member.email.verify-code') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 認証コード入力欄 -->
            <div>
                <label for="auth_code" class="block text-sm font-medium text-gray-700 mb-1">認証コード</label>
                <input id="auth_code" type="number" name="auth_code" value="{{ old('auth_code') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('auth_code') border-red-500 @enderror">
                
                @error('auth_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 「認証コードを送信してメールアドレスの変更を完了する」ボタン -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out text-center">
                    認証コードを送信してメールアドレスの変更を完了する
                </button>
            </div>
        </form>

        <!-- 「マイページに戻る」ボタン -->
        <!-- <div class="mt-4">
            <form action="{{ url('/mypage') }}" method="GET">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                    マイページに戻る
                </button>
            </form>
        </div> -->
    </div>

</body>
</html>
