<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* シンプルな基本スタイル */
        body { font-family: 'Inter', Arial, sans-serif; margin: 20px;}
        .container { max-width: 800px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        h1 { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 30px; font-size: 1.8em; }
        .form-group { margin-bottom: 20px; padding: 0 10px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #374151; }
        input[type="text"], textarea, select {
            width: 100%; padding: 10px; border: 1px solid #d1d5db; box-sizing: border-box; border-radius: 6px; transition: border-color 0.2s;
        }
        input[type="text"]:focus, textarea:focus, select:focus { border-color: #3b82f6; outline: none; }
        
        .alert-danger { color: #991b1b; background-color: #fee2e2; border: 1px solid #fca5a5; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .btn { padding: 12px 25px; border: none; cursor: pointer; border-radius: 6px; margin-right: 15px; transition: background-color 0.3s, transform 0.1s; font-weight: 600; }
        .btn:active { transform: scale(0.98); }
        .btn-primary { background-color: #10b981; color: white; box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3); } 
        .btn-primary:hover { background-color: #059669; }
        .btn-secondary { background-color: #6b7280; color: white; }
        .btn-secondary:hover { background-color: #4b5563; }
        
        .category-group { display: flex; gap: 20px; padding: 0 10px; }
        .category-group .form-group { flex: 1; padding: 0; }
        .category-group .form-group:first-child { min-width: 50%; } 
        
        /* --- 画像アップロード用のスタイル --- */
        .image-container { display: flex; flex-direction: column; gap: 25px; }
        .image-upload-group { 
            display: flex; 
            align-items: flex-start; 
            gap: 20px; 
            padding: 10px 0; 
            border-bottom: 1px dashed #e5e7eb;
        }
        .image-upload-group:last-child { border-bottom: none; }
        
        .image-info { width: 80px; font-weight: bold; margin-top: 5px; color: #374151; }
        .upload-area { display: flex; align-items: flex-start; gap: 20px; }
        
        .preview-box {
            width: 100px; /* サイズを少し小さく */
            height: 100px;
            border: 2px dashed #9ca3af;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #f9fafb;
            border-radius: 6px;
            flex-shrink: 0;
            position: relative;
        }
        .preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
        }
        .preview-box span {
            color: #9ca3af;
            font-size: 0.7em;
            text-align: center;
        }

        .upload-controls {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 5px;
        }
        
        /* カスタムアップロードボタンのスタイル */
        .btn-upload {
            background-color: #3b82f6;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background-color 0.2s;
        }
        .btn-upload:hover { background-color: #2563eb; }
        
        .btn-clear {
            background-color: #ef4444;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background-color 0.2s;
        }
        .btn-clear:hover { background-color: #dc2626; }

        .image-status {
            font-size: 0.8em;
            color: #10b981;
            margin-top: 5px;
            min-height: 1.5em; /* ロード中アニメーション用 */
        }
        .error-message {
            color: #ef4444;
            font-size: 0.8em;
            margin-top: 5px;
            min-height: 1.5em; /* エラー表示用 */
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>商品登録</h1>

    {{-- enctype="multipart/form-data" は削除 (ファイル送信はAjaxで行うため) --}}
    <form id="product-form" action="{{ route('product.confirm') }}" method="POST">
        @csrf

        {{-- ------------------------------------------------ --}}
        {{-- 1. 商品名 (必須, 50文字以内) --}}
        {{-- ------------------------------------------------ --}}
        <div class="form-group">
            <label for="name">商品名</label>
            <input type="text" id="name" name="name" 
                   value="{{ old('name') }}">
            @error('name') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        <div class="category-group">
        {{-- ------------------------------------------------ --}}
        {{-- 2. 大カテゴリ (必須) --}}
        {{-- ------------------------------------------------ --}}
        <div class="form-group">
            <label for="product_category_id">商品カテゴリ</label>
            <select id="product_category_id" name="product_category_id" data-old-subcategory="{{ old('product_subcategory_id') }}">
                <option value="">選択してください</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ (int)old('product_category_id') === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('product_category_id') <span class="error-message">{{ $message }}</span> @enderror
        </div>

        {{-- ------------------------------------------------ --}}
        {{-- 3. 小カテゴリ (必須) --}}
        {{-- ------------------------------------------------ --}}
        <div class="form-group">
            <label for="product_subcategory_id" style="color:transparent;">小カテゴリ</label>
            <select id="product_subcategory_id" name="product_subcategory_id" disabled>
                <option value="">大カテゴリを選択してください</option>
            </select>
            @error('product_subcategory_id') <span class="error-message">{{ $message }}</span> @enderror
        </div>
        </div>


        {{-- ------------------------------------------------ --}}
        {{-- 4. 写真 (4枚まで任意) - Ajax実装 --}}
        {{-- ------------------------------------------------ --}}
        <div class="form-group">
            <label>商品写真</label>
            <div class="image-container">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="image-upload-group">
                        
                        <div class="image-info">写真 {{ $i }}</div>
                        
                        <div class="upload-area">
                            {{-- プレビューボックスとステータス --}}
                            <div class="preview-box" data-index="{{ $i }}">
                                {{-- ここに初期のプレースホルダーを置いておく（JSで書き換えられる） --}}
                                <span>写真 {{ $i }}</span>
                            </div>

                            <div class="upload-controls">
                                {{-- アップロードボタン (カスタムボタン) --}}
                                <button type="button" class="btn-upload" data-file-target="image_{{ $i }}">
                                    アップロード
                                </button>
                                {{-- クリアボタン (一時パスのクリア用) --}}
                                <button type="button" class="btn-clear" data-clear-target="image_{{ $i }}" style="display:none;">
                                    クリア
                                </button>

                                <div class="image-status" data-status-for="{{ $i }}"></div>
                                <div class="error-message" data-error-for="{{ $i }}"></div>
                            </div>
                        </div>
                        
                        {{-- 実際のファイル入力 (非表示) - Ajax送信時に使用 --}}
                        <input type="file" id="image_{{ $i }}" 
                            class="image-input" style="display: none;">
                        
                        {{-- ★ Ajaxでアップロードされた一時ファイルのパスを格納する Hidden フィールド ★ --}}
                        <input type="hidden" name="image_{{ $i }}_temp_path" id="path_image_{{ $i }}" value="">

                        {{-- ★ 拡張子を格納する Hidden フィールド (永続保存時に必要) ★ --}}
                        <input type="hidden" name="image_{{ $i }}_ext" id="ext_image_{{ $i }}" value="">

                        @error('image_'.$i.'_temp_path') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                @endfor
            </div>
        </div>

        {{-- ------------------------------------------------ --}}
        {{-- 5. 商品説明 (必須, テキストエリア) --}}
        {{-- ------------------------------------------------ --}}
        <div class="form-group">
            <label for="product_content">商品説明</label>
            <textarea id="product_content" name="product_content" rows="5">{{ old('product_content') }}</textarea>
            @error('product_content') <span class="error-message">{{ $message }}</span> @enderror
        </div>
        
        <div style="margin-top: 30px; margin-bottom: 50px; padding: 0 10px;">
            {{-- 確認画面へ遷移するボタン --}}
            <button type="submit" class="btn btn-primary" id="submit-button">確認画面へ</button>

            @if (isset($source) && $source === 'list')
                <!-- 商品一覧 (product.list) から遷移してきた場合に表示 -->
                <a href="{{ route('product.list') }}" class="btn btn-secondary">商品一覧に戻る</a>
            @else
                <!-- トップ (top) またはその他の画面から遷移してきた場合に表示 -->
                <a href="{{ route('top') }}" class="btn btn-secondary">トップに戻る</a>
            @endif
        </div>
    </form>
</div>

{{-- 初期画像データ (一時ファイル情報) を渡すための要素 --}}
<div id="initial-image-data" data-initial-images='@json($initialImageData ?? [])'></div> 

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('product_category_id');
        const subcategorySelect = document.getElementById('product_subcategory_id');
        const oldSubcategoryId = categorySelect.getAttribute('data-old-subcategory');

        const form = document.getElementById('product-form');
        const submitButton = document.getElementById('submit-button');
        const imageInputs = document.querySelectorAll('.image-input');
        const uploadButtons = document.querySelectorAll('.btn-upload');
        const clearButtons = document.querySelectorAll('.btn-clear');
        
        // CSRFトークンを取得
        const csrfToken = document.querySelector('input[name="_token"]').value;
        
        // --- データ復元用の定義 ---
        const initialDataElement = document.getElementById('initial-image-data');
        const initialImageData = initialDataElement ? JSON.parse(initialDataElement.dataset.initialImages) : {};


        // --- Ajaxによる小カテゴリ連動処理 (変更なし) ---
        function updateSubcategories(categoryId, isInitialLoad = false) {
            
            subcategorySelect.innerHTML = '<option value="">' + (categoryId ? '読み込み中...' : '大カテゴリを選択してください') + '</option>';
            subcategorySelect.disabled = true;

            if (!categoryId) {
                return;
            }

            fetch(`{{ route('api.subcategories') }}?category_id=${categoryId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(subcategories => {
                    subcategorySelect.innerHTML = '<option value="">選択してください</option>';
                    
                    subcategories.forEach(subcategory => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        
                        if (isInitialLoad && oldSubcategoryId && subcategory.id == oldSubcategoryId) {
                            option.selected = true;
                        }

                        subcategorySelect.appendChild(option);
                    });
                    
                    subcategorySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Failed to fetch subcategories:', error);
                    subcategorySelect.innerHTML = '<option value="">読み込みエラー</option>';
                    subcategorySelect.disabled = true;
                });
        }

        categorySelect.addEventListener('change', function () {
            updateSubcategories(this.value, false);
        });

        if (categorySelect.value) {
            updateSubcategories(categorySelect.value, true);
        }


        // --- 画像アップロード/クリア関連ロジック ---

        /**
         * プレビューボックスとHiddenフィールドを更新する
         * @param {number} index - 画像のインデックス (1-4)
         * @param {string|null} url - プレビューに使う画像のURL (一時ファイルのURL)
         * @param {string|null} path - フォーム送信用の Hidden パス (tmp/...)
         * @param {string|null} ext - フォーム送信用の Hidden 拡張子
         */
        function updateImageDisplay(index, url = null, path = null, ext = null) {
            const previewBox = document.querySelector(`.preview-box[data-index="${index}"]`);
            const pathInput = document.getElementById(`path_image_${index}`);
            const extInput = document.getElementById(`ext_image_${index}`);
            const clearBtn = document.querySelector(`.btn-clear[data-clear-target="image_${index}"]`);

            if (!previewBox) return;

            // プレビューボックスの内容をクリア
            previewBox.innerHTML = ''; 
            previewBox.style.borderStyle = path ? 'solid' : 'dashed'; // 画像があれば実線に

            if (url) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = `写真 ${index} プレビュー`;
                // 画像が見つからなかった場合のフォールバック
                img.onerror = function() {
                    this.parentElement.innerHTML = '<span>読み込みエラー</span>';
                    this.parentElement.style.borderStyle = 'dashed';
                    console.error(`Error loading image URL: ${url}`);
                };
                previewBox.appendChild(img);
                clearBtn.style.display = 'block'; // 画像があればクリアボタン表示
            } else {
                const placeholder = document.createElement('span');
                placeholder.textContent = `写真 ${index}`;
                previewBox.appendChild(placeholder);
                clearBtn.style.display = 'none'; // 画像がなければクリアボタン非表示
            }

            // Hidden フィールドの更新
            pathInput.value = path || '';
            extInput.value = ext || '';

            // プレビューが更新されたら、エラーとステータスをクリア（アップロード関数とは別に）
            // アップロードが失敗した場合はエラー表示は残すため、ここではステータスのみ操作
            const statusDiv = document.querySelector(`[data-status-for="${index}"]`);
            if (statusDiv.textContent === 'アップロード完了') {
                 // 成功メッセージは残す
            } else {
                 statusDiv.textContent = '';
            }
        }

        /**
         * Ajaxでファイルをサーバーに送信する
         */
        async function uploadFile(file, index) {
            const statusDiv = document.querySelector(`[data-status-for="${index}"]`);
            const errorDiv = document.querySelector(`[data-error-for="${index}"]`);
            const previewBox = document.querySelector(`.preview-box[data-index="${index}"]`);

            // 状態をリセット
            errorDiv.textContent = ''; // エラーをクリア
            statusDiv.innerHTML = '';
            statusDiv.appendChild(document.createTextNode('アップロード中... '));
            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            statusDiv.appendChild(spinner);

            // 一時的にプレビューをローディングアニメーションに置き換え
            previewBox.innerHTML = `<div class="loading-spinner" style="width: 50px; height: 50px;"></div>`;
            previewBox.style.borderStyle = 'solid';

            const formData = new FormData();
            formData.append('image_file', file);
            formData.append('image_index', index);
            formData.append('_token', csrfToken); // CSRFトークンをFormDataに追加

            try {
                // 成功または失敗のレスポンスを受け取るまで再試行 (Exponential backoff)
                let response = null;
                const maxRetries = 3;
                let delay = 1000;

                for (let attempt = 0; attempt < maxRetries; attempt++) {
                    response = await fetch(`{{ route('api.product.upload_image') }}`, {
                        method: 'POST',
                        body: formData,
                    });

                    // 413, 200, 422の場合は再試行せず終了
                    if (response.status === 413 || response.ok || response.status === 422) {
                        break;
                    }
                    
                    // 500などのサーバーエラーの場合は待機して再試行
                    await new Promise(resolve => setTimeout(resolve, delay));
                    delay *= 2; // Exponential backoff
                }

                if (!response) throw new Error('ネットワークエラー: サーバーからの応答がありません');
                
                // 【★修正ポイント１：413エラーをここでキャッチする★】
                if (response.status === 413) {
                    statusDiv.textContent = '';
                    // 413はサーバー設定によるサイズエラーなので、ここでメッセージを確定させる
                    errorDiv.textContent = 'ファイルサイズが大きすぎます。10MB以下のファイルをアップロードしてください。';
                    updateImageDisplay(index);
                    return; // 処理を終了
                }

                // 413エラーではない場合、JSONパースを試みる
                const data = await response.json(); 

                if (response.ok) {
                    // 成功 (HTTP 200)
                    statusDiv.textContent = 'アップロード完了'; // 成功メッセージを明確に
                    errorDiv.textContent = '';
                    // プレビューと Hidden フィールドを更新
                    updateImageDisplay(index, data.url, data.path, data.extension); 
                } else if (response.status === 422) {
                    // バリデーションエラー (422 Unprocessable Entity)
                    
                    // ステータス表示をクリア
                    statusDiv.textContent = '';
                    
                    // エラーメッセージの取得ロジックを強化
                    let errorMessage = data.message; 
                    
                    if (!errorMessage && data.errors && data.errors.image_file && data.errors.image_file[0]) {
                        errorMessage = data.errors.image_file[0];
                    }

                    if (!errorMessage) {
                         errorMessage = 'アップロードされたファイルが無効です。';
                    }
                    
                    // エラーメッセージを専用のdivに表示
                    errorDiv.textContent = errorMessage;
                    
                    // プレビューと Hidden フィールドをリセット
                    updateImageDisplay(index);
                } else {
                    // その他のサーバーエラー (500など)
                    statusDiv.textContent = '';
                    errorDiv.textContent = data.message || `アップロード中に予期せぬエラーが発生しました (ステータス: ${response.status})。`;
                    updateImageDisplay(index);
                }
            } catch (error) {
                // JSONパースの失敗（413で空ボディの場合もここに来る可能性があった）や、ネットワーク接続エラー
                console.error('Upload failed:', error);
                statusDiv.textContent = '';
                // 413は上で処理されたため、ここではより一般的なエラーを表示
                errorDiv.textContent = 'エラー: サーバーとの通信、またはデータの解析に失敗しました。';
                updateImageDisplay(index);
            }
        }
        
        // --- イベントリスナー設定 ---
        
        // 1. カスタムアップロードボタン => ファイル入力のクリック
        uploadButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-file-target');
                const fileInput = document.getElementById(targetId);
                fileInput.click();
            });
        });

        // 2. ファイル入力の変更 => Ajaxアップロード実行
        imageInputs.forEach(input => {
            input.addEventListener('change', function() {
                const index = this.id.slice(-1);
                const file = this.files[0];
                
                if (file) {
                    // ファイルが選択された場合のみアップロード
                    uploadFile(file, index);
                } 
            });
        });

        // 3. クリアボタン => プレビューとHiddenフィールドのリセット
        clearButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-clear-target');
                const index = targetId.slice(-1);
                
                // ステータスやエラー表示をクリア
                document.querySelector(`[data-status-for="${index}"]`).textContent = '';
                document.querySelector(`[data-error-for="${index}"]`).textContent = '';

                // プレビューとHiddenフィールドをリセット
                updateImageDisplay(index, null, null, null);

                // initialImageDataからも削除（メモリ上のデータをきれいにする）
                delete initialImageData[`image_${index}`];
            });
        });


        // --- 復元ロジック (create画面読み込み時) ---
        function restoreImagePreviews() {
            for (let i = 1; i <= 4; i++) {
                const key = `image_${i}`;
                const imageDetail = initialImageData[key]; 
                
                if (imageDetail && imageDetail.url && imageDetail.path) {
                    document.getElementById(`path_image_${i}`).value = imageDetail.path;
                    document.getElementById(`ext_image_${i}`).value = imageDetail.extension;
                    
                    updateImageDisplay(i, imageDetail.url, imageDetail.path, imageDetail.extension);
                    
                    document.querySelector(`[data-status-for="${i}"]`).textContent = '';
                    
                } else {
                    document.getElementById(`path_image_${i}`).value = '';
                    document.getElementById(`ext_image_${i}`).value = '';
                    updateImageDisplay(i);
                }
            }
        }
        
        // 実行
        restoreImagePreviews();


        // --- 二重送信防止処理 (フォーム全体) ---
        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.textContent = '確認処理中...';
        });
    });
</script>
</body>
</html>
