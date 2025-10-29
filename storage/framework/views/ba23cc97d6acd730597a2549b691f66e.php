
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>
    <div class="confirm-container">
        <h1>ログイン</h1>

        
        
        <form method="POST" action="<?php echo e(route('login')); ?>" novalidate> 
            <?php echo csrf_field(); ?>

            
            <div class="form-row">
                <label for="email">メールアドレス</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    value="<?php echo e(old('email')); ?>"
                >
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="form-row">
                <label for="password">パスワード</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    value="" 
                >
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                
                
                <?php $__errorArgs = ['login_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    
                    <div class="error"><?php echo e($message); ?></div> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php if($errors->has('id') || $errors->has('password')): ?>
                    <div class="error">IDもしくはパスワードが間違っています</div>
                <?php endif; ?>
                <a href="<?php echo e(route('password.request')); ?>" style="padding-left: 170px;">パスワードを忘れた方はこちら</a>
            </div>

            
            
            
            <button type="submit" class="base-button primary-button submit-center-button">ログイン</button>
            
            <a href="<?php echo e(route('top')); ?>">
                <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
            </a>
        </form>
    </div>
</body>
</html><?php /**PATH /home/erika/laravel/resources/views/auth/login.blade.php ENDPATH**/ ?>