<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - 確認</title>
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
<body>

<div class="max-w-3xl mx-auto p-6 bg-white shadow-xl rounded-xl mt-10">
    <!-- ヘッダー部分 (会員登録/会員編集) -->
    <div class="flex justify-between items-center border-b pb-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $pageTitle }}
        </h1>
        <a href="{{ route($routePrefix . '.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition duration-150">
            一覧へ戻る
        </a>
    </div>

    <!-- 確認内容テーブル -->
    <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
        <dl>
            <!-- 1. ID -->
            <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    @if ($isEdit)
                        {{ $member->id ?? 'エラー' }}
                    @else
                        登録後に自動採番
                    @endif
                </dd>
            </div>

            <!-- 2. 氏名 -->
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">氏名</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    {{ $data['name_sei'] ?? '' }} {{ $data['name_mei'] ?? '' }}
                </dd>
            </div>

            <!-- 3. ニックネーム -->
            <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">ニックネーム</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    {{ $data['nickname'] ?? '未入力' }}
                </dd>
            </div>

            <!-- 4. 性別 -->
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">性別</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    {{ ($data['gender'] ?? null) == 1 ? '男性' : ( ($data['gender'] ?? null) == 2 ? '女性' : '未選択' ) }}
                </dd>
            </div>

            <!-- 5. パスワード -->
            <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">パスワード</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    @if ($isEdit && empty($data['password']))
                        セキュリティのため非表示
                    @else
                        セキュリティのため非表示
                    @endif
                </dd>
            </div>

            <!-- 6. メールアドレス -->
            <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                    {{ $data['email'] ?? '未入力' }}
                </dd>
            </div>

        </dl>
    </div>

    <!-- 確定ボタンと戻るボタン -->
    <div class="mt-8 flex justify-center space-x-6">
        <!-- 戻るボタン: GETで入力画面へ戻る。コントローラーがセッションからデータを再取得する。 -->
        <form action="{{ route($isEdit ? $routePrefix . '.edit' : $routePrefix . '.create', $member->id ?? null) }}" method="GET">
            <button type="submit"
                    class="px-8 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition duration-150 shadow-sm text-lg">
                前に戻る
            </button>
        </form>

        <!-- 確定ボタン: 登録/更新完了ルートへ送信 -->
        {{-- ★修正点: formにIDを追加 --}}
        <form id="complete-form" action="{{ $isEdit ? route($routePrefix . '.updateComplete', $member->id ?? null) : route($routePrefix . '.complete') }}" method="POST">
            @csrf
            
            {{-- ★重要: 編集時（更新完了）は、PUT/PATCHメソッドを偽装する --}}
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- 完了ルートのコントローラーでセッションからデータを取得するため、ここでは hidden 項目は不要です -->
            {{-- ★修正点: buttonにIDを追加 --}}
            <button type="submit" id="submit-button"
                    class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150 text-lg">
                {{ $isEdit ? '編集完了' : '登録完了' }}
            </button>
        </form>
    </div>
</div>

{{-- ★修正点: 二重送信防止のためのJavaScriptを追加 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('complete-form');
        const submitButton = document.getElementById('submit-button');
        const originalText = submitButton ? submitButton.textContent : '';

        if (form && submitButton) {
            form.addEventListener('submit', function() {
                // フォーム送信直後にボタンを無効化
                submitButton.disabled = true;
                
                // ユーザーに処理中であることをフィードバック
                submitButton.textContent = '処理中...'; 
                
                // スタイルも変更して連打を視覚的に防ぐ
                submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            });
            
            // 戻るボタンなどで戻った際、ページキャッシュによりボタンが無効化されたままになるのを防ぐ
            // (ブラウザによっては必要ないが、安全策として追加)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                    submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            });
        }
    });
</script>

</body>
</html>
