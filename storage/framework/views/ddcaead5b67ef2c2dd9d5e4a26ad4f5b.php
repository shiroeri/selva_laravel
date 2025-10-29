
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p><?php echo e($member->name_sei); ?> 様</p>
    
    <p>この度は、ご登録いただき誠にありがとうございます。</p>
    
    <p>お客様の登録内容は以下の通りです。</p>
    
    <ul>
        <li>氏名：<?php echo e($member->name_sei); ?> <?php echo e($member->name_mei); ?></li>
        <li>ニックネーム：<?php echo e($member->nickname); ?></li>
        <li>メールアドレス：<?php echo e($member->email); ?></li>
    </ul>
    
    <p>今後ともよろしくお願いいたします。</p>
    
    <p>セルバシステム運営事務局</p>
</body>
</html><?php /**PATH /home/erika/laravel/resources/views/emails/member_registered.blade.php ENDPATH**/ ?>