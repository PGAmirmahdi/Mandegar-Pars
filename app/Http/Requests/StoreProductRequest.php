<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'title' => 'required',
            'code' => 'required',
//            'slug' => 'required|unique:products',
            'category' => 'required',
            'system_price' => 'required',
            'partner_price_tehran' => 'required',
            'partner_price_other' => 'required',
            'single_price' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg|max:5000',
        ];
    }
}
