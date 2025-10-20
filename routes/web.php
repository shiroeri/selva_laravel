<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\PasswordReminderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', [HelloController::class, 'index']);

// 会員登録フロー
Route::controller(MemberController::class)->group(function () {
    // 1. 入力画面の表示と確認画面への遷移処理
    Route::get('/register/input', 'input')->name('member.input');
    Route::post('/register/confirm', 'confirm')->name('member.confirm'); // 確認画面へ

    // 2. 登録処理と完了画面への遷移処理
    Route::post('/register/store', 'store')->name('member.store'); // DB保存

    // 3. 完了画面の表示
    Route::get('/register/complete', 'complete')->name('member.complete');

});

// ログイン・ログアウトのルート設定
    // ログインフォームの表示
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // ログイン処理（フォーム送信先）
    Route::post('/login', [LoginController::class, 'login'])->name('login.post'); 

    // ログアウト処理
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// トップ画面
    Route::get('/top', [TopController::class, 'index'])->name('top');

// パスワード再設定フロー
Route::controller(PasswordReminderController::class)->group(function () {
    // 1. フォーム表示 (ログイン画面から遷移)
    Route::get('/password/reset', 'showForm')->name('password.request');

    // 2. メール送信処理 (フォーム送信先)
    Route::post('/password/email', 'sendEmail')->name('password.email');

    // 3. 送信完了画面表示
    Route::get('/password/sent', 'showSent')->name('password.sent');

    // ★追加ルート1: ⑧ パスワード再設定（パスワード設定）フォーム表示★
    // URLにはトークン（token）を含める必要があります
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');

    // ★追加ルート2: パスワード更新処理★
    Route::post('/password/update', 'updatePassword')->name('password.update');
});