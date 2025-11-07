<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'カテゴリフォーム' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', 'Noto Sans JP', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-start justify-center pt-12 pb-12">

    @php
        // フォームが利用するDBの初期値データ（編集時）
        $initialCategoryData = $category ?? (object)['id' => null, 'category_name' => null, 'subcategories' => []];

        // フォーム表示用データを作成 (old()またはセッションデータ $input を優先)
        $formData = (array)$initialCategoryData; 
        
        // $inputが存在し、かつ配列であればマージする
        if (isset($input) && is_array($input)) {
            // セッションデータ（$input）をDBデータ（$formData）に上書きする（確認画面からの戻りを優先）
            $formData = array_merge($formData, $input);
        }

        // Blade内でオブジェクトとしてアクセスしやすいように再度オブジェクト化（オプション）
        $formDataObject = (object)$formData;
    @endphp

    <div class="space-y-6 max-w-4xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">
        
        <!-- 1. ページヘッダー (グレー背景) -->
        <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
            <h2 class="text-3xl font-extrabold text-gray-800">{{ $pageTitle ?? 'カテゴリフォーム' }}</h2>
            <!-- 戻るボタン（戻るリンクが設定されている場合） -->
            @if(isset($backLink))
                <a href="{{ $backLink }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
                    一覧へ戻る
                </a>
            @endif
        </div>


        <!-- 2. フォーム本体 -->
        <div class="p-8">
            <!-- フォームアクションの設定 -->
            {{-- 編集時はIDを使用して更新確認ルートへ、登録時は確認ルートへ --}}
            <form action="{{ $isEdit ? route($routePrefix . '.updateConfirm', $initialCategoryData->id) : route($routePrefix . '.confirm') }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PUT')
                    {{-- 編集時にIDを渡す --}}
                    <input type="hidden" name="id" value="{{ $initialCategoryData->id }}">
                @endif

                <div class="space-y-8">

                    <!-- 商品大カテゴリID (編集時のみ表示) -->
                    <div class="grid grid-cols-4 gap-4 items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="col-span-1 font-semibold text-gray-700">商品大カテゴリID</div>
                        <div class="col-span-3 text-gray-900">
                            @if ($isEdit)
                                {{ $initialCategoryData->id ?? 'エラー' }}
                            @else
                                登録後に自動採番
                            @endif
                        </div>
                    </div>

                    <!-- 商品大カテゴリ名 -->
                    <div class="grid grid-cols-4 gap-4 items-start">
                        <label for="category_name" class="col-span-1 font-semibold text-gray-700 pt-2">
                            商品大カテゴリ
                        </label>
                        <div class="col-span-3">
                            <input type="text" name="category_name" id="category_name" 
                                   value="{{ old('category_name', $formDataObject->category_name ?? '') }}"
                                   class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('category_name') ? 'border-red-500' : 'border-gray-300' }}">
                            @error('category_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 商品小カテゴリ (10行) -->
                    <div class="grid grid-cols-4 gap-4 items-start border-t pt-8 border-gray-200">
                        <div class="col-span-1 font-semibold text-gray-700 pt-2">商品小カテゴリ</div>
                        <div class="col-span-3 space-y-3">
                            @for ($i = 0; $i < 10; $i++)
                                @php
                                    // $formDataObject->subcategories は配列であると想定
                                    $subcatValue = old('subcategories.' . $i, $formDataObject->subcategories[$i] ?? '');
                                @endphp
                                <input type="text" name="subcategories[]" 
                                       value="{{ $subcatValue }}"
                                       class="w-full p-3 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('subcategories.' . $i) ? 'border-red-500' : 'border-gray-300' }}">
                                @error('subcategories.' . $i)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            @endfor
                        </div>
                    </div>

                </div>

                <div class="mt-10 flex justify-center space-x-4">
                    <button type="submit"
                            class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition duration-150 transform hover:scale-105">
                        確認画面へ
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>