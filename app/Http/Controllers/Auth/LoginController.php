<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member; // 登録された会員情報を取得するために使用
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    // ログインフォームの表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン認証処理 (設計書要件: 認証とエラー表示)
    public function login(Request $request)
    {
        // 設計書要件: 項目に不備があった場合、ログインフォームに戻りエラーを表示
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // ----------------------------------------------------
        // 設計書要件: ID, パスワードがあった場合の認証チェック
        // ----------------------------------------------------
        
        // 1. メールアドレスで会員を検索
        $member = Member::where('email', $credentials['email'])->first();

        // 2. 認証チェック
        if ($member && Hash::check($credentials['password'], $member->password)) {
            
            // 認証成功: Laravelの標準認証でログインセッションを確立
            // 💡 ただし、このプロジェクトで標準Authを未使用の場合は、
            //    セッションを直接操作するか、Authを適切に設定する必要があります。
            //    ここではLaravel標準の認証フロー(Auth::login)に寄せて記述します。
            Auth::login($member);

            // ★重要★: セッションIDを再生成し、セッションを新しいものとして確立
            $request->session()->regenerate();

            // ログイン成功時にトップ画面に遷移
            return redirect()->route('top')->with('status', 'ログインに成功しました！');

        }

        // ----------------------------------------------------
        // 設計書要件: エラー表示
        // ----------------------------------------------------
        
        // エラーの詳細は「IDもしくはパスワードが間違っています」と表示
        $error_message = 'IDもしくはパスワードが間違っています';

        // 💡 DBの接続でエラーがあった場合、エラーの詳細は表示しない
        //    (DBエラー時はそもそもここに来る前にLaravelがエラーを出す可能性が高いですが、
        //     ここではID/パスワードがDBのレコードと一致しないケースのみを想定)
        
        throw ValidationException::withMessages([
            'email' => [$error_message],
            // 設計書要件: パスワードは表示しない
        ])->redirectTo(route('login'));
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ログアウト後、トップ画面へリダイレクト
        return redirect()->route('top');
    }
}