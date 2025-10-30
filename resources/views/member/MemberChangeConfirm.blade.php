<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報変更確認</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center py-10">

    <div class="w-full max-w-2xl bg-white shadow-xl rounded-xl p-8 md:p-12">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-10 border-b-2 pb-4">会員情報変更確認画面</h2>

        <div class="space-y-6">
            <!-- 確認項目表示エリア -->
            <div class="bg-yellow-50 p-6 rounded-lg space-y-4">

                <!-- 氏名 -->
                <div class="flex border-b pb-2">
                    <div class="w-1/3 text-lg font-medium text-gray-700">氏名</div>
                    <div class="w-2/3 text-lg text-gray-900">{{ $data['name_sei'] }} {{ $data['name_mei'] }}</div>
                </div>

                <!-- ニックネーム -->
                <div class="flex border-b pb-2">
                    <div class="w-1/3 text-lg font-medium text-gray-700">ニックネーム</div>
                    <div class="w-2/3 text-lg text-gray-900">{{ $data['nickname'] }}</div>
                </div>

                <!-- 性別 -->
                <div class="flex">
                    <div class="w-1/3 text-lg font-medium text-gray-700">性別</div>
                    <div class="w-2/3 text-lg text-gray-900">
                        {{ $data['gender'] == '1' ? '男性' : '女性' }}
                    </div>
                </div>
            </div>

            <!-- ボタンエリア -->
            <div class="pt-8 space-y-4">
                <!-- 「変更完了」ボタン: DB更新処理 (updateメソッド) に対応するルートに変更 -->
                <!-- ルート名を直接指定: member.edit.update -->
                <form action="{{ url('member/edit/update') }}" method="POST">
                    @csrf
                    <!-- フォームの action を /member/edit/update に修正 -->

                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 ease-in-out">
                        変更完了
                    </button>
                </form>

                <!-- 「前に戻る」ボタン: フォーム画面へリダイレクト -->
                <form action="{{ route('member.edit.form') }}" method="GET">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                        前に戻る
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
