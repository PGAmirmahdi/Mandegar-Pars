<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
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
            'code' => 'required|numeric|unique:inventories,code,'.$this->inventory->id,
            'type' => 'required',
            'count' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'count.required' => 'فیلد موجودی الزامی است'
        ];
    }
}