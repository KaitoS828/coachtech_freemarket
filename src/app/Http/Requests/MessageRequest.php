<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // バリデーションルール
    public function rules()
    {
        return [
            // 本文は画像がない場合のみ必須
            'content' => 'required_without:image|nullable|string|max:400',
            'image' => 'nullable|mimes:jpeg,png',
        ];
    }

    public function messages(){

        return [
            'content.required_without' => '本文を入力してください',
            'content.max' => '本文は400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];

    }
}
