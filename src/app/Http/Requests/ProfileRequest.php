<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // ログインチェック用

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // ログインしているユーザーなら許可する
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 設計書に基づいたバリデーションルール
            'image' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'], // 拡張子jpeg,png
            'name' => ['required', 'string', 'max:255'], // ユーザー名 (テーブル仕様に合わせる)
            'post_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // ハイフンあり8文字
            'address' => ['required', 'string', 'max:255'], // 住所
            'building' => ['nullable', 'string', 'max:255'], // 建物名
        ];
    }

    /**
     * エラーメッセージの定義 (任意ですが推奨)
     */
    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'post_code.required' => '郵便番号を入力してください',
            'post_code.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
            // ... 他のメッセージ
        ];
    }
}