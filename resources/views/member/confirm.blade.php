{{-- resources/views/member/confirm.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録確認画面</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="confirm-container">
    <h1>会員情報確認画面</h1>

    <table class="confirm-table">
        <tr>
            <th>氏名</th>
            <td>{{ $data['name_sei'] ?? '' }}&nbsp;{{ $data['name_mei'] ?? '' }}</td>
        </tr>
        <tr>
            <th>ニックネーム</th>
            <td>{{ $data['nickname'] ?? '' }}</td>
        </tr>
        <tr>
            <th>性別</th>
            <td>
                {{ config('system.master.genders')[$data['gender']] ?? '未選択' }}
            </td>
        </tr>
        <tr>
            <th>パスワード</th>
            <td>セキュリティのため非表示</td> {{-- パスワードは非表示（仕様書通り） --}}
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>{{ $data['email'] ?? '' }}</td>
        </tr>
    </table>
    
    <div class="confirm-actions">
        {{-- 登録（ストア）処理へ進むボタン --}}
        <form action="{{ route('member.store') }}" method="POST" id="register-form" style="display: inline;">
            @csrf
            <button type="submit" id="register-button" class="base-button primary-button">登録完了</button>
        </form>

        {{-- 二重送信防止用JavaScript --}}
        <script>
            document.getElementById('register-form').addEventListener('submit', function() {
                // フォーム送信後、ボタンを非活性化する
                document.getElementById('register-button').disabled = true;
                document.getElementById('register-button').innerText = '登録中...';
            });
        </script>

        {{-- 入力画面に戻るリンク --}}
        <a href="{{ route('member.input') }}">
            <button type="button" class="base-button secondary-button">前に戻る</button>
        </a>
    </div>
    </div>
</body>
</html>