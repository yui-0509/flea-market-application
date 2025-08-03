<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
        return [
            'item_name' => 'required',
            'description' => 'required|max:255',
            'item_image' => 'required|image|mimes:jpeg,png',
            'categories'   => 'required|array|min:1',
            'categories.*' => 'integer|exists:categories,id',
            'status' => 'required|in:1,2,3,4',
            'price' => 'required|integer|min:0',
        ];
    }
}
