<?php
// Controllerから渡されることを想定したダミー/フォールバックデータ

// 1. 商品情報
$product = $product ?? (object)['id' => 1, 'name' => '商品名不明', 'image_1' => 'dummy_path.jpg'];

// 2. 確認するレビューデータ (ユーザーが今回入力したデータ)
$reviewData = $reviewData ?? ['rating' => 5, 'body' => '確認データがありません。'];

// 3. 商品の総合評価情報 (create.blade.phpと共通の表示に必要なため追加)
//    ※Controllerが渡さない場合に備えてダミー値を設定
//    【重要】Controllerでこの変数に「商品の実際の平均評価」を渡しているか確認してください。
$averageEvaluation = $averageEvaluation ?? 4.2;
$reviewCount = $reviewCount ?? 150;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー登録内容確認</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* カスタムスタイル: 星の評価表示 */
        .star-rating { color: #f59e0b; font-size: 1.5rem; }
        .star-rating .empty { color: #d1d5db; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4 sm:p-8 font-sans">

<div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 shadow-xl rounded-2xl border border-gray-200">
    
    
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー登録確認</h1>
        </div>
        
        
        <a href="<?php echo e(route('product.review.cancel_to_top', $product)); ?>" class="flex items-center space-x-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition p-2 rounded-lg bg-gray-50 hover:bg-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2v10a1 1 0 01-1 1h-3"></path></svg>
            <span>トップに戻る</span>
        </a>
    </div>

    
    <div class="mb-8 p-6 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
        <div class="flex-shrink-0">
            <?php
                // DBに保存されている画像パス (image_1) を使用
                $imagePath = $product->image_1 ?? null;
                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : 'https://placehold.co/80x80/cccccc/333333?text=No+Photo';
            ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product->name); ?>" class="w-20 h-20 object-cover rounded-lg shadow-md" 
                 onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/333333?text=Error'">
        </div>
        <div class="flex-grow text-center sm:text-left">
            <p class="text-2xl font-semibold text-gray-700 mt-1"><?php echo e($product->name ?? '商品名不明'); ?></p>
        </div>
        <div class="flex-shrink-0 text-center">
            <p class="text-sm font-medium text-gray-600">総合評価</p>
            
            <p class="text-4xl font-extrabold text-indigo-600 mt-1"><?php echo e(ceil($averageEvaluation)); ?></p>
            <div class="star-rating">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <span class="<?php echo e($i <= ceil($averageEvaluation) ? 'filled' : 'empty'); ?>">★</span>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    
    <div class="space-y-6 mb-10">
        
        <div class="p-4 border rounded-lg bg-gray-50">
            <p class="text-lg font-bold text-gray-700 mb-1">商品評価</p>
            <div class="text-3xl font-extrabold text-indigo-600">
                <?php echo e($reviewData['rating'] ?? 'N/A'); ?> 点
            </div>
            <div class="star-rating mt-1">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    
                    <span class="<?php echo e($i <= ($reviewData['rating'] ?? 0) ? 'filled' : 'empty'); ?>">★</span>
                <?php endfor; ?>
            </div>
        </div>

        
        <div class="p-4 border rounded-lg bg-gray-50">
            <p class="text-lg font-bold text-gray-700 mb-2">商品コメント</p>
            <div class="prose max-w-none p-3 bg-white rounded-lg border border-gray-200 shadow-inner">
                
                <?php if(!empty($reviewData['body'])): ?>
                    <p class="whitespace-pre-wrap text-gray-800 leading-relaxed"><?php echo (e($reviewData['body'])); ?></p>
                <?php else: ?>
                    <p class="text-gray-500 italic">コメントなし</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    
    <form method="POST" action="<?php echo e($product->id ? route('product.review.store', $product) : '#'); ?>">
        <?php echo csrf_field(); ?>

        
        <input type="hidden" name="rating" value="<?php echo e($reviewData['rating'] ?? ''); ?>">
        <input type="hidden" name="body" value="<?php echo e($reviewData['body'] ?? ''); ?>">

        
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            
            <a href="<?php echo e($product->id ? route('product.review.create', $product) : '#'); ?>" 
               class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-lg font-medium rounded-xl shadow-md text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-300">
                前に戻る
            </a>
            
            
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 border border-transparent text-lg font-medium rounded-xl shadow-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500 transition duration-300 transform hover:scale-[1.02]"
                    <?php echo e($product->id ? '' : 'disabled'); ?>>
                登録する
            </button>
        </div>
    </form>
</div>
</body>
</html>
<?php /**PATH /home/erika/laravel/resources/views/product/review/confirm.blade.php ENDPATH**/ ?>