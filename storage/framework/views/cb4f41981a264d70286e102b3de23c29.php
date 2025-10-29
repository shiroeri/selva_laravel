<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細: <?php echo e($product->name); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Interフォントと基本的な背景色を設定 */
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            padding: 30px 0;
        }
        /* コンテナのスタイル設定 */
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            background-color: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); 
            /* ★修正点1: 内部のabsolute要素の基準にする */
            position: relative; 
        }
        /* タイトルのスタイル */
        h1 { 
            color: #1f2937; 
            font-size: 2.2em; 
            font-weight: 700;
            /* ★修正点2: ボタンと重ならないようにマージンを調整 */
            margin-bottom: 25px;
            /* 以前のヘッダー要素のスペースを維持しつつ、ボタン配置のために調整 */
            padding-bottom: 10px; 
        }
        /* 情報行のラベルとコンテンツのスタイル */
        .info-label {
            font-weight: 600;
            color: #4b5563;
            width: 120px;
            flex-shrink: 0;
        }
        .info-content {
            color: #1f2937;
            font-weight: 400;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        /* 商品説明ボックスのスタイル */
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
            white-space: pre-wrap; /* 改行を保持 */
            min-height: 100px;
        }
        /* ボタン群のスタイル */
        .btn-group {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
        }
        .btn { 
            padding: 12px 25px; 
            border: none; 
            cursor: pointer; 
            border-radius: 8px; 
            transition: background-color 0.3s, transform 0.1s; 
            font-weight: 600; 
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-back { 
            background-color: #6366f1; /* Indigo */
            color: white; 
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
        }
        .btn-back:hover { 
            background-color: #4f46e5;
            transform: translateY(-1px);
        }
        .btn-top { 
            background-color: #10b981; /* Tealに変更 */
            color: white;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }
        .btn-top:hover { 
            background-color: #059669;
            transform: translateY(-1px);
        }
        /* ★追加★ レビューボタンのスタイル */
        .btn-review {
            background-color: #ec4899; /* Pink */
            color: white;
            box-shadow: 0 4px 6px rgba(236, 72, 153, 0.3);
        }
        .btn-review:hover { 
            background-color: #db2777;
            transform: translateY(-1px);
        }

        /* 画像ギャラリーのスタイル */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .image-box {
            width: 100%;
            padding-top: 100%; /* 1:1 のアスペクト比を維持 */
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #e5e7eb; /* 画像がない場合の背景 */
        }
        .image-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }
        .image-box:hover img {
            transform: scale(1.05);
        }

        /* ★追加★ コンテナ内の右上に配置するスタイル */
        .absolute-top-right {
            position: absolute;
            top: 20px; /* .container の上端からの距離（padding 40px より内側） */
            right: 20px; /* .container の右端からの距離 */
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="container">
        
        
        <a href="<?php echo e(route('top')); ?>" class="btn btn-top absolute-top-right">
            トップに戻る
        </a>

        <header class="mb-6">
            
            <h1>商品詳細</h1>
        </header>

        <section class="product-info">
            
            <div class="info-row">
                <!-- <div class="info-label">商品カテゴリ</div> -->
                <div class="info-content">
                    
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                        <?php echo e($product->category->name ?? '---'); ?>

                    </span>
                    <span class="mx-1">></span>
                    
                    <span class="bg-indigo-100 text-indigo-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                        <?php echo e($product->subcategory->name ?? '---'); ?>

                    </span>
                   
                </div>
            </div>
        
            
            <div class="info-row">
                <!-- <div class="info-label">商品名</div> -->
                <div class="info-content font-bold text-xl"><?php echo e($product->name); ?></div>
            </div>

            
             <div class="mt-6 text-sm text-gray-500 flex justify-between">
                <div>更新日時: <?php echo e($product->updated_at->format('Y/m/d H:i')); ?></div>
            </div>

            
            
            
            <!-- <h2 class="text-xl font-semibold mt-8 mb-4 text-gray-700">商品写真</h2> -->
            <div class="image-gallery">
                <?php for($i = 1; $i <= 4; $i++): ?>
                    <?php
                        // DBに保存されている画像パスを取得（例: products/1_16788...jpg）
                        $image_path = $product->{'image_' . $i};
                    ?>
                    <?php if($image_path): ?>
                        <div class="image-box">
                            
                            <img src="<?php echo e(asset('storage/' . $image_path)); ?>" 
                                 alt="商品写真 <?php echo e($i); ?>"
                                 onerror="this.onerror=null; this.src='https://placehold.co/150x150/cccccc/333333?text=画像なし'">
                        </div>
                    <?php else: ?>
                        
                        <div class="image-box flex items-center justify-center text-sm text-gray-500">
                            画像なし
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            
            <h2 class="text-xl font-semibold mt-8 mb-4 text-gray-700">商品説明</h2>
            <div class="description-box"><?php echo e($product->product_content); ?></div>
            
        </section>

        
        
        <?php echo $__env->make('product.review_section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        

        
        <div class="btn-group">
            <?php
                // Sessionから、ProductController@showDetailで保存した一覧画面へ戻るべきページ番号を取得
                $sourcePage = session('product_list_source_page', 1);
                
                // 戻り先のURLを生成。
                $backUrl = route('product.list', ['page' => $sourcePage]);
            ?>
            
            
            <a href="<?php echo e($backUrl); ?>" class="btn btn-back">
                商品一覧に戻る
            </a>
            
            
            <?php if(auth()->guard()->check()): ?>
                
                
                
                    <a href="<?php echo e(route('product.review.create', $product)); ?>" class="btn btn-review">
                        この商品についてのレビューを登録
                    </a>
                
                    <!-- 
                    <div class="text-pink-600 font-semibold p-3 border border-pink-300 bg-pink-50 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        この商品には既にレビューを投稿済みです
                    </div> -->
                
            <?php endif; ?>
            

        </div>
    </div>
</body>
</html>
<?php /**PATH /home/erika/laravel/resources/views/product/show.blade.php ENDPATH**/ ?>