{{-- resources/views/password/sent.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワード再設定（完了）</title>
    {{-- 共通のスタイルシートを読み込みます --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="page-container">
    <div class="confirm-container">
        <h1>パスワード再設定（メール送信完了）</h1>

        <p>パスワード再設定の案内メールを送信しました。</p>
        <p>（まだパスワードの再設定は完了しておりませんん）</p>
        <p>届きましたメールに記載されている</p>
        <p>『パスワード再設定URL』をクリックし、</p>
        <p>パスワードの再設定を完了させてください。</p>
        
        {{-- トップに戻るリンク --}}
        <a href="{{ route('top') }}">
            <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
        </a>
    </div>
</div>

</body>
</html>