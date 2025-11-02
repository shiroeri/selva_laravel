<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate extends Middleware
{
    /**
     * 未認証の場合にリダイレクトするパスを取得します。
     * * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // JSONリクエストでない場合（通常のWebアクセスの場合）
        if (! $request->expectsJson()) {
            // 管理者ログイン画面へのルート名にリダイレクトする
            // config/auth.php で設定されたガードが未認証の場合に実行されます
            return route('admin.login');
        }
    }
    
    /**
     * ユーザーが認証されていることを確認します。
     * このミドルウェアは、管理者用ガード 'admin' の認証状態のみをチェックします。
     * * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        // ★★★ 修正ポイント: ここでガードを 'admin' に固定する ★★★
        
        // Auth::guard('admin') で、管理者用ガードを使って認証チェックを行う
        if (Auth::guard('admin')->check()) {
            return; // 認証済みなので続行
        }

        // 認証されていない場合は、未認証例外をスローし、redirectTo() へ処理を委ねる
        $this->unauthenticated($request, ['admin']);
    }
}
