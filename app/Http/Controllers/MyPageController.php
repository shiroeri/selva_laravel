<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPageController extends Controller
{
    /**
     * マイページを表示する。
     * ログインユーザーの会員情報を取得し、ビューに渡します。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 仕様: ログイン時のみ遷移可能 (web.phpでauthミドルウェアを設定済み)
        
        // 仕様: 会員情報を表示 (Eloquentを利用して、現在ログイン中のユーザー情報を取得)
        // Auth::user() は App\Models\User (または設定されたUserモデル) のインスタンスを返します。
        $user = Auth::user();

        // ビューにユーザー情報を渡して表示
        return view('mypage', [
            'user' => $user,
        ]);
    }
}
