<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧・検索</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル */
        body { font-family: 'Inter', Arial, sans-serif;}
        .container { max-width: 1000px; margin: 30px auto; background-color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); }
        h1 { color: #1f2937; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; margin-bottom: 30px; font-size: 2em; font-weight: 700; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; color: #374151; }
        .input-field, select {
            width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; 
        }
        .btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 6px; font-weight: 600; transition: background-color 0.2s, transform 0.1s; }
        .btn-search { background-color: #3b82f6; color: white; }
        .btn-search:hover { background-color: #2563eb; }
        .btn-clear { background-color: #6b7280; color: white; }
        .btn-clear:hover { background-color: #4b5563; }
        .btn-register { background-color: #10b981; color: white; }
        .btn-register:hover { background-color: #059669; }
        .product-image { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; }
        
        /* テーブルスタイル */
        .product-table th, .product-table td { padding: 12px 15px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .product-table th { background-color: #f3f4f6; color: #4b5563; font-weight: 600; font-size: 0.9em; }
        .product-table tr:hover { background-color: #f9fafb; }
        .no-result { text-align: center; color: #9ca3af; padding: 40px 0; font-size: 1.1em; }
        
        /* フラッシュメッセージ */
        .flash-success { background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0; }

    </style>
</head>
<body>
<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h1>商品一覧</h1>
        @auth
        <a href="{{ route('product.create') }}" class="btn btn-register">新規商品登録</a>
        @endauth
    </div>

    {{-- 登録完了メッセージ --}}
    @if (session('success'))
        <div class="flash-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- ------------------------------------------------ --}}
    {{-- 1. 検索フォーム --}}
    {{-- ------------------------------------------------ --}}
    <div class="bg-gray-50 p-6 rounded-lg shadow-inner mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">商品検索</h2>
        <form action="{{ route('product.list') }}" method="GET" id="search-form">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- 大カテゴリ --}}
                <div class="form-group">
                    <label for="product_category_id">カテゴリ</label>
                    <select id="product_category_id" name="product_category_id" class="input-field">
                        <option value="">カテゴリ</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ ($search['product_category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- 小カテゴリ (JavaScriptで動的に更新) --}}
                <div class="form-group">
                    <label for="product_subcategory_id">　　　　　</label>
                    {{-- 検索条件を保持し、JSで初期ロード時に設定するため data-old-value を使用 --}}
                    <select id="product_subcategory_id" name="product_subcategory_id" class="input-field" disabled 
                            data-old-value="{{ $search['product_subcategory_id'] ?? '' }}">
                        <option value="">サブカテゴリ</option>
                    </select>
                </div>
                
                {{-- フリーワード --}}
                <div class="form-group">
                    <label for="free_word">フリーワード</label>
                    <input type="text" id="free_word" name="free_word" class="input-field" 
                           placeholder="キーワードを入力" value="{{ $search['free_word'] ?? '' }}">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-4">
                <button type="submit" class="btn btn-search">
                    商品検索
                </button>
                <!-- <a href="{{ route('product.list') }}" class="btn btn-clear">
                    クリア
                </a> -->
            </div>
        </form>
    </div>


    {{-- ------------------------------------------------ --}}
    {{-- 2. 商品一覧表示 --}}
    {{-- ------------------------------------------------ --}}
    <h2 class="text-xl font-semibold mb-4 text-gray-700">検索結果 ({{ $products->total() }}件)</h2>

    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full product-table bg-white">
            <thead>
                <tr>
                    <th class="w-20">写真</th>
                    <th class="w-1/4">カテゴリ</th>
                    <th class="w-1/4">商品名</th>
                    <!-- <th class="w-1/4">登録日</th> -->
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @php
                                // DBに保存されている画像パス (image_1) を使用
                                $imagePath = $product->image_1;
                                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-image" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
                        </td>
                        <td class="text-gray-700">
                            {{-- category()リレーションとsubcategory()リレーションを使用 --}}
                            {{ $product->category->name ?? '不明' }} 
                            <span class="text-gray-500 text-sm"> > {{ $product->subcategory->name ?? '不明' }}</span>
                        </td>
                        <td class="text-gray-900 font-medium">{{ $product->name }}</td>
                        <!-- <td class="text-gray-500 text-sm">
                            {{ $product->created_at->format('Y/m/d H:i') }}
                        </td> -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="no-result">該当する商品が見つかりませんでした。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 3. ページネーションリンク --}}
    <div class="mt-8">
        {{-- ★修正箇所: カスタムビューの指定を削除し、links() のみを使用します。★ --}}
        {{ $products->links() }}
    </div>

    {{-- トップに戻るリンク --}}
    <a href="{{ route('top') }}">
        <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
    </a>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('product_category_id');
        const subcategorySelect = document.getElementById('product_subcategory_id');
        const oldSubcategoryId = subcategorySelect.getAttribute('data-old-value');

        // Ajaxによる小カテゴリ連動処理
        function updateSubcategories(categoryId, isInitialLoad = false) {
            
            subcategorySelect.innerHTML = '<option value="">' + (categoryId ? '読み込み中...' : '大カテゴリを選択してください') + '</option>';
            subcategorySelect.disabled = true;

            if (!categoryId) {
                // 大カテゴリが選択されていない場合は「全て」に戻す
                subcategorySelect.innerHTML = '<option value="">全て</option>';
                subcategorySelect.disabled = false; // 有効化
                return;
            }
            
            // APIルートのURLを構築 (このルートが存在するものとしています)
            // 例: {{ route('api.subcategories') }}
            // 環境に依存するため、ここではハードコードを避けています
            const url = `/api/subcategories?category_id=${categoryId}`; // 仮のパス

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(subcategories => {
                    subcategorySelect.innerHTML = '<option value="">全て</option>';
                    
                    subcategories.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        
                        // 初回ロード時かつ古いサブカテゴリIDが存在する場合、選択状態を復元
                        if (isInitialLoad && oldSubcategoryId && subcategory.id == oldSubcategoryId) {
                            option.selected = true;
                        }

                        subcategorySelect.appendChild(option);
                    });
                    
                    subcategorySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Failed to fetch subcategories:', error);
                    subcategorySelect.innerHTML = '<option value="">読み込みエラー</option>';
                    subcategorySelect.disabled = true;
                });
        }

        // 大カテゴリの変更時イベント
        categorySelect.addEventListener('change', function () {
            // 大カテゴリが変更されたら小カテゴリの選択状態をリセット
            // data-old-valueの値は変更しない（初期ロード時の復元用であるため）
            subcategorySelect.value = ''; 
            updateSubcategories(this.value, false);
        });

        // ページロード時の初期カテゴリ設定
        if (categorySelect.value) {
            updateSubcategories(categorySelect.value, true);
        } else {
            // 大カテゴリが初期状態で「全て」の場合、小カテゴリも「全て」にする
            subcategorySelect.innerHTML = '<option value="">全て</option>';
            subcategorySelect.disabled = false;
        }
    });
</script>
</body>
</html>
