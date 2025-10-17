<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    //
    public function index()
    {
        // resources/views/hello.blade.php を呼び出す
        return view('hello', ['name' => '訪問者']);
    }
}
