<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
// 管理者コントローラー
use App\Http\Controllers\Admin\AdminController; 
// 会員側コントローラー
use App\Http\Controllers\HelloController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordReminderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\MemberEditController;
use App\Http\Controllers\MemberPasswordController;
use App\Http\Controllers\MemberEmailController;
use App\Http\Controllers\Mypage\ReviewController as MypageReviewController;
// Admin\LoginController を AdminLoginController という名前で参照します。
use App\Http\Controllers\Admin\LoginController as AdminLoginController; 
use App\Http\Middleware\AdminAuthenticate;

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

    // ログアウト処理 (既存のものをそのまま利用)
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



// =========================================================================
// ★★★ 管理者側: メインルートグループ ★★★
// =========================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // 認証不要ルート
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('login.post');

    // ---------------------------------------------------------------------
    // 認証必須ルート (ミドルウェアを直接指定)
    // ---------------------------------------------------------------------
    Route::middleware([AdminAuthenticate::class])->group(function () {

        // ログアウト実行（認証済みユーザーのみ実行可能）
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
        
        // 管理者トップページ (★一時的にミドルウェアから外す)
        Route::get('top', [AdminController::class, 'index'])->name('top');
        
        // ... (他の認証が必要な管理者ページ)
    });
});



// =========================================================================
// ★★★ 会員側: 認証が必要なルートグループ（既存のauthミドルウェア） ★★★
// =========================================================================
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
    
    // ★★★ メールアドレス変更機能のルート設定 (ここから) ★★★
    Route::controller(MemberEmailController::class)->group(function () {
        // 1. フォーム表示 (GET: /member/email/form)
        Route::get('/member/email/form', 'showForm')->name('member.email.show-form');

        // 2. 認証メール送信処理 (POST: /member/email/send-auth-code)
        Route::post('/member/email/send-auth-code', 'sendAuthCode')->name('member.email.send-auth-code');

        // 3. 認証コード入力フォーム表示 (GET: /member/email/verify)
        Route::get('/member/email/verify', 'showVerifyForm')->name('member.email.show-verify-form');
        
        // 4. 認証コード検証と更新完了処理 (POST: /member/email/verify-code)
        Route::post('/member/email/verify-code', 'verifyCode')->name('member.email.verify-code');
    });
    // ★★★ メールアドレス変更機能のルート設定 (ここまで) ★★★


    // ★★★ マイページ機能のルート設定（ここを追加しました） ★★★
    // 仕様: マイページはログイン時のみ遷移可能
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');
    // ★★★ マイページ機能のルート設定（ここまで） ★★★

    // 【修正点】マイページ内のレビュー管理ルート (MypageReviewControllerを使用)    
    Route::prefix('mypage/reviews')->name('mypage.reviews.')->controller(MypageReviewController::class)->group(function () {    
        // 1. 自分のレビュー一覧表示 (GET: /mypage/reviews)    
        Route::get('/', 'index')->name('index');    
        
        // 2. レビュー編集フォーム表示 (GET: /mypage/reviews/{review}/edit) 
        Route::get('/{review}/edit', 'edit')->name('edit'); 
        
        // 3. レビュー編集内容確認 (POST: /mypage/reviews/{review}/confirm)   
        Route::post('/{review}/confirm', 'confirm')->name('confirm');   

        // ★★★ 欠けていた削除確認ルートを追加 ★★★
        // 削除確認フォーム表示 (GET: /mypage/reviews/{review}/delete/confirm)
        Route::get('/{review}/delete/confirm', 'deleteConfirm')->name('deleteConfirm');
        
        // 4. レビュー更新実行 (PUT/PATCH: /mypage/reviews/{review})    
        Route::put('/{review}', 'update')->name('update');  
        
        // 5. レビュー削除実行 (DELETE: /mypage/reviews/{review})   
        Route::delete('/{review}', 'destroy')->name('destroy'); 
    }); 
    // ★★★ マイページ機能のルート設定（ここまで） ★★★

    // 退会確認画面へのルート
    Route::get('/withdraw', [WithdrawController::class, 'showWithdrawForm'])->name('withdraw.confirm');
    
    // 退会処理を実行するルート (POST)
    Route::post('/withdraw', [WithdrawController::class, 'withdraw'])->name('withdraw');

    // 1. フォーム表示 (GET: /member/edit)
    Route::get('/member/edit', [MemberEditController::class, 'form'])->name('member.edit.form');

    // 2. 確認画面へ遷移 (POST: /member/edit/confirm)
    Route::post('/member/edit/confirm', [MemberEditController::class, 'confirm'])->name('member.edit.confirm');

    // 3. 変更完了 (DB更新) (POST: /member/edit/update)
    Route::post('/member/edit/update', [MemberEditController::class, 'update'])->name('member.edit.update');

    // 4. 完了画面 (GET: /member/edit/complete)
    Route::get('/member/edit/complete', [MemberEditController::class, 'complete'])->name('member.edit.complete');

    // ★★★ パスワード変更機能のルート設定 (ここから) ★★★
    // 5. パスワード変更フォームの表示 (GET: /member/password/edit)
    Route::get('/member/password/edit', [MemberPasswordController::class, 'showForm'])
        ->name('member.password.edit.form');

    // 6. パスワード更新処理の実行 (POST: /member/password/update)
    Route::post('/member/password/update', [MemberPasswordController::class, 'update'])
        ->name('member.password.update');
    // ★★★ パスワード変更機能のルート設定 (ここまで) ★★★

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

// 商品IDを指定して、その商品のレビュー一覧を表示するルート
// 例: /products/1/reviews
Route::get('/products/{product}/reviews', [ReviewController::class, 'index'])
    ->name('product.reviews.index');
