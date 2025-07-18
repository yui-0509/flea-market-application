<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'username'   => 'required|string',
            'post_code'  => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'    => 'required|string',
            'building'   => 'nullable|string',
        ];
    }
}
