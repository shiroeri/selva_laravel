<?php

// ★注意: 名前空間が 'Admin' になっているか確認してください
namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Authファサードを使用するため追加

class AdminController extends Controller
{
    /**
     * 管理者トップページを表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 認証済み管理者を取得
        // 'admin' ガードでログインしているユーザーのモデルインスタンスを取得
        $admin = Auth::guard('admin')->user();

        // ユーザー情報をビューに渡す
        // ビューファイルは admin/top.blade.php のまま
        return view('admin.top', compact('admin'));
    }
    
    // 他の管理者関連のメソッド (login, logout など) はこのクラス内に記述されています
}
