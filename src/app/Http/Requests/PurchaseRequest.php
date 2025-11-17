<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            // 支払い方法: 選択必須
            'payment_method' => ['required', 'in:card,bank,convenience'], 
            // 配送先: 選択必須 (ただし、これはControllerでProfile情報をチェックするため、ここでは簡略化)
            'shipping_option' => ['nullable'], 
        ];
    }
    
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください', // FN023
        ];
    }
}