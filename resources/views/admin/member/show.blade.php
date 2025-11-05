<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員詳細</title>
    <!-- Tailwind CSS (仮定) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 適切なフォントを設定 */
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

    <div class="space-y-6 max-w-3xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">
        
        <!-- 1. ページヘッダー (グレー背景) -->
        <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-800">会員詳細</h2>
            <a href="{{ route('admin.member.index') }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
                一覧へ戻る
            </a>
        </div>

        <!-- 2. 詳細情報 -->
        <div class="p-8">
            <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
                <dl>
                    <!-- ID -->
                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">ID</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0 font-mono">
                            {{ $member->id }}
                        </dd>
                    </div>

                    <!-- 氏名 -->
                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">氏名</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $member->name_sei }} {{ $member->name_mei }}
                        </dd>
                    </div>

                    <!-- ニックネーム -->
                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">ニックネーム</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $member->nickname }}
                        </dd>
                    </div>

                    <!-- 性別 (★★★ 修正点 ★★★) -->
                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">性別</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                            {{-- Memberモデルの getGenderNameAttribute() アクセサを利用 --}}
                            {{ $member->gender_name }}
                        </dd>
                    </div>

                    <!-- パスワード -->
                    <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">パスワード</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                            セキュリティのため非表示
                        </dd>
                    </div>

                    <!-- メールアドレス -->
                    <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-base font-medium text-gray-500">メールアドレス</dt>
                        <dd class="mt-1 text-base text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $member->email }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- 3. 操作ボタン (編集・削除) -->
            <div class="mt-10 flex justify-center space-x-6">
                
                <!-- 編集ボタン -->
                <a href="{{ route('admin.member.edit', $member->id) }}"
                   class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition duration-150 transform hover:scale-105">
                    編集
                </a>

                <!-- 削除ボタン (フォームとして実装) -->
                <form action="{{ route('admin.member.destroy', $member->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-10 py-3 bg-red-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-red-700 transition duration-150 transform hover:scale-105">
                        削除
                    </button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>
