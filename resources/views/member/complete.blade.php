{{-- resources/views/member/complete.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録完了画面</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="confirm-container">
    <h1>会員登録完了</h1>
    <p>会員登録が完了しました。</p>

    <br>
    
    {{-- トップに戻るリンク --}}
    <a href="{{ route('top') }}">
        <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
    </a>
</div>
</body>
</html>