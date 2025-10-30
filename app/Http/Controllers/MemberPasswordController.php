<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Eloquentモデルを使用

class MemberPasswordController extends Controller
{
    /**
     * パスワード変更フォームを表示する。
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('member.MemberPasswordForm');
    }

    /**
     * パスワード更新処理を実行する。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. バリデーションの実行
        // 「現在のパスワード」のチェックを完全に削除
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[a-zA-Z0-9]+$/', // 半角英数字のみ
                'confirmed', // password_confirmation との一致
            ],
        ], [
            'password.required' => 'パスワードは必須入力です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは20文字以内で入力してください。',
            'password.regex' => 'パスワードは半角英数字のみで入力してください。',
            // ご要望通り、confirmedエラーを password_confirmation のエラーとして分離
            'password_confirmation.confirmed' => 'パスワードと確認用パスワードが一致しません。',
            'password_confirmation.required' => 'パスワード確認は必須入力です。',
            // 備考: password_confirmationがrequiredのルールを満たさない場合（空の場合）は、
            // Laravelの仕様により、上記のメッセージではなく'password.confirmed'のエラーとして処理され、
            // 'password'側に表示されるため、ここでは'password_confirmation.confirmed'のみを定義しています。
        ]);
        
        // 2. 「現在のパスワード」の正当性チェックロジックを削除

        // 3. パスワードをハッシュ化して更新
        try {
            // Eloquentを利用してDBを更新
            $user->password = Hash::make($request->password);
            $user->save();

            // 4. 更新完了後、マイページへリダイレクト
            session()->flash('status', 'パスワードが正常に変更されました。');
            // ご要望の仕様書に従い、マイページへ遷移
            return redirect()->route('mypage.index'); 

        } catch (\Exception $e) {
            // DB操作エラーが発生した場合の処理
            \Log::error('Password update failed for user ' . $user->id . ': ' . $e->getMessage());
            // エラーをセッションに格納してフォームに戻る
            return redirect()->back()->withErrors(['db_error' => 'パスワードの更新中にエラーが発生しました。時間をおいて再度お試しください。'])->withInput();
        }
    }
}
