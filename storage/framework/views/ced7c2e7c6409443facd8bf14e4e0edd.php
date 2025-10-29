<script src="https://cdn.tailwindcss.com"></script>
<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="<?php echo e(__('Pagination Navigation')); ?>" class="flex items-center justify-between">
        
        <!-- 狭い画面用の「前へ」「次へ」ボタン (隠れている部分) -->
        <div class="flex flex-1 justify-between sm:hidden">
            
            <?php if($paginator->onFirstPage()): ?>
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    <?php echo e(__('pagination.previous')); ?>

                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <?php echo e(__('pagination.previous')); ?>

                </a>
            <?php endif; ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    <?php echo e(__('pagination.next')); ?>

                </a>
            <?php else: ?>
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    <?php echo e(__('pagination.next')); ?>

                </span>
            <?php endif; ?>
        </div>

        <!-- 広い画面用のページネーション (ページ番号と「前へ」「次へ」) -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
            
            
            <?php if(!$paginator->onFirstPage()): ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="relative inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="<?php echo e(__('pagination.previous')); ?>">
                    <?php echo e(__('pagination.previous')); ?>

                </a>
            <?php endif; ?>
            
            
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                
                
                <?php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $windowSize = 3;

                    // ページ総数が表示ウィンドウサイズ以下の場合
                    if ($lastPage <= $windowSize) {
                        $startPage = 1;
                        $endPage = $lastPage;
                    } else {
                        // ページ総数がウィンドウサイズより大きい場合 (常に3ページ表示)
                        
                        // 1. デフォルトは現在ページを中心に設定
                        $startPage = $currentPage - 1;
                        $endPage = $currentPage + 1;

                        // 2. 開始位置が1より小さかった場合 (Page 1など)
                        if ($startPage < 1) {
                            $startPage = 1;
                            $endPage = $windowSize; // 1, 2, 3を表示
                        }
                        
                        // 3. 終了位置が最終ページを超えた場合 (最終ページなど)
                        if ($endPage > $lastPage) {
                            $endPage = $lastPage;
                            // 最終ページからウィンドウサイズ分遡る (例: 5, 4, 3)
                            $startPage = $lastPage - ($windowSize - 1); 
                        }
                    }
                ?>

                
                <?php $__currentLoopData = range($startPage, $endPage); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // LaravelのPaginatorから直接URLを取得
                        $url = $paginator->url($page);
                    ?>

                    <?php if($page == $currentPage): ?>
                        <span aria-current="page">
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5"><?php echo e($page); ?></span>
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>">
                            <?php echo e($page); ?>

                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </span>

            
            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="<?php echo e(__('pagination.next')); ?>">
                    <?php echo e(__('pagination.next')); ?>

                </a>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH /home/erika/laravel/resources/views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>