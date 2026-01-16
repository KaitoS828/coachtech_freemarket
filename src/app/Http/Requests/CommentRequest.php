<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check(); // ログインユーザーのみ許可
    }

    public function rules()
    {
        return [
            // 入力必須、最大文字数255
            'comment' => ['required', 'string', 'max:255'], 
            'item_id' => ['required', 'exists:items,id'], // どの商品へのコメントか
        ];
    }
    
    public function messages()
    {
        return [
            'comment.required' => '商品コメントを入力してください', // FN020
            'comment.max' => '入力できる文字数は最大255文字です', // FN020
        ];
    }
}