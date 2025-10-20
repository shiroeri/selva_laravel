{{-- resources/views/emails/password-reset-link.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
<h1>パスワード再発行</h1>

<p>以下のURLをクリックしてパスワードを再発行してください。</p>

<p>
    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
</p>

</body>
</html>