@php
// 新規登録用の設定
$pageTitle = '商品カテゴリ登録';
$isEdit = false;
$routePrefix = 'admin.category';

// 一覧画面へのリンクを設定（必要に応じてルート名を変更してください）
$backLink = route('admin.category.index');

// コントローラーから渡される可能性のある変数
// $input: 確認画面から戻った際のセッションデータ（古い入力値）
// $category: 新規登録時は null
$input = $input ?? [];
$category = $category ?? null;


@endphp

{{-- 共通テンプレートを読み込み、変数を渡す --}}
@include('admin.registrationEditCommon.categoryInput', [
'pageTitle' => $pageTitle,
'isEdit' => $isEdit,
'routePrefix' => $routePrefix,
'input' => $input,
'category' => $category,
'backLink' => $backLink,
])