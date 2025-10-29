<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧・検索</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
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
        .btn-detail { background-color: #f97316; color: white; font-size: 0.85em; padding: 6px 12px; }
        .btn-detail:hover { background-color: #ea580c; }
        .product-image { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; }
        
        /* テーブルスタイル */
        .product-table th, .product-table td { padding: 12px 15px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .product-table th { background-color: #f3f4f6; color: #4b5563; font-weight: 600; font-size: 0.9em; }
        /* カーソルを合わせても詳細ボタンがある列以外はホバー背景色を変えないように調整 */
        .product-table tr:not(.no-result-row):hover { background-color: #f9fafb; }
        .product-table tr a:hover { text-decoration: underline; color: #1d4ed8; } /* 商品名リンクのホバー効果 */

        .no-result { text-align: center; color: #9ca3af; padding: 40px 0; font-size: 1.1em; }
        
        /* フラッシュメッセージ */
        .flash-success { background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0; }

    </style>
</head>
<body>
<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h1>商品一覧</h1>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('product.create')); ?>" class="btn btn-register">新規商品登録</a>
        <?php endif; ?>
    </div>

    
    <?php if(session('success')): ?>
        <div class="flash-success" role="alert">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    
    
    <div class="bg-gray-50 p-6 rounded-lg shadow-inner mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">商品検索</h2>
        <form action="<?php echo e(route('product.list')); ?>" method="GET" id="search-form">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                
                <div class="form-group">
                    <label for="product_category_id">カテゴリ</label>
                    <select id="product_category_id" name="product_category_id" class="input-field">
                        <option value="">カテゴリ</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" 
                                <?php echo e(($search['product_category_id'] ?? '') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                
                <div class="form-group">
                    <label for="product_subcategory_id">　　　　　</label>
                    
                    <select id="product_subcategory_id" name="product_subcategory_id" class="input-field" disabled 
                            data-old-value="<?php echo e($search['product_subcategory_id'] ?? ''); ?>">
                        <option value="">サブカテゴリ</option>
                    </select>
                </div>
                
                
                <div class="form-group">
                    <label for="free_word">フリーワード</label>
                    <input type="text" id="free_word" name="free_word" class="input-field" 
                           placeholder="キーワードを入力" value="<?php echo e($search['free_word'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-4">
                <button type="submit" class="btn btn-search">
                    商品検索
                </button>
                <!-- <a href="<?php echo e(route('product.list')); ?>" class="btn btn-clear">
                    クリア
                </a> -->
            </div>
        </form>
    </div>


    
    
    
    <h2 class="text-xl font-semibold mb-4 text-gray-700">検索結果 (<?php echo e($products->total()); ?>件)</h2>

    <div class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full product-table bg-white">
            <thead>
                <tr>
                    <th class="w-20">写真</th>
                    <th class="w-1/4">カテゴリ</th>
                    <th class="w-1/4">商品名</th>
                    <th class="w-1/4">商品総合評価</th>
                    <th class="w-32"></th> 
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    
                    <?php
                        $rating = $product->reviews_avg_evaluation;
                        // 評価がnull (レビューなし) の場合は 0 とする
                        $displayRating = is_numeric($rating) ? round($rating, 1) : 0;
                    ?>

                    <tr>
                        <td>
                            <?php
                                // DBに保存されている画像パス (image_1) を使用
                                $imagePath = $product->image_1;
                                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
                            ?>
                            
                            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product->name); ?>" class="product-image" 
                                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
                        </td>
                        <td class="text-gray-700">
                            
                            <?php echo e($product->category->name ?? '不明'); ?> 
                            <span class="text-gray-500 text-sm"> > <?php echo e($product->subcategory->name ?? '不明'); ?></span>
                        </td>
                        <td class="text-gray-900 font-medium">
                            
                            <a href="<?php echo e(route('product.show', [$product, 'page' => $products->currentPage()])); ?>" class="text-blue-600 hover:text-blue-800">
                            <?php echo e($product->name); ?>

                            </a>
                        </td>
                        <td>
                            <div class="text-sm">
                                
                                <div class="text-yellow-500 text-2xl" style="letter-spacing: 0.1em;">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        
                                        <?php if($i <= ceil($displayRating)): ?>
                                            ★
                                        <?php else: ?>
                                            <span class="text-gray-300">★</span>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <span class="text-4xl font-extrabold mr-2 <?php echo e($displayRating > 0 ? 'text-indigo-600' : 'text-gray-400'); ?>">
                                <?php echo e(ceil($displayRating)); ?>

                            </span>
                        </td>
                        <td>
                            
                            <a href="<?php echo e(route('product.show', [$product, 'page' => $products->currentPage()])); ?>" class="btn btn-detail">
                                詳細
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr class="no-result-row">
                        <td colspan="5" class="no-result">該当する商品が見つかりませんでした。</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-8">
        
        <?php echo e($products->appends(request()->input())->links()); ?>

    </div>

    
    <a href="<?php echo e(route('top')); ?>">
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
            // route('api.subcategories') は /api/subcategories に解決されます
            const url = `/api/subcategories?category_id=${categoryId}`;

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
<?php /**PATH /home/erika/laravel/resources/views/product/list.blade.php ENDPATH**/ ?>