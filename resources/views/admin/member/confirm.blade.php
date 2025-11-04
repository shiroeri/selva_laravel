@php
    // 新規登録確認画面用の設定
    $pageTitle = '会員登録';
    $isEdit = false;
    $routePrefix = 'admin.member';

    // コントローラーから渡される $data (入力データ) と $member (存在しないので null) が
    // 共通テンプレートで使用されることを前提とします。
    // 仮に $data が未定義の場合は、空の配列を渡すなどの処理をここに追加してください。
    // 例: $data = $data ?? [];
    // 例: $member = $member ?? null;
@endphp

{{-- 共通テンプレートを読み込み、上記の変数を引き継ぐ --}}
@include('admin.registrationEditCommon.confirm', [
    'pageTitle' => $pageTitle,
    'isEdit' => $isEdit,
    'routePrefix' => $routePrefix,
    'data' => $data,
    'member' => null, // 登録時はmemberインスタンスは存在しない
])
