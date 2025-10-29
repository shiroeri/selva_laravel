<?php
// Controllerから渡されることを想定
// productを渡すことで、リンクボタンの遷移先を正しく設定できます
$product = $product ?? (object)['id' => 1, 'name' => '商品名不明'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レビュー登録完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* アイコンの装飾 */
        .check-icon {
            animation: bounce-in 0.8s ease-out;
        }
        @keyframes bounce-in {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4 sm:p-8 font-sans">

<div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 shadow-xl rounded-2xl border border-gray-200">
    
    <div class="flex justify-between items-center mb-8 border-b pb-4">
        <div class="flex items-center space-x-4">
            <h1 class="text-3xl font-extrabold text-gray-900">商品レビュー登録完了</h1>
        </div>
        
        
        <a href="<?php echo e(route('top')); ?>" class="flex items-center space-x-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition p-2 rounded-lg bg-gray-50 hover:bg-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2v10a1 1 0 01-1 1h-3"></path></svg>
            <span>トップに戻る</span>
        </a>
    </div>

        
        <p class="text-xl text-gray-700 mb-10">
            商品レビューの登録が完了しました。
        </p>

        
        <div class="flex flex-col space-y-4 w-full">
            
        
        <a href="<?php echo e(route('product.reviews.index', $product)); ?>" 
           class="w-full px-8 py-3 text-lg font-medium rounded-xl shadow-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300">
            商品レビュー一覧へ
        </a>
            
            
            <a href="<?php echo e($product->id ? route('product.show', $product) : '#'); ?>" 
               class="w-full px-8 py-3 text-lg font-medium rounded-xl shadow-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300">
                商品詳細に戻る
            </a>
        </div>
        
    
</div>
</body>
</html>
<?php /**PATH /home/erika/laravel/resources/views/product/review/complete.blade.php ENDPATH**/ ?>