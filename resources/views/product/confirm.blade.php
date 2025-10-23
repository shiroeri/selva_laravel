<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録確認</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        /* シンプルな基本スタイル */
        body { font-family: Arial, sans-serif; margin: 20px;}
        .container { max-width: 800px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; margin-bottom: 25px; color: #333; }
        
        /* 確認画面用のスタイル */
        .confirm-group { margin-bottom: 20px; padding: 15px; border-left: 5px solid #007bff; background-color: #f9f9f9; border-radius: 4px; }
        .confirm-group p { margin: 0; }
        .confirm-group .label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        .confirm-group .value { font-size: 1.1em; white-space: pre-wrap; word-wrap: break-word; }
        
        /* ボタンエリア */
        .button-group { margin-top: 30px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; font-size: 1em; text-decoration: none; display: inline-block; text-align: center; }
        .btn-primary { background-color: #28a745; color: white; } /* 登録ボタンは緑色に */
        .btn-primary:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; color: white; } /* 修正ボタンは灰色に */
        
        /* 画像プレビュー */
        #image-previews {
            display: flex;
            flex-direction: column; 
            gap: 20px; 
            margin-top: 10px;
        }
        
        /* 各写真のコンテナ */
        .image-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .image-label {
            font-weight: bold;
            color: #333;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 3px;
        }

        .preview-box {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #eee;
        }
        .preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* 画像なしの場合の表示スタイル */
        .no-image {
            color: #999; 
            font-style: italic; 
            margin: 0;
            padding-left: 10px; /* 見た目の調整 */
        }
        
        /* フォームボタンを横並びにするためのスタイル */
        .inline-form { display: inline; }
    </style>
</head>
<body>
<div class="container">
    <h1>商品登録確認画面</h1>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ------------------------------------------------ --}}
    {{-- 1. 商品名 --}}
    {{-- ------------------------------------------------ --}}
    <div class="confirm-group">
        <span class="label">商品名</span>
        <p class="value">{{ $input['name'] }}</p>
    </div>

    {{-- ------------------------------------------------ --}}
    {{-- 2. カテゴリ --}}
    {{-- ------------------------------------------------ --}}
    <div class="confirm-group">
        <span class="label">商品カテゴリ</span>
        <p class="value">{{ $categoryName }} > {{ $subcategoryName }}</p>
    </div>

    {{-- ------------------------------------------------ --}}
    {{-- 3. 写真 --}}
    {{-- ------------------------------------------------ --}}
    <div class="confirm-group">
        <span class="label">写真</span>
        <div id="image-previews">
            
            @php
                $imageKeys = ['image_1', 'image_2', 'image_3', 'image_4'];
                $hasAnyImage = false;
            @endphp
            
            {{-- 各写真ごとに「写真N」のラベルと画像を表示 --}}
            @foreach ($imageKeys as $index => $key)
                @php
                    $number = $index + 1;
                    // 【修正点１】Base64データではなく、コントローラから渡された公開URLを取得する
                    $imageUrl = $imageData[$key]['url'] ?? null; 
                @endphp
                <div class="image-item">
                    {{-- 写真N のラベル --}}
                    <span class="image-label">写真 {{ $number }}</span>
                    
                    {{-- 【修正点２】URLが存在する場合のみ画像を表示する --}}
                    @if (!empty($imageUrl))
                        @php
                            $hasAnyImage = true; // 画像が1枚でもあることを記録
                        @endphp
                        <div class="preview-box" title="{{ $key }}">
                            {{-- URLを直接src属性に指定する --}}
                            <img src="{{ $imageUrl }}" alt="写真 {{ $number }} プレビュー">
                        </div>
                    @else
                        {{-- 画像がない場合はプレースホルダーを表示 --}}
                        <p class="no-image">登録なし</p>
                    @endif
                </div>
            @endforeach

            {{-- 全ての写真が登録されていない場合の全体メッセージ --}}
            @if (!$hasAnyImage)
                <p class="value" style="font-style: italic; color: #999;">写真の登録はありません。</p>
            @endif
        </div>
    </div>

    {{-- ------------------------------------------------ --}}
    {{-- 4. 商品説明 --}}
    {{-- ------------------------------------------------ --}}
    <div class="confirm-group">
        <span class="label">商品説明</span>
        {{-- 改行を反映させるために nl2br を使用 --}}
        <p class="value">{!! nl2br(e($input['product_content'])) !!}</p>
    </div>

    {{-- ------------------------------------------------ --}}
    {{-- フォームアクション --}}
    {{-- ------------------------------------------------ --}}
    <div class="button-group">
        
        {{-- 登録実行ボタン: セッションのデータを使ってDBに保存 (product.store) --}}
        {{-- ★修正: IDを追加しました。 --}}
        <form action="{{ route('product.store') }}" method="POST" id="product-store-form">
            @csrf
            {{-- このフォームにはセッションID以外のデータは含めません --}}
            <button type="submit" class="btn btn-primary" id="submit-button">商品を登録する</button>
        </form>

        {{-- 「前に戻る」ボタンを入力値をPOSTする専用フォームに変更（ここは問題ないが、渡すデータを調整） --}}
        <form action="{{ route('product.create') }}" method="POST" class="inline-form">
            @csrf
            
            {{-- 1. テキスト入力値を Hidden フィールドとしてすべて含める --}}
            @foreach ($input as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            {{-- 【修正点３】画像データではなく、一時ファイルのパスと拡張子をHiddenフィールドとして含める --}}
            @foreach ($imageData as $key => $data)
                @if ($data)
                    {{-- create.blade.phpで画像情報を再構築するために必要 --}}
                    <input type="hidden" name="{{ $key }}_temp_path_retained" value="{{ $data['path'] ?? '' }}">
                    <input type="hidden" name="{{ $key }}_ext_retained" value="{{ $data['extension'] ?? '' }}">
                @endif
            @endforeach

            <button type="submit" class="btn btn-secondary">前に戻る</button>
        </form>
        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 登録フォーム（最初のフォーム）のみを対象とする
        // document.getElementById で ID からフォームを取得するように変更
        const storeForm = document.getElementById('product-store-form');
        const submitButton = document.getElementById('submit-button');

        // --- 二重送信防止処理 ---
        if (storeForm) {
            storeForm.addEventListener('submit', function() {
                // 登録ボタンと、もしあれば「前に戻る」ボタンも無効化する
                document.querySelectorAll('.btn').forEach(btn => {
                    btn.disabled = true;
                });
                submitButton.textContent = '登録処理中...';
            });
        }
    });
</script>
</body>
</html>
