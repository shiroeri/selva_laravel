<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細: {{ $product->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Interフォントと基本的な背景色を設定 */
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            padding: 30px 0;
        }
        /* コンテナのスタイル設定 */
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            background-color: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); 
            /* ★修正点1: 内部のabsolute要素の基準にする */
            position: relative; 
        }
        /* タイトルのスタイル */
        h1 { 
            color: #1f2937; 
            font-size: 2.2em; 
            font-weight: 700;
            /* ★修正点2: ボタンと重ならないようにマージンを調整 */
            margin-bottom: 25px;
            /* 以前のヘッダー要素のスペースを維持しつつ、ボタン配置のために調整 */
            padding-bottom: 10px; 
        }
        /* 情報行のラベルとコンテンツのスタイル */
        .info-label {
            font-weight: 600;
            color: #4b5563;
            width: 120px;
            flex-shrink: 0;
        }
        .info-content {
            color: #1f2937;
            font-weight: 400;
        }
        .info-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        /* 商品説明ボックスのスタイル */
        .description-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
            white-space: pre-wrap; /* 改行を保持 */
            min-height: 100px;
        }
        /* ボタン群のスタイル */
        .btn-group {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 15px;
        }
        .btn { 
            padding: 12px 25px; 
            border: none; 
            cursor: pointer; 
            border-radius: 8px; 
            transition: background-color 0.3s, transform 0.1s; 
            font-weight: 600; 
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-back { 
            background-color: #6366f1; /* Indigo */
            color: white; 
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
        }
        .btn-back:hover { 
            background-color: #4f46e5;
            transform: translateY(-1px);
        }
        .btn-top { 
            background-color: #10b981; /* Tealに変更 */
            color: white;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }
        .btn-top:hover { 
            background-color: #059669;
            transform: translateY(-1px);
        }

        /* 画像ギャラリーのスタイル */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .image-box {
            width: 100%;
            padding-top: 100%; /* 1:1 のアスペクト比を維持 */
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #e5e7eb; /* 画像がない場合の背景 */
        }
        .image-box img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }
        .image-box:hover img {
            transform: scale(1.05);
        }

        /* ★追加★ コンテナ内の右上に配置するスタイル */
        .absolute-top-right {
            position: absolute;
            top: 20px; /* .container の上端からの距離（padding 40px より内側） */
            right: 20px; /* .container の右端からの距離 */
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="container">
        
        {{-- ★配置変更★ 画面固定を解除し、container内の右上に配置 --}}
        <a href="{{ route('top') }}" class="btn btn-top absolute-top-right">
            トップに戻る
        </a>

        <header class="mb-6">
            {{-- h1タグの境界線がボタンと重なるため、ボタンの下に配置する --}}
            <h1>商品詳細</h1>
        </header>

        <section class="product-info">
            {{-- カテゴリ情報 --}}
            <div class="info-row">
                <!-- <div class="info-label">商品カテゴリ</div> -->
                <div class="info-content">
                    {{-- 大カテゴリ --}}
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                        {{ $product->category->name ?? '---' }}
                    </span>
                    <span class="mx-1">></span>
                    {{-- 小カテゴリ --}}
                    <span class="bg-indigo-100 text-indigo-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                        {{ $product->subcategory->name ?? '---' }}
                    </span>
                   
                </div>
            </div>
        
            {{-- 商品名 --}}
            <div class="info-row">
                <!-- <div class="info-label">商品名</div> -->
                <div class="info-content font-bold text-xl">{{ $product->name }}</div>
            </div>

            {{-- 登録日・更新日 --}}
             <div class="mt-6 text-sm text-gray-500 flex justify-between">
                <div>更新日時: {{ $product->updated_at->format('Y/m/d H:i') }}</div>
            </div>

            
            
            {{-- 商品写真 (最大4枚) --}}
            <!-- <h2 class="text-xl font-semibold mt-8 mb-4 text-gray-700">商品写真</h2> -->
            <div class="image-gallery">
                @for ($i = 1; $i <= 4; $i++)
                    @php
                        // DBに保存されている画像パスを取得（例: products/1_16788...jpg）
                        $image_path = $product->{'image_' . $i};
                    @endphp
                    @if ($image_path)
                        <div class="image-box">
                            {{-- asset('storage/...') で公開URLを生成 --}}
                            <img src="{{ asset('storage/' . $image_path) }}" 
                                 alt="商品写真 {{ $i }}"
                                 onerror="this.onerror=null; this.src='https://placehold.co/150x150/cccccc/333333?text=画像なし'">
                        </div>
                    @else
                        {{-- 画像がない場合は「画像なし」を表示 --}}
                        <div class="image-box flex items-center justify-center text-sm text-gray-500">
                            画像なし
                        </div>
                    @endif
                @endfor
            </div>

            {{-- 商品説明 --}}
            <h2 class="text-xl font-semibold mt-8 mb-4 text-gray-700">商品説明</h2>
            <div class="description-box">{{ $product->product_content }}</div>
            
        </section>

        {{-- ボタン群 --}}
        <div class="btn-group">
            @php
                // Sessionから、ProductController@showDetailで保存した一覧画面へ戻るべきページ番号を取得
                $sourcePage = session('product_list_source_page', 1);
                
                // 戻り先のURLを生成。
                $backUrl = route('product.list', ['page' => $sourcePage]);
            @endphp
            
            {{-- 「商品一覧に戻る」ボタン: 元のページに戻る --}}
            <a href="{{ $backUrl }}" class="btn btn-back">
                商品一覧に戻る
            </a>
            
            {{-- コンテナ下部の「トップに戻る」ボタンは、上部のボタンが目立つため、削除しました。 --}}
        </div>
    </div>
</body>
</html>
