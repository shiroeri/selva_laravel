<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberStoreRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // パスワードハッシュ化のため
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberRegistered;

class MemberController extends Controller
{
    // 1. 入力画面表示
    public function input()
    {
        // セッションからデータがあればフォームに再表示 (入力保持)
        $member = session('member_input'); 
        return view('member.input', compact('member'));
    }

    // 2. 確認画面表示 (バリデーションとセッション保持)
    public function confirm(MemberStoreRequest $request)
    {
        // バリデーション済みのデータをセッションに保存
        $request->session()->put('member_input', $request->validated());
        
        // パスワードは表示しないため、セッションデータからパスワードを除外してビューに渡す
        $data = $request->validated();
        unset($data['password']); 
        
        return view('member.confirm', compact('data'));
    }

    // 3. DB登録処理
    public function store(Request $request)
    {
        // セッションから入力データを取得
        $data = $request->session()->get('member_input');

        // セッションデータがない場合は入力画面に戻す
        if (!$data) {
            return redirect()->route('member.input')->with('error', '不正な遷移またはセッションが切れました。最初から入力してください。');
        }

        // DBに保存
        $member = Member::create([
            'name_sei' => $data['name_sei'],
            'name_mei' => $data['name_mei'],
            'nickname' => $data['nickname'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            // パスワードはハッシュ化して保存
            'password' => Hash::make($data['password']), 
        ]);

        // 登録完了メールの送信処理
        Mail::to($member->email)->send(new MemberRegistered($member));

        // セッションデータをクリア
        $request->session()->forget('member_input');

        // 完了画面へのアクセスを許可するフラグを設定★
        $request->session()->put('registration_complete', true);

        // 完了画面へリダイレクト
        return redirect()->route('member.complete');
    }

    // 4. 完了画面表示
    public function complete()
    {
        // 完了フラグがない場合は不正アクセスとみなし、入力画面に戻す★
        if (!$request->session()->pull('registration_complete')) {
            // pull() は値を取得した後、セッションから削除する
            return redirect()->route('member.input')->with('error', '不正なアクセスです。');
        }
        
        return view('member.complete');
    }


}