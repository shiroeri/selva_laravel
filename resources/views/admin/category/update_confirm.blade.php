@php
// 編集確認画面用の設定
$pageTitle = '商品カテゴリ編集確認';
$isEdit = true; // 編集モードを有効にする
$routePrefix = 'admin.category';

// コントローラーから渡される以下の変数を前提とします。
// 1. $input: セッションから取得した入力データ (変更後のデータ)
// 2. $category: データベースから取得した編集対象のCategoryインスタンス (IDの表示などに使用)

// 仮に $input が未定義の場合は、空の配列を渡すなどの処理をここに追加してください。
// 例: $input = $input ?? [];
// 例: $category が未定義の場合はエラーになる可能性があるため、適切に処理してください。


@endphp

{{-- 共通テンプレートを読み込み、上記の変数を引き継ぐ --}}
{{-- 共通ファイルは /resources/views/admin/registrationEditCommon/categoryConfirm.blade.php を使用 --}}
@include('admin.registrationEditCommon.categoryConfirm', [
'pageTitle' => $pageTitle,
'isEdit' => $isEdit,
'routePrefix' => $routePrefix,
'input' => $input,
// 編集時には、ID表示のために必ず Category インスタンスが必要
'category' => $category,
])