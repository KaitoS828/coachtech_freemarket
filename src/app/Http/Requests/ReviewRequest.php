<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        // とりあえず評価の整数と、コメントを渡す
        return [
            'rate' => 'required|integer|min:1|max:5',
        ];
    }

    public function messages()
    {
        return [
            'rate.required' => '評価は必須です。',
            'rate.integer' => '評価は整数で入力してください。',
            'rate.min' => '評価は1以上で入力してください。',
            'rate.max' => '評価は5以下で入力してください。',
            'comment.string' => 'コメントは文字列で入力してください。',
            'comment.max' => 'コメントは500文字以内で入力してください。',
        ];
    }
}
