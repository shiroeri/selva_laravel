<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrator;
use Illuminate\Support\Facades\Log; // Logファサードを追加

class LoginController extends Controller
{
    /**
     * ログインフォーム表示
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // 既にログイン済みであれば管理画面トップへリダイレクト
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.top');
        }
        return view('admin.login');
    }

    /**
     * ログイン処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. バリデーションチェック
        $credentials = $request->validate([
            'login_id' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{7,10}$/'], // 英数字7〜10文字
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[a-zA-Z0-9]+$/'], // 英数字8〜20文字
        ], [
            // 日本語エラーメッセージ
            'login_id.required' => 'ログインIDは必須項目です。',
            'login_id.regex' => 'ログインIDは半角英数字7〜10文字で入力してください。',
            'password.required' => 'パスワードは必須項目です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは20文字以内で入力してください。',
            'password.regex' => 'パスワードは半角英数字で入力してください。',
        ]);
        
        // --- 🚨 デバッグコードの挿入 🚨 ---
        $admin = Administrator::where('login_id', $credentials['login_id'])->first();

        if (!$admin) {
            Log::warning('ADMIN_LOGIN_FAIL: ログインIDに一致するユーザーが見つかりません。', ['login_id' => $credentials['login_id']]);
        } else {
            // ユーザーが見つかった場合、パスワードが一致するか、ハッシュが適切かを確認
            $is_password_correct = Hash::check($credentials['password'], $admin->password);
            
            if (!$is_password_correct) {
                Log::error('ADMIN_LOGIN_FAIL: パスワードが一致しません。');
                Log::info('DBのパスワードハッシュ:', ['db_hash' => $admin->password]);
                Log::info('入力された平文のパスワード:', ['input_password' => $credentials['password']]);
                
                // DBのパスワードが短すぎる（ハッシュ化されていない可能性）をチェック
                if (strlen($admin->password) < 60) {
                    Log::critical('🚨 パスワードがハッシュ化されていません！ 🚨');
                    Log::critical('DBのパスワードは平文の可能性があります。Hash::make() で更新してください。');
                }
            } else {
                Log::info('ADMIN_LOGIN_DEBUG: Hash::check() でパスワードは一致しました。');
            }
        }
        // --- 🚨 デバッグコード終了 🚨 ---

        // 2. 認証試行 (元のロジック)
        // Auth::attempt() の第一引数のキーを 'login_id' に上書き
        $authenticated = Auth::guard('admin')->attempt([
            'login_id' => $credentials['login_id'],
            'password' => $credentials['password'],
        ], $request->filled('remember')); // 'remember' チェックボックスがフォームにあれば true

        if ($authenticated) {
            // 認証成功
            Log::info('ADMIN_LOGIN_SUCCESS: 認証成功');
            $request->session()->regenerate();

            // ログイン成功時のリダイレクト先 (管理画面トップ)
            return redirect()->route('admin.top');
        }

        // 認証失敗
        // ログインIDまたはパスワードが一致しない場合のエラー表示
        return back()->withErrors([
            'login_id' => 'ログインID、またはパスワードが一致しません。',
        ])->onlyInput('login_id'); // ログインIDのみ再入力させる
    }

    /**
     * ログアウト処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // ログアウト成功時はログインフォームへ遷移
        return redirect()->route('admin.login');
    }
}
