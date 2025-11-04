@php
    // 会員編集確認画面用の設定
    $pageTitle = '会員編集';
    $isEdit = true;
    $routePrefix = 'admin.member';

    // コントローラーから渡される $data (入力データ) と $member (編集対象のモデルインスタンス) が
    // 共通テンプレートで使用されることを前提とします。
    // 編集時は $member が必須です。
    // 例: $data = $data ?? [];
    // 例: if (!isset($member)) { abort(404); }
@endphp

{{-- 共通テンプレートを読み込み、上記の変数を引き継ぐ --}}
@include('admin.registrationEditCommon.confirm', [
    'pageTitle' => $pageTitle,
    'isEdit' => $isEdit,
    'routePrefix' => $routePrefix,
    'data' => $data,
    'member' => $member, // 編集時はmemberインスタンスが必要
])
