{{-- resources/views/password/reset.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワード再設定（パスワード設定）</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="confirm-container">
    <h1>パスワード再設定</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        {{-- 認証に必要なトークンとメールアドレスを隠しフィールドで送信 --}}
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-row">
            <label for="password">パスワード</label>
            {{-- パスワードは「●●●で見えないように」 type="password" を使用 --}}
            <input 
                type="password" 
                id="password" 
                name="password"
                autocomplete="new-password"
            >
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label for="password_confirmation">パスワード確認</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation"
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="base-button primary-button submit-center-button">パスワードリセット</button>
    </form>
    
    {{-- トップに戻るリンク --}}
    <a href="{{ route('top') }}">
            <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
    </a>
</div>

</body>
</html>