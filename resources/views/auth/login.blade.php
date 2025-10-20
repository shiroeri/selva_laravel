{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    {{-- 既存のスタイルシートを読み込みます --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="confirm-container">
        <h1>ログイン</h1>

        {{-- ログイン処理を行うルート（通常は Route::post('/login', ...) ） --}}
        {{-- ブラウザの自動バリデーションを無効化 --}}
        <form method="POST" action="{{ route('login') }}" novalidate> 
            @csrf

            {{-- 1. メールアドレス (ログインID) --}}
            <div class="form-row">
                <label for="email">メールアドレス</label>
                <input 
                    type="text" {{-- type="email" ではなく type="text" にすることで、Laravelのエラー表示を優先させやすくします --}}
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                >
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 2. パスワード --}}
            <div class="form-row">
                <label for="password">パスワード</label>
                <input 
                    type="password" {{-- ****表示のため type="password" にします --}}
                    id="password" 
                    name="password"
                    value="" {{-- エラーで戻った場合も値を表示しない（空にする） --}}
                >
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

                {{-- ★Laravelの認証失敗時のグローバルエラーメッセージを表示する場所★ --}}
                {{-- DB連携エラー（ID/PW不一致）の場合、Laravelは通常 $errors->default にメッセージを格納します --}}
                @error('login_id') 
                    {{-- 失敗時のメッセージはコントローラー側で 'login_id' や 'password' に関連付けられることが多い --}}
                    <div class="error">{{ $message }}</div> 
                @enderror
                @if ($errors->has('id') || $errors->has('password'))
                    <div class="error">IDもしくはパスワードが間違っています</div>
                @endif
                <a href="{{ route('password.request') }}" style="padding-left: 170px;">パスワードを忘れた方はこちら</a>
            </div>

            
            
            
            <button type="submit" class="base-button primary-button submit-center-button">ログイン</button>
            {{-- トップに戻るリンク --}}
            <a href="{{ route('top') }}">
                <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
            </a>
        </form>
    </div>
</body>
</html>