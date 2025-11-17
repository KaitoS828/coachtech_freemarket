<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest; // ★作成したカスタムリクエストを use

class LoginController extends Controller
{
    public function create(){
        return view ('auth/login');
    }

    public function store(LoginRequest $request) // ★引数を変更
    {
        // ... 認証ロジック ...
    }
}