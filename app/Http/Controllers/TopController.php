<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopController extends Controller
{
    /**
     * トップ画面を表示する
     */
    public function index()
    {
        // ログイン状態によってBladeの @auth/@guest ディレクティブが動作するため、
        // コントローラー側で特別なデータ処理は不要です。
        
        return view('top');
    }
}