<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールアドレス変更の認証コード</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">

    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h1 style="color: #ff8c00; font-size: 24px; text-align: center; border-bottom: 2px solid #ff8c00; padding-bottom: 10px;">メールアドレス変更の認証コード</h1>
        
        <p style="margin-top: 20px;">この度は、メールアドレスの変更手続きありがとうございます。</p>
        <p>新しいメールアドレス（<span style="font-weight: bold;">{{ $newEmail ?? '【新しいメールアドレス】' }}</span>）宛にこのメールが届いています。</p>
        <p>以下の認証コードを入力してください。</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 18px; color: #555; margin-bottom: 10px;">認証コード:</p>
            <p style="font-size: 32px; font-weight: bold; color: #ff8c00; background-color: #ffe0b2; display: inline-block; padding: 10px 20px; border-radius: 6px; letter-spacing: 5px;">
                {{ $authCode }}
            </p>
        </div>
    </div>

</body>
</html>
