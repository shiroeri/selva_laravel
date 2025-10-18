{{-- resources/views/emails/member_registered.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>{{ $member->name_sei }} 様</p>
    
    <p>この度は、ご登録いただき誠にありがとうございます。</p>
    
    <p>お客様の登録内容は以下の通りです。</p>
    
    <ul>
        <li>氏名：{{ $member->name_sei }} {{ $member->name_mei }}</li>
        <li>ニックネーム：{{ $member->nickname }}</li>
        <li>メールアドレス：{{ $member->email }}</li>
    </ul>
    
    <p>今後ともよろしくお願いいたします。</p>
    
    <p>セルバシステム運営事務局</p>
</body>
</html>