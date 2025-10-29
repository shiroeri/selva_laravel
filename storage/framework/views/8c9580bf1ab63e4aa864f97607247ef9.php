
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>トップ画面</title>
    
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    <style>
        /* トップ画面用の簡単なスタイル（既存のCSSがない場合の仮のスタイル） */
        .top-container {
            width: 80%;
            margin: 50px auto;
            text-align: center;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            border-bottom: 1px solid #ccc;
        }
        .header-nav a, .header-nav button {
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid #333;
            margin-left: 10px;
            cursor: pointer;
        }
        .welcome-message {
            font-size: 1.1em;
            font-weight: bold;
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>トップページ</h1>
        <div class="header-nav">

            
            
            
            <?php if(auth()->guard()->check()): ?>
                
                <span class="welcome-message">
                    ようこそ <?php echo e(Auth::user()->name_sei); ?> <?php echo e(Auth::user()->name_mei); ?>様
                </span>
                <a href="<?php echo e(route('product.list')); ?>">商品一覧</a>
                <a href="<?php echo e(route('product.create')); ?>">新規商品登録</a>
                
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="header-button base-button">ログアウト</button>
                </form>
                
            <?php endif; ?>

            
            
            
            <?php if(auth()->guard()->guest()): ?>
                

                <a href="<?php echo e(route('product.list')); ?>">商品一覧</a>

                
                <a href="<?php echo e(route('member.input')); ?>" class="header-button base-button primary-button">新規会員登録</a>

                
                <a href="<?php echo e(route('login')); ?>" class="header-button base-button">ログイン</a>
            <?php endif; ?>

        </div>
    </header>

    <div class="top-container">
        <h2></h2>
        <p></p>
    </div>

</body>
</html><?php /**PATH /home/erika/laravel/resources/views/top.blade.php ENDPATH**/ ?>