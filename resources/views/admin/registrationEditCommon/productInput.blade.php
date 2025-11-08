@php
    // --- 変数初期化の堅牢化 ---
    $product       = $product ?? null;         // 既存の製品データ (編集時のみ)
    $input         = $input ?? [];             // 確認画面からの戻りなどで使用するセッションデータ
    $isEdit        = $isEdit ?? (bool)$product; // 編集モードかどうか
    $categories    = $categories ?? [];        // 大カテゴリリスト
    $subcategories = $subcategories ?? [];     // 小カテゴリリスト
    $members       = $members ?? [];           // 会員リスト
    $clearFlags    = $clearFlags ?? [];        // 画像削除フラグ(編集時)

    // フォームが利用するDBの初期値データ（存在しない場合はnullで初期化）
    $initialProductData = $product ?? (object)[
        'id' => null,
        'name' => null,
        'member_id' => null,
        'product_category_id' => null,
        'product_subcategory_id' => null,
        'image_1' => null,
        'image_2' => null,
        'image_3' => null,
        'image_4' => null,
        'product_content' => null,
    ];

    // DBデータとセッションデータ（old() or $input）をマージし、表示用データを生成
    $formData = array_merge((array)$initialProductData, is_array($input) ? $input : []);
    $formDataObject = (object)$formData;

    // 画像パス（既存のDBデータ）
    $dbImagePaths = [
        'image_1' => $initialProductData->image_1 ?? '',
        'image_2' => $initialProductData->image_2 ?? '',
        'image_3' => $initialProductData->image_3 ?? '',
        'image_4' => $initialProductData->image_4 ?? '',
    ];

    // ルーティング設定
    $productId   = $initialProductData->id;
    $routePrefix = $routePrefix ?? 'admin.product';

    // 送信先: 新規は confirm、編集は updateConfirm
    $formAction = ($isEdit)
        ? route($routePrefix . '.updateConfirm', ['product' => $productId])
        : route($routePrefix . '.confirm');

    // Ajaxアップロード先
    $ajaxUploadRoute = route('admin.product.ajax_upload_image');

    // タイトル/戻る
    $pageTitle = $pageTitle ?? ($isEdit ? '商品編集' : '商品登録');
    $backLink  = $backLink  ?? route($routePrefix . '.index');
@endphp

@php \Illuminate\Support\Facades\Log::error('### BLADE FILE IS EXECUTING SUCCESSFULLY ###'); @endphp

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? '商品フォーム' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .image-preview-container { width:150px;height:150px;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f7f7f7;margin-bottom:8px;border-radius:8px;position:relative; }
        .image-preview { max-width:100%;max-height:100%;object-fit:contain; }
        .upload-button { display:inline-block;cursor:pointer;padding:6px 12px;background:#f0f0f0;border:1px solid #ccc;border-radius:4px;font-size:14px;transition:background-color .2s; }
        .upload-button:hover { background:#e0e0e0; }
        .file-input { display:none; }
        .loading-spinner { border:4px solid rgba(0,0,0,.1);border-left-color:#3b82f6;border-radius:50%;width:30px;height:30px;animation:spin 1s linear infinite;position:absolute;z-index:10; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .form-row { border-bottom:1px solid #e5e7eb;padding-bottom:1rem; }
        .form-row:last-child { border-bottom:none; }
        select:disabled { background:#f3f4f6;cursor:not-allowed; }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex items-start justify-center pt-12 pb-12">

<div class="space-y-6 max-w-4xl mx-auto p-0 bg-white shadow-2xl rounded-xl w-full overflow-hidden">
    <div class="bg-gray-100 p-6 flex items-center justify-between border-b border-gray-200">
        <h2 class="text-3xl font-extrabold text-gray-800">{{ $pageTitle }}</h2>
        @if(isset($backLink))
            <a href="{{ $backLink }}" class="px-5 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 shadow-md">
                一覧へ戻る
            </a>
        @endif
    </div>

    <div class="p-8">
        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @if($isEdit)
                @method('PUT')
                <input type="hidden" name="id" value="{{ $initialProductData->id }}">
            @endif

            <div class="space-y-6">
                {{-- ID --}}
                <div class="flex items-center pt-4 form-row">
                    <label class="w-1/4 text-base font-medium text-gray-700">ID</label>
                    <div class="w-3/4 text-base text-gray-900 font-mono">
                        @if($isEdit) {{ $initialProductData->id }} @else 登録後に自動採番 @endif
                    </div>
                </div>

                {{-- 会員 --}}
                <div class="flex items-center pt-4 form-row">
                    <label for="member_id" class="w-1/4 text-base font-medium text-gray-700">会員</label>
                    <div class="w-3/4">
                        <select name="member_id" id="member_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">選択してください</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}"
                                        {{ old('member_id', $formDataObject->member_id ?? '') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name_sei }} {{ $member->name_mei }}
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- 商品名 --}}
                <div class="flex items-center pt-4 form-row">
                    <label for="name" class="w-1/4 text-base font-medium text-gray-700">商品名</label>
                    <div class="w-3/4">
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $formDataObject->name ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- 商品カテゴリ --}}
                <div class="flex items-start pt-4 form-row">
                    <label class="w-1/4 text-base font-medium text-gray-700 pt-2">商品カテゴリ</label>
                    <div class="w-3/4 flex space-x-6">
                        <div class="flex-1">
                            <select name="product_category_id" id="product_category_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">選択してください</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            {{ old('product_category_id', $formDataObject->product_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex-1">
                            <select name="product_subcategory_id" id="product_subcategory_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">選択してください</option>
                                @foreach ($subcategories as $subcat)
                                    <option value="{{ $subcat->id }}"
                                            data-parent-id="{{ $subcat->product_category_id ?? $subcat->parent_id }}"
                                            {{ old('product_subcategory_id', $formDataObject->product_subcategory_id ?? '') == $subcat->id ? 'selected' : '' }}>
                                        {{ $subcat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_subcategory_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <br>
                <label class="w-1/4 text-base font-medium text-gray-700 pt-2">商品写真</label>

                @for ($i = 1; $i <= 4; $i++)
                    @php
                        $imageFieldName = 'image_' . $i;
                        $dbPath = $initialProductData->$imageFieldName ?? '';
                        $isCleared = old($imageFieldName . '_clear', ($clearFlags[$imageFieldName] ?? null)) == '1';
                        $oldOrTempPath = old($imageFieldName, $formDataObject->$imageFieldName ?? '');
                        $displayPath = $oldOrTempPath;
                        if ($isEdit && empty($oldOrTempPath) && !empty($dbPath)) { $displayPath = $dbPath; }

                        $relativePathForWeb = null;
                        $imageUrl = null;

                        if (!empty($displayPath)) {
                            if (\Illuminate\Support\Str::contains($displayPath, 'storage/app/public/')) {
                                $relativePathForWeb = \Illuminate\Support\Str::after($displayPath, 'public/');
                            } else {
                                $relativePathForWeb = $displayPath;
                            }
                            $relativePathForWeb = ltrim($relativePathForWeb, '/');
                            $imageUrl = asset('storage/' . $relativePathForWeb);
                        }

                        \Illuminate\Support\Facades\Log::info("--- Image Debug ($imageFieldName) ---");
                        \Illuminate\Support\Facades\Log::info("1. DB Path: {$dbPath}");
                        \Illuminate\Support\Facades\Log::info("2. Display Path (Form Value): {$displayPath}");
                        \Illuminate\Support\Facades\Log::info("3. Relative Web Path: {$relativePathForWeb}");
                        \Illuminate\Support\Facades\Log::info("4. Final Image URL: {$imageUrl}");
                        \Illuminate\Support\Facades\Log::info("----------------------------------");
                    @endphp
                    <div class="flex items-start pt-4 @if($i < 4) form-row @endif">
                        <label class="w-1/4 text-base font-medium text-gray-700 pt-2">写真 {{ $i }}</label>
                        <div class="w-3/4 flex flex-col items-start space-y-2">
                            <div class="image-preview-container" id="preview_{{ $i }}">
                                @if(!$isCleared && $relativePathForWeb)
                                    <img src="{{ $imageUrl }}" alt="画像 {{ $i }}" class="image-preview">
                                @else
                                    <span class="text-gray-400 text-sm">{{ $isCleared && ($isEdit) ? '削除予定' : '画像なし' }}</span>
                                @endif
                            </div>

                            <input type="file"
                                   name="{{ $imageFieldName }}_file"
                                   id="file_input_{{ $i }}"
                                   class="file-input"
                                   onchange='handleImageUpload(event, "preview_{{ $i }}", "{{ $imageFieldName }}", "{{ $ajaxUploadRoute }}")'>

                            <label for="file_input_{{ $i }}" class="upload-button">アップロード</label>

                            <input type="hidden" name="{{ $imageFieldName }}_path" id="{{ $imageFieldName }}_path" value="{{ $displayPath }}">

                            @if(($isEdit) && $dbPath)
                                <label class="flex items-center space-x-2 text-sm text-gray-600">
                                    <input type="checkbox" name="{{ $imageFieldName }}_clear" value="1"
                                           class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                           {{ old($imageFieldName . '_clear', ($clearFlags[$imageFieldName] ?? null)) == '1' ? 'checked' : '' }}>
                                    <span>この画像を削除する</span>
                                </label>
                            @endif

                            @error($imageFieldName)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                @endfor

                {{-- 商品説明 --}}
                <div class="flex items-start pt-4">
                    <label for="product_content" class="w-1/4 text-base font-medium text-gray-700 pt-2">商品説明</label>
                    <div class="w-3/4">
                        <textarea name="product_content" id="product_content" rows="5"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">{{ old('product_content', $formDataObject->product_content ?? '') }}</textarea>
                        @error('product_content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-center space-x-4">
                <button type="submit"
                        class="px-10 py-3 bg-blue-600 text-white font-bold text-lg rounded-xl shadow-xl hover:bg-blue-700 transition duration-150 transform hover:scale-105">
                    確認画面へ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * 大カテゴリ選択時の小カテゴリフィルタリングロジック
     */
    document.addEventListener('DOMContentLoaded', function () {
        const mainCatSelect = document.getElementById('product_category_id');
        const subCatSelect = document.getElementById('product_subcategory_id');
        // data-parent-id属性を持つ小カテゴリのオプションすべてを取得
        const subCatOptions = subCatSelect.querySelectorAll('option[data-parent-id]');

        function filterSubCategories(event) {
            const selectedMainCatId = mainCatSelect.value;
            let resetSubCatValue = false;

            // 大カテゴリが未選択の場合、小カテゴリを選択不可にする
            subCatSelect.disabled = (selectedMainCatId === '');

            // 最初の「選択してください」オプションを表示
            subCatSelect.querySelector('option[value=""]').style.display = 'block';

            subCatOptions.forEach(option => {
                const parentId = option.getAttribute('data-parent-id');
                if (selectedMainCatId === '' || parentId === selectedMainCatId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    if (option.selected) resetSubCatValue = true;
                }
            });

            if ((event && event.type === 'change') || resetSubCatValue) {
                subCatSelect.value = '';
            }
        }

        mainCatSelect.addEventListener('change', filterSubCategories);
        filterSubCategories(null);
    });

    /**
     * 画像ファイルの変更を検知し、ローカルプレビューとAjaxによる一時アップロードを行うロジック
     * @param {Event} event ファイル入力の change イベント
     * @param {string} previewId プレビューコンテナのID (例: 'preview_1')
     * @param {string} inputName 隠しフィールドの名前 (例: 'image_1')
     * @param {string} uploadRoute Ajaxアップロード先のルートURL (Bladeで生成済み)
     */
    function handleImageUpload(event, previewId, inputName, uploadRoute) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById(previewId);
        const pathInput = document.getElementById(inputName + '_path'); // 一時パスを保持する隠しフィールド

        // 以前の画像やテキストをクリア
        previewContainer.innerHTML = '';
        // 隠しフィールドの値をリセット
        pathInput.value = '';

        if (file) {
            // ローディングスピナーを表示
            const spinner = document.createElement('div');
            spinner.classList.add('loading-spinner');
            previewContainer.appendChild(spinner);

            // 即時ローカルプレビュー
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = '';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'New Image Preview';
                img.classList.add('image-preview');
                previewContainer.appendChild(img);
                previewContainer.appendChild(spinner);
            }
            reader.readAsDataURL(file);

            // Ajaxアップロード
            uploadFileViaAjax(file, uploadRoute, pathInput, previewContainer, spinner);

        } else {
            const textSpan = document.createElement('span');
            textSpan.classList.add('text-gray-400', 'text-sm');
            textSpan.textContent = '画像なし';
            previewContainer.appendChild(textSpan);
        }
    }

    /**
     * ファイルをAjaxでサーバーにアップロードし、一時ファイル名を隠しフィールドに設定
     * success=false の場合は HTTP 200 でも alert で通知してプレビューをエラー表示にする
     */
    function uploadFileViaAjax(file, uploadRoute, pathInput, previewContainer, spinner) {
        const formData = new FormData();
        formData.append('image_file', file);
        const imageIndex = pathInput.id.split('_')[1]; // 例: image_1_path -> 1
        formData.append('image_index', imageIndex);

        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);

        fetch(uploadRoute, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json().catch(() => ({})))
        .then(data => {
            // 成功判定（サーバーは success を必ず返す想定）
            if (!data || data.success === false) {
                const message = data && data.message ? data.message : 'アップロードに失敗しました。';
                alert(message); // ← 要件どおりアラートで通知
                showError(previewContainer, message);

                // ファイル入力と hidden をリセット
                const fileInput = document.getElementById(`file_input_${imageIndex}`);
                if (fileInput) fileInput.value = '';
                pathInput.value = '';
                return;
            }

            // success=true: 一時保存パスを保持
            if (data.path) {
                pathInput.value = data.path; // 例: tmp/xxxx.jpg
                // プレビューは既に表示済み（ローカル）。サーバーURLで置き換えたい場合は下記を利用
                // const img = previewContainer.querySelector('img.image-preview');
                // if (img && data.url) img.src = data.url;
            } else {
                alert('アップロード失敗: データ形式不正');
                showError(previewContainer, 'アップロード失敗 (データ形式不正)');
                const fileInput = document.getElementById(`file_input_${imageIndex}`);
                if (fileInput) fileInput.value = '';
                pathInput.value = '';
            }
        })
        .catch(err => {
            alert('サーバーエラーが発生しました。');
            showError(previewContainer, 'サーバーエラー');
            const fileInput = document.getElementById(`file_input_${imageIndex}`);
            if (fileInput) fileInput.value = '';
            pathInput.value = '';
        })
        .finally(() => {
            if (spinner && spinner.parentNode) {
                spinner.parentNode.removeChild(spinner);
            }
        });
    }

    /**
     * プレビューコンテナにエラーメッセージを表示
     */
    function showError(previewContainer, message) {
        previewContainer.innerHTML = '';
        const errorSpan = document.createElement('span');
        errorSpan.classList.add('text-red-500', 'text-center', 'text-sm', 'p-2');
        errorSpan.textContent = message;
        previewContainer.appendChild(errorSpan);
    }
</script>
</body>
</html>
