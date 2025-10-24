<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordReminderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TopController;


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

// 認証が必要なルートグループ
Route::middleware('auth')->group(function () {
    
    // 【追加】Ajaxによる小カテゴリ取得ルート
    Route::get('/api/subcategories', [ProductController::class, 'getSubcategories'])->name('api.subcategories');

    // ★新規: Ajaxによる画像アップロード一時保存ルートを追加 ★
    Route::post('/api/product/upload-image', [ProductController::class, 'uploadImage'])->name('api.product.upload_image');

    // ★★★ 商品登録フローのルート設定 ★★★
    Route::controller(ProductController::class)->group(function () {
        // ⑨ 商品登録フォームの表示
        // Route::controller() を使用しているため、メソッド名のみを指定します
        Route::match(['GET', 'POST'], '/product/create', 'create')->name('product.create');

        // ★追加1: 入力値確認処理 (バリデーションとセッション保存)
        Route::post('/product/confirm', 'confirm')->name('product.confirm');
        
        // ★★★ 修正: 確認画面表示のGETルートを追加 ★★★
        Route::get('/product/confirm', 'showConfirm')->name('product.show_confirm');

        // ★追加2: DB登録実行処理 (確認画面からの遷移)
        // ルート名を product/confirm.blade.php と合わせて execute_store に修正
        Route::post('/product/store', 'executeStore')->name('product.execute_store');

    });
});

// 商品一覧 (product.list) を認証グループの外に出す
// 既存の /products ルート
Route::get('/products', [ProductController::class, 'list'])->name('product.list');

// ★【追加】/product/list ルートを追加し、既存と同じ処理に紐付ける
// product.list.legacy の定義を追加することで、Controllerでの route() 呼び出しエラーを解消する
Route::get('/product/list', [ProductController::class, 'list'])->name('product.list.legacy');

// ★【新規追加】商品詳細画面のルート (IDを含む)
Route::get('/product/{id}', [ProductController::class, 'showDetail'])->name('product.detail');
