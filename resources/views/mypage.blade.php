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
            margin: 20px;/
        }
        .mypage-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* シャドウを強調 */
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
            display: inline-block;
            text-align: center;
        }
        .btn-return {
            background-color: #eee;
            color: #333;
            transition: background-color 0.3s;
        }
        .btn-return:hover {
            background-color: #ddd;
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-logout:hover {
            background-color: #c82333;
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
        
        /* --- 退会ボタンのスタイルを追加/調整 --- */
        .withdraw-area {
            margin-top: 30px;
            text-align: center; /* ボタンを中央に配置 */
        }

        .btn-withdraw {
            /* 基本デザイン */
            display: inline-block;
            padding: 10px 40px;
            font-size: 16px;
            font-weight: bold;
            color: #2a64a3; /* 文字色: 青 */
            background-color: #ffffff; /* 背景色: 白 */
            border: 2px solid #a8c8e8; /* 枠線: 薄い青 */
            border-radius: 25px; /* 角丸を大きく */
            text-decoration: none;
            cursor: pointer;
            
            /* 立体感・影の追加 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
        }

        .btn-withdraw:hover {
            background-color: #f0f8ff; /* ホバーで少し明るく */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* ホバーで影を強調 */
            transform: translateY(-2px);
        }
        /* --- スタイルここまで --- */
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

            <div style="text-align: center; padding: 20px;">
                <!-- 
                    Laravelの route() ヘルパ関数を使用して、
                    MemberEditControllerの form メソッドに対応するURL（/member/edit）を生成します。
                -->
                <a href="{{ route('member.edit.form') }}" 
                   style="
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        border: none;
                        transition: background-color 0.3s;
                   "
                   onmouseover="this.style.backgroundColor='#0056b3'"
                   onmouseout="this.style.backgroundColor='#007bff'">
                    会員情報変更
                </a>
            </div>


            <!-- パスワード (仕様: セキュリティのため非表示) -->
            <div class="info-row">
                <div class="info-label">パスワード</div>
                <div class="info-value" style="color: red;">セキュリティのため非表示</div>
            </div>

            <div style="text-align: center; padding: 20px;">
                <!-- 
                    Laravelの route() ヘルパ関数を使用して、
                    MemberEditControllerの form メソッドに対応するURL（/member/edit）を生成します。
                -->
                <a href="{{ route('member.password.edit.form') }}" 
                   style="
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        border: none;
                        transition: background-color 0.3s;
                   "
                   onmouseover="this.style.backgroundColor='#0056b3'"
                   onmouseout="this.style.backgroundColor='#007bff'">
                    パスワード変更
                </a>
            </div>

            <!-- メールアドレス -->
            <div class="info-row">
                <div class="info-label">メールアドレス</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>

            <div style="text-align: center; padding: 20px;">
                <!-- 
                    Laravelの route() ヘルパ関数を使用して、
                    MemberEditControllerの form メソッドに対応するURL（/member/edit）を生成します。
                -->
                <a href="{{ route('member.email.show-form') }}" 
                   style="
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        border: none;
                        transition: background-color 0.3s;
                   "
                   onmouseover="this.style.backgroundColor='#0056b3'"
                   onmouseout="this.style.backgroundColor='#007bff'">
                    メールアドレス変更
                </a>
            </div>

            <div style="text-align: center; padding: 20px;">
                <!-- 
                    Laravelの route() ヘルパ関数を使用して、
                    MemberEditControllerの form メソッドに対応するURL（/member/edit）を生成します。
                -->
                <a href="{{ route('mypage.reviews.index') }}" 
                   style="
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        border: none;
                        transition: background-color 0.3s;
                   "
                   onmouseover="this.style.backgroundColor='#0056b3'"
                   onmouseout="this.style.backgroundColor='#007bff'">
                    商品レビュー管理
                </a>
            </div>

        </div>
        
        <!-- 退会ボタンを中央に配置するエリア -->
        <div class="withdraw-area">
            <a href="{{ route('withdraw.confirm') }}" class="btn-withdraw">
                退会
            </a>
        </div>
    </div>
</body>
</html>
