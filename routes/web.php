<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MemberController;

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