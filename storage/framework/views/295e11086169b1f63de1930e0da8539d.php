
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>
    <div class="confirm-container">
    <h1>会員情報登録</h1>

    
    <form action="<?php echo e(route('member.confirm')); ?>" method="POST" novalidate>
        <?php echo csrf_field(); ?>

        
        <div class="form-row name-group">
            <label class="name-label">氏名</label>
            <div class="name-fields">
                
                <div class="name-item">
                    <label for="name_sei">姓</label>
                    <input type="text" id="name_sei" name="name_sei" value="<?php echo e(old('name_sei', $member['name_sei'] ?? '')); ?>">
                    
                    <?php $__errorArgs = ['name_sei'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error name-item-error"><?php echo e($message); ?></div> 
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="name-item">
                    <label for="name_mei">名</label>
                    <input type="text" id="name_mei" name="name_mei" value="<?php echo e(old('name_mei', $member['name_mei'] ?? '')); ?>">
                    
                    <?php $__errorArgs = ['name_mei'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error name-item-error"><?php echo e($message); ?></div> 
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            
            
        </div>

        
        <div class="form-row">
            <label for="nickname">ニックネーム</label>
            <input type="text" id="nickname" name="nickname" value="<?php echo e(old('nickname', $member['nickname'] ?? '')); ?>">
            <?php $__errorArgs = ['nickname'];
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
            <label>性別</label>
            <div class="gender-options">
                <?php $__currentLoopData = config('system.master.genders'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="radio-label">
                        <input 
                            type="radio" 
                            name="gender" 
                            value="<?php echo e($value); ?>" 
                            <?php echo e((old('gender', $member['gender'] ?? '') == $value) ? 'checked' : ''); ?>

                        > <?php echo e($label); ?>

                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['gender'];
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
            <input type="password" id="password" name="password">
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
        </div>
        
        
        <div class="form-row">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            <?php $__errorArgs = ['password_confirmation'];
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
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="<?php echo e(old('email', $member['email'] ?? '')); ?>">
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

        <button type="submit" class="base-button primary-button submit-center-button">確認画面へ</button>

        
        <a href="<?php echo e(route('top')); ?>">
            <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
        </a>
    </form>
    </div>
</body>
</html><?php /**PATH /home/erika/laravel/resources/views/member/input.blade.php ENDPATH**/ ?>