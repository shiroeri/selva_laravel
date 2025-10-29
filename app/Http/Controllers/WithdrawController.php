<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Userモデルを使用する場合

class WithdrawController extends Controller
{
    /**
     * 退会確認画面を表示する
     *
     * @return \Illuminate\View\View
     */
    public function showWithdrawForm()
    {
        // ユーザーに退会処理の最終確認を行うブレードを返す
        // ビューパスは resources/views/user/withdraw/confirm.blade.php を想定
        return view('withdraw.confirm');
    }

    /**
     * 退会処理を実行する（ユーザーアカウントを論理削除する）
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function withdraw(Request $request)
    {
        // 認証済みのユーザーを取得
        $user = Auth::user();

        if (!$user) {
            // ユーザーが認証されていない場合はログイン画面などへリダイレクト
            return redirect('/login')->with('error', 'ログインが必要です。');
        }
        
        // パスワードの確認処理（ここでは省略。必要に応じて実装してください）
        // if (!Auth::guard('web')->validate(['email' => $user->email, 'password' => $request->password])) {
        //     return back()->withErrors(['password' => 'パスワードが一致しません。']);
        // }

        try {
            DB::transaction(function () use ($user) {
                // 1. ログアウト処理
                Auth::logout();
                
                // 2. ユーザーモデルの削除（SoftDeletesにより論理削除）
                $user->delete();
            });

            // セッションを再生成してセキュリティを確保
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 退会完了ページなどへリダイレクト
            return redirect('top')->with('status', '退会手続きが完了しました。ご利用ありがとうございました。');
            
        } catch (\Exception $e) {
            // エラーが発生した場合の処理
            \Log::error("User withdrawal failed for user ID: {$user->id}. Error: " . $e->getMessage());
            return back()->with('error', '退会処理中にエラーが発生しました。再度お試しください。');
        }
    }
}
