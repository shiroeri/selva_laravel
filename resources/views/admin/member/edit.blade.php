@extends('admin.registrationEditCommon.input', [
    // 共通テンプレートが必要とする変数
    'isEdit' => true, // 編集画面
    'routePrefix' => 'admin.member', 
    // コントローラーから渡される編集対象のモデル
    'member' => $member, 

    // ページタイトル
    // $member->id が存在することを前提としています
    'pageTitle' => '会員編集',
    
    // 戻るボタンのリンク先 (一覧画面へ)
    // route()関数が定義されていることを前提としています
    'backLink' => route('admin.member.index'),
])
