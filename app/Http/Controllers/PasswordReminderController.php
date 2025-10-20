<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; // パスワードハッシュ化に使用
use Illuminate\Support\Facades\DB;   // ★追加: DB操作用★
use Illuminate\Support\Str;          // ★追加: トークン生成用★

class PasswordReminderController extends Controller
{
    /**
     * ⑤ パスワード再設定フォームを表示する (メールアドレス入力画面)
     */
    public function showForm()
    {
        return view('password.request'); // resources/views/password/request.blade.php
    }

    /**
     * ⑥ パスワード再設定（メール送信完了）画面を表示する
     */
    public function showSent()
    {
        return view('password.sent'); // resources/views/password/sent.blade.php
    }

    // 2. メール送信処理 (トークン生成とメール送信リンクの作成)
    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = $request->email;
        $member = Member::where('email', $email)->first();

        if (!$member) {
            throw ValidationException::withMessages([
                'email' => ['ご入力のメールアドレスは登録されておりません。'],
            ])->redirectTo(route('password.request'));
        }

        // ★★★ 変更点1: トークン生成とDBへの保存 ★★★
        $plainToken = Str::random(60); // メールに含める平文トークン
        
        // パスワードリセットテーブルにトークンを保存 (セキュリティのためハッシュ化)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($plainToken),
                'created_at' => now()
            ]
        );

        // ★★★ 変更点2: メール送信時にトークンを渡す ★★★
        try {
            // メールクラスにユーザー情報と平文トークンを渡す
            Mail::to($email)->send(new PasswordResetLink($member, $plainToken));

        } catch (\Exception $e) {
            Log::error('Password Reset Mail Failed: ' . $e->getMessage());
        }

        return redirect()->route('password.sent');
    }


    // 4. ⑧ パスワード再設定（パスワード設定）フォーム表示 (トークン検証)
    public function showResetForm(Request $request, $token = null)
    {
        // 1. メールアドレスとトークンがURLに含まれているかチェック
        if (!$request->email || !$token) {
            return redirect()->route('password.request')
                             ->withErrors(['email' => '無効なリンクです。']);
        }

        // 2. トークンレコードの検索
        $reset = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        // 3. トークンの有効性、期限切れ、ハッシュ値の一致をチェック
        if (!$reset || now()->subHours(1)->gt($reset->created_at) || !Hash::check($token, $reset->token)) {
             // リンクが無効または期限切れの場合 (ここでは有効期限を1時間と仮定)
             return redirect()->route('password.request')
                              ->withErrors(['email' => 'パスワード再設定リンクが無効または期限切れです。再度手続きを行ってください。']);
        }

        // 4. 有効な場合はビューを表示
        return view('password.reset', ['token' => $token, 'email' => $request->email]);
    }

    // 5. パスワード更新処理 (トークン最終検証とパスワード更新)
    public function updatePassword(Request $request)
    {
        // 1. バリデーション (tokenとemailはフォームのhiddenフィールドから取得)
        $request->validate([
            'token' => 'required', 
            'email' => 'required|email|exists:members,email',
            'password' => [
                'required', 'string', 'min:8', 'max:20', 'confirmed', 'regex:/^[a-zA-Z0-9]+$/',
            ],
        ]);

        // 2. トークンの最終チェック
        $reset = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();
        
        if (!$reset || !Hash::check($request->token, $reset->token)) {
            // トークンが無効な場合
            return redirect()->route('password.request')
                             ->withErrors(['email' => 'セッションが無効です。再度パスワード再設定手続きを行ってください。']);
        }

        // 3. パスワード更新
        $member = Member::where('email', $request->email)->first();

        if ($member) {
            $member->password = Hash::make($request->password);
            $member->save();
            
            // 4. トークンの削除 (使用済みトークンは必ず削除)
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // 5. ログイン画面へ遷移 (設計書要件)
            return redirect()->route('login')->with('status', 'パスワードを再設定しました。新しいパスワードでログインしてください。');
        }

        return redirect()->back()->withErrors(['email' => '予期せぬエラーが発生しました。']);
    }
}