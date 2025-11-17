<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest; // ★作成したカスタムリクエストを use

class RegisterController extends Controller
{
    // 登録フォーム表示
    public function create()
    {
        return view('auth.register'); // 登録 Blade ファイルのパス
    }

    // ユーザー登録処理
    public function store(RegisterRequest $request) 
    {
        // バリデーションが通過した後の処理
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ユーザーをログインさせる
        auth()->login($user);

        // 修正箇所: リダイレクト先をプロフィール編集画面に変更 
        return redirect()->route('mypage.profile.edit'); 
    }
}