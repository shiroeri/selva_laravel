<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * @var array
     * 親クラス (Illuminate\Auth\Middleware\Authenticate) でprotectedとして定義されているプロパティを、
     * IDEの静的解析エラーを回避するために明示的に宣言します。
     */
    protected $guards;
    
    /**
     * ユーザーが認証されていない場合にリダイレクトすべきパスを取得します。
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            
            // ----------------------------------------------------
            // 💡 修正ポイント: リクエストURLのパスで判定する 
            // ----------------------------------------------------
            
            // 1. リクエストされたURLのパスが '/admin' で始まるかを確認
            //    -> 管理者系のルートにアクセスしようとしていると判断
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            
            // 2. それ以外（通常のユーザーログイン）
            return route('login');
        }

        return null;
    }
}
