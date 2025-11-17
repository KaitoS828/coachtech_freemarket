<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check(); // ログインユーザーのみ許可
    }

    public function rules()
    {
        return [
            // 郵便番号: 入力必須、ハイフンありの8文字
            'post_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], 
            // 住所: 入力必須
            'address' => ['required', 'string', 'max:255'],
            // 建物名: 任意
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }
    
    public function messages()
    {
        return [
            'post_code.required' => '郵便番号を入力してください',
            'post_code.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}