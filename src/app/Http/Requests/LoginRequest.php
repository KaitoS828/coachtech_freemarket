<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    /**
     * リクエストがこのバリデーションルールを満たす権限があるか確認します。
     * ログインはゲスト（未ログイン）ユーザーに許可されます。
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guest(); 
    }

    /**
     * アプリケーションに適用されるバリデーションルールを取得します。
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // FN008/FN009: メールアドレスのルール
            'email' => ['required', 'string', 'email', 'max:255'], 
            
            // FN008/FN009: パスワードのルール
            'password' => ['required', 'string'], 
        ];
    }
    
    /**
     * カスタムバリデーションメッセージを適用する場合はここで定義できます。
     * (FN010のエラーメッセージ要件に対応するため)
     *
     * @return array
     */
    public function messages()
    {
        return [
            // FN010-1: 未入力の場合
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            
            // FN010-2: 入力情報が誤っている場合 (Laravelの標準認証機能が対応)
            'email.email' => 'メールアドレスはメール形式で入力してください',
        ];
    }
}