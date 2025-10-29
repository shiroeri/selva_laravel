<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($product->name); ?> のレビュー一覧</title>
<!-- Tailwind CSS CDN (実際のプロジェクトではインストールを推奨) -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* 星アイコンのスタイル */
.star-rating {
    display: inline-flex; /* Flexboxを使用して星を水平に並べる */
    color: #FFC700; /* 金色 */
}
.star-full:before { content: "\2605"; } /* 塗りつぶされた星 */
.star-empty:before { content: "\2606"; } /* 空の星 */

/* 商品カード内の星評価 */
.product-card-star .star-full:before,
.product-card-star .star-empty:before {
    font-size: 1.5rem; /* 大きめの星 */
}
/* 個別レビュー内の星評価 */
.review-star .star-full:before,
.review-star .star-empty:before {
    font-size: 1.25rem; /* 通常の星 */
}
/* ページャーのデザイン: 現在のページ番号 */
/* @apply を解消し、カスタムスタイルのみを定義 */
.custom-pagination .current-page {
    background-color: #4f46e5; /* indigo-600 */
    color: white;
    font-weight: bold;
    border-color: #4f46e5;
    pointer-events: none; /* 現在のページはクリック不可にする */
}
/* .custom-pagination .page-link の定義はHTML要素に直接展開するため削除しました */
</style>
</head>
<body class="bg-gray-50 p-4 sm:p-8 font-['Inter']">

<div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-xl p-6 md:p-10 border border-gray-100">

<!-- ヘッダー: 商品情報カード -->
<header class="border-b pb-6 mb-8">

    
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー一覧</h1>
        </div>
        
        
        <a href="<?php echo e(route('product.review.cancel_to_top', $product)); ?>" class="flex items-center space-x-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition p-2 rounded-lg bg-gray-50 hover:bg-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2v10a1 1 0 01-1 1h-3"></path></svg>
            <span>トップに戻る</span>
        </a>
    </div>
    
    
    <div class="p-6 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
        <div class="flex-shrink-0">
            <?php
                // DBに保存されている画像パス (image_1) を使用
                $imagePath = $product->image_1 ?? null;
                // asset('storage/' ...) のルートが適切であることを前提としています
                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
            ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product->name); ?>" class="w-20 h-20 object-cover rounded-lg shadow-md" 
                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
        </div>
        <div class="flex-grow text-center sm:text-left">
            <p class="text-2xl font-semibold text-gray-700 mt-1"><?php echo e($product->name ?? '商品名不明'); ?></p>
        </div>
        
        
        <div class="flex-shrink-0 text-center">
            <?php
                // Controllerから渡された切り上げ後の値を使用
                $avg_evaluation_ceiled = $averageEvaluationCeiled ?? 0;
                $review_count = $reviews->total(); // ページネーションオブジェクトから総件数を取得
                // 星評価は切り上げ後の値そのものを使用
                $rounded_avg = $avg_evaluation_ceiled;
            ?>
            <p class="text-sm font-medium text-gray-600">総合評価</p>
            
            <p class="text-4xl font-extrabold text-indigo-600 mt-1"><?php echo e(number_format($avg_evaluation_ceiled)); ?></p>
            <div class="star-rating product-card-star justify-center mt-1">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <span class="<?php echo e($i <= $rounded_avg ? 'star-full' : 'star-empty'); ?>"></span>
                <?php endfor; ?>
            </div>
            <!-- <p class="text-xs text-gray-500 mt-1">（全<?php echo e(number_format($review_count)); ?>件）</p> -->
        </div>
    </div>
</header>

<main>

    <?php if($reviews->isEmpty()): ?>
        <div class="text-gray-500 p-6 border-4 border-dashed border-gray-200 rounded-xl text-center bg-white">
            <p class="text-lg font-medium">まだレビューはありません。</p>
            <p class="text-sm mt-1">最初のレビューを投稿してみましょう！</p>
        </div>
    <?php else: ?>
        <!-- レビューリスト -->
        <div class="space-y-8">
            <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border-b pb-8 last:border-b-0">
                    <!-- 投稿者名と日付 -->
                    <div class="flex items-center justify-between mb-3">
                         <p class="text-sm text-gray-500">
                            <span class="font-bold text-gray-700"><?php echo e($review->member->name ?? '匿名ユーザー'); ?>さん</span>
                        </p>
                        <!-- <p class="text-xs text-gray-400">
                            <?php echo e($review->created_at->format('Y年m月d日 H:i')); ?>

                        </p> -->
                    </div>

                    <!-- 星評価と数字 -->
                    <div class="flex items-center space-x-3 mb-2">
                        <!-- 星評価 -->
                        <div class="star-rating review-star">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="<?php echo e($i <= $review->evaluation ? 'star-full' : 'star-empty'); ?>"></span>
                            <?php endfor; ?>
                        </div>
                        <!-- 評価値 -->
                        <span class="text-xl font-bold text-gray-800"><?php echo e($review->evaluation); ?></span>
                    </div>

                    <!-- レビュータイトル -->
                    <?php if($review->title ?? false): ?>
                        <h3 class="text-lg font-bold text-gray-800 mt-1"><?php echo e($review->title); ?></h3>
                    <?php endif; ?>

                    <br>

                    <!-- レビュー本文 -->
                     <h1>商品コメント</h1>
                    <p class="text-gray-700 leading-relaxed mt-2 bg-gray-50 p-4 rounded-lg border border-gray-100 shadow-sm">
                        <?php echo nl2br(e($review->comment)); ?>

                    </p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- ページネーションリンク (カスタム実装) -->
        <?php if($reviews->hasPages()): ?>
            <?php
                // @apply で定義されていたクラス群
                $linkClasses = 'px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 transition duration-150 hover:bg-gray-100';
                
                $currentPage = $reviews->currentPage();
                $lastPage = $reviews->lastPage();
                
                // 3ページ分表示のためのロジック
                $startPage = max(1, $currentPage - 1);
                $endPage = min($lastPage, $startPage + 2);
                $startPage = max(1, $endPage - 2); // 3つ表示を維持
            ?>
            
            <div class="mt-10 custom-pagination flex justify-center items-center space-x-2">
                
                <?php if(!$reviews->onFirstPage()): ?>
                    
                    <a href="<?php echo e($reviews->previousPageUrl()); ?>" class="<?php echo e($linkClasses); ?> hover:bg-indigo-50" title="前へ">前へ</a>
                <?php endif; ?>
                
                
                <div class="flex space-x-2">
                    <?php for($page = $startPage; $page <= $endPage; $page++): ?>
                        <?php if($page == $currentPage): ?>
                            
                            <span class="current-page <?php echo e($linkClasses); ?>"><?php echo e($page); ?></span>
                        <?php else: ?>
                            
                            <a href="<?php echo e($reviews->url($page)); ?>" class="<?php echo e($linkClasses); ?>"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                
                <?php if($reviews->hasMorePages()): ?>
                    
                    <a href="<?php echo e($reviews->nextPageUrl()); ?>" class="<?php echo e($linkClasses); ?> hover:bg-indigo-50" title="次へ">次へ</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <div class="mt-10 flex justify-center"> 
        <a href="<?php echo e($product->id ? route('product.show', $product) : '#'); ?>" 
            class="px-6 py-3 border border-gray-300 text-lg font-medium rounded-xl shadow-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300">
            商品詳細に戻る
        </a>
    </div>

</main>


</div>

</body>
</html>
<?php /**PATH /home/erika/laravel/resources/views/product/review/index.blade.php ENDPATH**/ ?>