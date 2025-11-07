@php
// 新規登録確認画面用の設定
$pageTitle = '商品カテゴリ登録確認';
$isEdit = false;
$routePrefix = 'admin.category';

// コントローラーから渡される $input (入力データ) が
// 共通テンプレートで使用されることを前提とします。
// 仮に $input が未定義の場合は、空の配列を渡すなどの処理をここに追加してください。
// 例: $input = $input ?? [];
// 例: $category = $category ?? null; // 登録時はCategoryインスタンスは存在しない


@endphp

{{-- 共通テンプレートを読み込み、上記の変数を引き継ぐ --}}
@include('admin.registrationEditCommon.categoryConfirm', [
'pageTitle' => $pageTitle,
'isEdit' => $isEdit,
'routePrefix' => $routePrefix,
'input' => $input,
'category' => $category ?? null,
])