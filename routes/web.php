<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordReminderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\ReviewController; // ReviewController を使用するためにインポート


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

    // パスワード再設定（パスワード設定）フォーム表示
    Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');

    // パスワード更新処理
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
        Route::match(['GET', 'POST'], '/product/create', 'create')->name('product.create');

        // 入力値確認処理 (バリデーションとセッション保存)
        Route::post('/product/confirm', 'confirm')->name('product.confirm');
        
        // 確認画面表示のGETルートを追加
        Route::get('/product/confirm', 'showConfirm')->name('product.show_confirm');

        // DB登録実行処理 (確認画面からの遷移)
        Route::post('/product/store', 'executeStore')->name('product.execute_store');
    });

    // ★★★ レビュー投稿フローのルート設定 ★★★
    
    // 商品IDをプレフィックスとするグループ (Route Model Binding 'product' を使用)
    Route::prefix('product/{product}')->group(function () {
        // 1. レビュー作成フォームの表示 (GET: /product/{product}/review/create)
        Route::get('/review/create', [ReviewController::class, 'create'])
            ->name('product.review.create');

        // 2. レビュー内容の確認処理 (POST: /product/{product}/review/confirm)
        Route::post('/review/confirm', [ReviewController::class, 'confirm'])
            ->name('product.review.confirm');

        // 3. レビューの最終登録処理 (POST: /product/{product}/review/store)
        Route::post('/review/store', [ReviewController::class, 'store'])
            ->name('product.review.store');
            
        // 4. レビュー登録完了画面の表示 (GET: /product/{product}/review/complete)
        Route::get('/review/complete', [ReviewController::class, 'complete'])
            ->name('product.review.complete');
    });

    // ★★★ ここまでレビュー投稿ルート ★★★

});

// 商品一覧 (product.list) を認証グループの外に出す
// 既存の /products ルート
Route::get('/products', [ProductController::class, 'list'])->name('product.list');

// product.list.legacy の定義
Route::get('/product/list', [ProductController::class, 'list'])->name('product.list.legacy');

// 商品詳細画面のルート (ReviewControllerが参照する 'product.show' ルートを定義)
// パラメーター名を {product} に変更し、ReviewControllerが期待する名前 'product.show' を付けます。
Route::get('/product/{product}', [ProductController::class, 'showDetail'])->name('product.show');

Route::get('/products/{product}/reviews/cancel', [ReviewController::class, 'cancelAndRedirect'])->name('product.review.cancel_to_top');

// 元々あった 'product.detail' や 'products.show' の重複定義は削除しました。

// 商品IDを指定して、その商品のレビュー一覧を表示するルート
// 例: /products/1/reviews
Route::get('/products/{product}/reviews', [ReviewController::class, 'index'])
    ->name('product.reviews.index');
