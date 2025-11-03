<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradeMessageRequest extends FormRequest
{
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
        return [
            'body' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max' => '本文は400文字以内で入力してください',
            'image.image' => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
