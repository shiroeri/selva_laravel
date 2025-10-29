<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退会確認</title>
    <!-- Tailwind CSS (CDN) を読み込み (デザイン用) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 見やすいようにフォントを設定 */
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl">
        
        <!-- ヘッダーボタン: トップに戻る/ログアウト (image_06e47e.png のデザインに合わせる) -->
        <div class="flex justify-end space-x-2 mb-8">
            <a href="{{ route('top') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">トップに戻る</a>
            <!-- ログアウト機能は、通常Auth::logout()を含むPOSTリクエストで行います。ここでは仮のリンクとしています。 -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-pink-500 rounded-lg hover:bg-pink-600 transition">ログアウト</button>
            </form>
        </div>

        <!-- 退会確認セクション -->
        <div class="p-6 rounded-lg text-center">
            <p class="text-xl font-semibold text-gray-700 mb-8">退会します。よろしいですか？</p>
            
            <br>

                <!-- 「マイページに戻る」ボタン -->
                <a href="{{ url('/mypage') }}" class="px-6 py-3 font-semibold text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition duration-150 shadow-md">
                    マイページに戻る
                </a>
                
                <br>
                <br>

                <!-- 「退会する」ボタン（退会処理実行） -->
                <form method="POST" action="{{ route('withdraw') }}">
                    @csrf
                    <button type="submit" class="px-6 py-3 font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-150 shadow-md">
                        退会する
                    </button>
                </form>
            
        </div>

    </div>
</body>
</html>
