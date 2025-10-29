
<div class="review-section mt-10 p-6 bg-white shadow-lg rounded-xl">
    <h2 class="text-2xl font-bold text-gray-800 border-b-2 pb-2 mb-6">商品レビュー</h2>

    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4">
        
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <span class="">総合評価</span>
            <div class="text-sm">
                
                <div class="text-yellow-500 text-2xl" style="letter-spacing: 0.1em;">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if($i <= ceil($averageEvaluation)): ?>
                            ★
                        <?php else: ?>
                            <span class="text-gray-300">★</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
            <span class="text-4xl font-extrabold text-indigo-600 mr-2">
                <?php echo e(ceil($averageEvaluation)); ?>

            </span>
        </div>
    </div>
    
    <div class="pt-4">
        <a href="<?php echo e(route('product.reviews.index', $product)); ?>" 
           class="text-lg font-semibold text-indigo-600 hover:text-indigo-800 transition duration-150 ease-in-out flex items-center group">
            >> レビューを見る
        </a>
    </div>

</div>
<?php /**PATH /home/erika/laravel/resources/views/product/review_section.blade.php ENDPATH**/ ?>