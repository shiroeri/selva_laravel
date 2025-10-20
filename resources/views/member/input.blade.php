{{-- resources/views/member/input.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="confirm-container">
    <h1>会員情報登録</h1>

    {{-- フォームの送信先は確認画面の処理 --}}
    <form action="{{ route('member.confirm') }}" method="POST" novalidate>
        @csrf

        {{-- 氏名（姓）と氏名（名）を一つのグループで扱う --}}
        <div class="form-row name-group">
            <label class="name-label">氏名</label>
            <div class="name-fields">
                {{-- 氏名（姓） --}}
                <div class="name-item">
                    <label for="name_sei">姓</label>
                    <input type="text" id="name_sei" name="name_sei" value="{{ old('name_sei', $member['name_sei'] ?? '') }}">
                    {{-- ★修正点1: 姓のエラーをここへ移動★ --}}
                    @error('name_sei')
                        <div class="error name-item-error">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- 氏名（名） --}}
                <div class="name-item">
                    <label for="name_mei">名</label>
                    <input type="text" id="name_mei" name="name_mei" value="{{ old('name_mei', $member['name_mei'] ?? '') }}">
                    {{-- ★修正点2: 名のエラーをここへ移動★ --}}
                    @error('name_mei')
                        <div class="error name-item-error">{{ $message }}</div> 
                    @enderror
                </div>
            </div>
            
            {{-- 氏名に関するエラーの統合コンテナは削除しました --}}
        </div>

        {{-- ニックネーム --}}
        <div class="form-row">
            <label for="nickname">ニックネーム</label>
            <input type="text" id="nickname" name="nickname" value="{{ old('nickname', $member['nickname'] ?? '') }}">
            @error('nickname')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- 性別 (ラジオボタン: 1=男性, 2=女性) --}}
        <div class="form-row">
            <label>性別</label>
            <div class="gender-options">
                @foreach (config('system.master.genders') as $value => $label)
                    <label class="radio-label">
                        <input 
                            type="radio" 
                            name="gender" 
                            value="{{ $value }}" 
                            {{ (old('gender', $member['gender'] ?? '') == $value) ? 'checked' : '' }}
                        > {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('gender')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-row">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>
        
        {{-- パスワード確認 --}}
        <div class="form-row">
            <label for="password_confirmation">パスワード確認</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
            @error('password_confirmation') 
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="form-row">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email', $member['email'] ?? '') }}">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="base-button primary-button submit-center-button">確認画面へ</button>

        {{-- トップに戻るリンク --}}
        <a href="{{ route('top') }}">
            <button type="button" class="base-button secondary-button submit-center-button">トップに戻る</button>
        </a>
    </form>
    </div>
</body>
</html>