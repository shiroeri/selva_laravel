@extends('admin.registrationEditCommon.input', [
    // 共通テンプレートが必要とする変数
    'isEdit' => false, // 新規登録
    'routePrefix' => 'admin.member', 
    'member' => null, // 新規登録なのでデータは渡さない

    // ページタイトル
    'pageTitle' => '会員登録',
    
    // 戻るボタンのリンク先 (一覧画面へ)
    // route()関数が定義されていることを前提としています
    'backLink' => route('admin.member.index'),
])
