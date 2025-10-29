<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .mypage-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: inline-block;
        }
        .header-buttons {
            float: right;
            margin-top: 5px;
        }
        .header-buttons button, .header-buttons a {
            padding: 8px 15px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-return {
            background-color: #eee;
            color: #333;
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            flex-grow: 1;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="mypage-container">
        <!-- ページヘッダーとボタン -->
        <div>
            <h1 style="display: inline-block; margin-right: 20px;">マイページ</h1>
            <div class="header-buttons">
                <!-- 仕様: 「トップに戻る」ボタン -->
                <a href="{{ route('top') }}" class="btn-return">トップに戻る</a>
                
                <!-- 仕様: 「ログアウト」ボタン -->
                <!-- ログアウトはPOSTリクエストで行うためフォームを使用 -->
                <form method="POST" action="{{ route('logout') }}" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn-logout">ログアウト</button>
                </form>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- 会員情報表示エリア -->
        <div>
            
            <!-- 氏名 -->
            <div class="info-row">
                <div class="info-label">氏名</div>
                <!-- Auth::user()から取得した情報 $user を表示 -->
                <div class="info-value">{{ $user->name }}</div>
            </div>

            <!-- ニックネーム -->
            <div class="info-row">
                <div class="info-label">ニックネーム</div>
                <!-- ニックネームの属性名が 'nickname' であると仮定 -->
                <div class="info-value">{{ $user->nickname ?? '設定なし' }}</div>
            </div>

            <!-- 性別 -->
            <div class="info-row">
                <div class="info-label">性別</div>
                <!-- 性別データの表示ロジックは、DB上のカラム名と値に依存します -->
                <div class="info-value">
                    {{ $user->gender == 1 ? '男性' : ($user->gender == 2 ? '女性' : '未設定') }}
                </div>
            </div>

            <!-- パスワード (仕様: セキュリティのため非表示) -->
            <div class="info-row">
                <div class="info-label">パスワード</div>
                <div class="info-value" style="color: red;">セキュリティのため非表示</div>
            </div>

            <!-- メールアドレス -->
            <div class="info-row">
                <div class="info-label">メールアドレス</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
        </div>
    </div>
</body>
</html>
