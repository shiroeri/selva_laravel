{{-- resources/views/top.blade.php --}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>トップ画面</title>
    {{-- 既存のスタイルシートを読み込みます --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* トップ画面用の簡単なスタイル（既存のCSSがない場合の仮のスタイル） */
        .top-container {
            width: 80%;
            margin: 50px auto;
            text-align: center;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            border-bottom: 1px solid #ccc;
        }
        .header-nav a, .header-nav button {
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid #333;
            margin-left: 10px;
            cursor: pointer;
        }
        .welcome-message {
            font-size: 1.1em;
            font-weight: bold;
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>トップページ</h1>
        <div class="header-nav">

            {{-- =================================== --}}
            {{-- ログイン状態にあるとき（@auth） --}}
            {{-- =================================== --}}
            @auth
                {{-- 設計書要件: 「ようこそ〇〇様」と氏名を表示 --}}
                <span class="welcome-message">
                    ようこそ {{ Auth::user()->name_sei }} {{ Auth::user()->name_mei }}様
                </span>
                <a href="{{ route('product.list') }}">商品一覧</a>
                <a href="{{ route('product.create') }}">新規商品登録</a>
                {{-- 仕様: クリックでマイページへ遷移（ログイン時のみ表示・遷移可能） --}}
                <a href="{{ route('mypage.index') }}" class="nav-item nav-item-mypage">マイページ</a>
                {{-- 設計書要件: 「ログアウト」ボタンを表示 --}}
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="header-button base-button">ログアウト</button>
                </form>
                
            @endauth

            {{-- =================================== --}}
            {{-- ログアウト状態にあるとき（@guest） --}}
            {{-- =================================== --}}
            @guest
                {{-- 設計書要件: 「ようこそ〇〇様」は非表示 --}}

                <a href="{{ route('product.list') }}">商品一覧</a>

                {{-- 設計書要件: 「新規会員登録」ボタンを表示 --}}
                <a href="{{ route('member.input') }}" class="header-button base-button primary-button">新規会員登録</a>

                {{-- 設計書要件: 「ログイン」ボタンを表示 --}}
                <a href="{{ route('login') }}" class="header-button base-button">ログイン</a>
            @endguest

        </div>
    </header>

    <div class="top-container">
        <h2></h2>
        <p></p>
    </div>

</body>
</html>