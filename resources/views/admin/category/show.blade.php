<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品カテゴリ詳細</title>
    <!-- Tailwind CSS (仮定) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* 適切なフォントを設定 */
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
            background-color: #f3f4f6;
        }
        /* ボタンのホバーエフェクトと影 */
        .btn-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

{{-- ページコンテンツの開始 --}}
<div class="container mx-auto p-4">
    <div class="max-w-xl mx-auto bg-white shadow-xl rounded-xl overflow-hidden transform transition duration-500 hover:shadow-2xl">
        
        {{-- ヘッダー --}}
        <div class="p-5 bg-indigo-500 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-white">商品カテゴリ詳細</h1>
            <a href="{{ route('admin.category.index') }}" class="bg-white text-indigo-600 font-bold py-2 px-4 rounded-full shadow-md hover:bg-indigo-100 transition duration-150 ease-in-out">
                一覧へ戻る
            </a>
        </div>

        {{-- 詳細情報セクション --}}
        <div class="p-6">
            <table class="w-full text-sm border-separate border-spacing-y-2">
                <tbody>
                    {{-- 商品大カテゴリID --}}
                    <tr class="bg-gray-50 rounded-lg">
                        <td class="py-3 px-4 font-semibold text-gray-700 w-1/3 rounded-l-lg border-r border-gray-200">商品大カテゴリID</td>
                        <td class="py-3 px-4 text-gray-900 rounded-r-lg">{{ $category->id }}</td>
                    </tr>
                    {{-- 商品大カテゴリ名 --}}
                    <tr class="bg-gray-50 rounded-lg">
                        <td class="py-3 px-4 font-semibold text-gray-700 rounded-l-lg border-r border-gray-200">商品大カテゴリ</td>
                        <td class="py-3 px-4 text-gray-900 rounded-r-lg">{{ $category->name }}</td>
                    </tr>
                    {{-- 商品小カテゴリリスト --}}
                    <tr class="bg-gray-50 rounded-lg">
                        <td class="py-3 px-4 font-semibold text-gray-700 align-top rounded-l-lg border-r border-gray-200">商品小カテゴリ</td>
                        <td class="py-3 px-4 text-gray-900 rounded-r-lg">
                            {{-- ここを修正して縦並びにする --}}
                            @forelse ($category->subcategories as $subcategory)
                                <div class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded-full mb-1 w-fit">
                                    {{ $subcategory->name }}
                                </div>
                            @empty
                                <span class="text-gray-500 text-sm">（小カテゴリなし）</span>
                            @endforelse
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- ボタン群 --}}
            <div class="mt-10 flex justify-center space-x-6">
                {{-- 編集ボタン --}}
                <a href="{{ route('admin.category.edit', $category->id) }}" class="btn-shadow bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-200 ease-in-out transform hover:scale-105">
                    編集
                </a>
                
                {{-- 削除ボタン（フォームでDELETEリクエストを送信） --}}
                <form id="delete-form-{{ $category->id }}" action="{{ route('admin.category.destroy', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-shadow bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-200 ease-in-out transform hover:scale-105">
                        削除
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- ページコンテンツの終了 --}}

</body>
</html>