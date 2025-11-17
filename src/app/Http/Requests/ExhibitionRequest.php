<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            // FN028/FN029: 商品名, 説明, 画像, カテゴリ, 状態, 価格
            'name' => ['required', 'string', 'max:255'], // 商品名
            'description' => ['required', 'string', 'max:255'], // 商品説明 (最大文字数255)
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'], // 商品画像 (アップロード必須、拡張子jpeg/png)
            'categories' => ['required', 'array', 'min:1'], // 商品のカテゴリー (選択必須)
            'condition' => ['required', 'string'], // 商品の状態 (選択必須)
            'price' => ['required', 'numeric', 'min:1'], // 商品価格 (数値型、0円以上)
            
            'brand_name' => ['nullable', 'string', 'max:255'], // ブランド名 (任意)
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'image.required' => '商品画像を選択してください',
            'categories.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.numeric' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上の数値を入力してください',
            'description.max' => '商品説明は最大255文字以内で入力してください',
        ];
    }
}