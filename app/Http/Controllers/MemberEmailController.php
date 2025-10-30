<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // DBファサードは今回は不要になりましたが、以前のuse文として残します
use Carbon\Carbon; 
// use App\Models\Member; // モデルを直接インポートすることも可能ですが、ここではget_class($user)を使用します

// 変更後のメールアドレスをセッションに保存するためのキー
const NEW_EMAIL_SESSION_KEY = 'member_new_email_pending'; 

class MemberEmailController extends Controller
{
    /**
     * メールアドレス変更フォームを表示する。
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        $user = Auth::user();

        // フォームを表示（現在のメールアドレスを渡す）
        return view('member.MemberEmailForm', [
            'currentEmail' => $user->email,
        ]);
    }

    /**
     * 変更後のメールアドレスを受け取り、認証コードを生成してDBに保存し、メール送信（ログ出力）を行う。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendAuthCode(Request $request)
    {
        $user = Auth::user();
        
        // 1. バリデーションの実行
        $request->validate([
            'new_email' => [
                'required',
                'string',
                'email',
                'max:200',
                'unique:members,email', 
            ],
        ], [
            'new_email.required' => '変更後のメールアドレスは必須です。',
            'new_email.email' => 'メールアドレスの形式が正しくありません。',
            'new_email.max' => '変更後のメールアドレスは200文字以内で入力してください。',
            'new_email.unique' => 'このメールアドレスは既に登録済みです。',
        ]);
        
        // 2. 認証コード（6桁の数値）を生成し、DB内で一意であることを確認
        $authCode = '';
        $maxAttempts = 10; // 試行回数に上限を設定して無限ループを防ぐ
        $attempt = 0;
        
        // 🚨 修正点: 認証済みユーザーのクラス名を取得し、Eloquentでユニークチェックを行います
        $MemberModelClass = get_class($user); 

        do {
            // 6桁のランダムな数字文字列を生成
            $authCode = strval(rand(100000, 999999));
            $attempt++;

            // Eloquentを利用して、このコードが他のユーザーの認証コードとして使われていないかを確認
            $isUnique = $MemberModelClass::where('auth_code', $authCode)->doesntExist();

            if ($attempt >= $maxAttempts) {
                // 最大試行回数を超えた場合、ログを出してエラーをスロー
                Log::error("Failed to generate a unique auth code after $maxAttempts attempts.");
                return redirect()->back()->withErrors(['code_error' => '認証コードの生成に失敗しました。時間をおいて再度お試しください。']);
            }

        } while (!$isUnique); // 一意でなければループを続ける
        
        // 3. 認証コードをDBに保存し、新しいメールアドレスをセッションに保存
        $newEmail = $request->new_email;
        // $codeCreatedAt = now(); // 有効期限削除のため不要

        try {
            // membersテーブルの auth_code のみを更新 (Eloquent利用)
            $user->auth_code = $authCode;
            $user->save();
            
            // 変更後のメールアドレスをセッションに一時保存
            session([NEW_EMAIL_SESSION_KEY => $newEmail]);
            
        } catch (\Exception $e) {
            Log::error('DB update failed during email change preparation for user ' . $user->id . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['db_error' => 'メールアドレス変更の準備中にエラーが発生しました。時間をおいて再度お試しください。'])->withInput();
        }

        // 4. 認証メールの送信（ログ出力）
        $emailContent = view('emails.AuthCodeEmail', [
            'authCode' => $authCode,
            'newEmail' => $newEmail, 
        ])->render();
        
        Log::info('--- Email Verification Log ---');
        Log::info('TO: ' . $newEmail);
        Log::info('AUTH CODE: ' . $authCode);
        Log::info('EMAIL CONTENT: ' . $emailContent); 
        Log::info('------------------------------');

        // 5. 認証コード入力画面へリダイレクト
        session()->flash('status', '認証メールを新しいメールアドレス（' . $newEmail . '）に送信しました。');
        return redirect()->route('member.email.show-verify-form');
    }

    /**
     * 認証コード入力フォームを表示する。
     * @return \Illuminate\View\View
     */
    public function showVerifyForm()
    {
        // セッションに新しいメールアドレスがなければフォームに戻す（手続きが中断されているため）
        if (!session()->has(NEW_EMAIL_SESSION_KEY)) {
            session()->flash('db_error', 'メールアドレス変更の手続きが確認できませんでした。最初からやり直してください。');
            return redirect()->route('member.email.show-form');
        }
        
        // 認証コード入力画面を表示
        return view('member.MemberEmailVerify');
    }

    /**
     * 認証コードを検証し、メールアドレスの更新を完了する。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyCode(Request $request)
    {
        $user = Auth::user();

        // 1. バリデーションの実行
        $request->validate([
            'auth_code' => ['required', 'string', 'digits:6'], // 6桁の数値
        ], [
            'auth_code.required' => '認証コードは必須です。',
            'auth_code.digits' => '認証コードは6桁の数字で入力してください。',
        ]);
        
        // 2. セッションから変更後のメールアドレスを取得
        $newEmail = session(NEW_EMAIL_SESSION_KEY);

        if (!$newEmail) {
            return redirect()->route('member.email.show-form')->withErrors(['db_error' => 'メールアドレスの変更情報が見つかりません。再度手続きを行ってください。']);
        }
        
        // 3. 認証コードのチェック
        $submittedCode = $request->auth_code;
        $storedCode = $user->auth_code;

        // DBから取得したコードがnullでないことを確認し、両方を文字列にキャストして比較する
        $isCodeMatch = (string)$submittedCode === (string)$storedCode;

        Log::info('--- Auth Code Verification Debug ---');
        Log::info('Submitted Code: ' . $submittedCode . ' (Type: ' . gettype($submittedCode) . ')');
        Log::info('Stored Code: ' . (is_null($storedCode) ? 'NULL' : $storedCode) . ' (Type: ' . gettype($storedCode) . ')');
        Log::info('Comparison Result: ' . ($isCodeMatch ? 'MATCH' : 'MISMATCH'));
        Log::info('------------------------------------');


        // DBにコードが保存されていない、またはコードが一致しない場合
        if (is_null($storedCode) || !$isCodeMatch) {
            return redirect()->back()->withErrors(['auth_code' => '認証コードが間違っています。']);
        }

        // 4. メールアドレスの更新を完了 (Eloquent利用)
        try {
            // emailをセッションから取得した値に更新
            $user->email = $newEmail;
            // 認証コードの一時保存用カラムをクリア
            // $user->auth_code = null;
            $user->save();

            // 5. セッションから一時メールアドレスを削除
            session()->forget(NEW_EMAIL_SESSION_KEY);
            
            session()->flash('status', 'メールアドレスが正常に変更されました。');
            
            // 6. マイページへリダイレクト
            return redirect()->route('mypage.index'); 

        } catch (\Exception $e) {
            Log::error('Email update failed for user ' . $user->id . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['db_error' => 'メールアドレスの更新中にエラーが発生しました。時間をおいて再度お試しください。']);
        }
    }
}
