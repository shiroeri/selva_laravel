{{-- resources/views/password/request.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワード再設定</title>
    {{-- 共通のスタイルシートを読み込む場合 --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="confirm-container">
        <h1>パスワード再設定</h1>

        <p>パスワード再設定用のURLを記載したメールを送信します。</p>
        <p>ご登録されたメールアドレスを入力してください。</p>

        <br>

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="form-row">
                <label for="email">メールアドレス</label>
                <input 
                    type="text" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                >
                {{-- コントローラーで設定したバリデーションエラーを表示 --}}
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="base-button primary-button submit-center-button">送信する</button>
        </form>
        
        {{-- トップに戻るリンク --}}
        <a href="{{ route('top') }}">
            <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
        </a>
    </div>

</body>
</html>