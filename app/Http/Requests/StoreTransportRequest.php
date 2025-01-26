<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransportRequest extends FormRequest
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
            'transporters' => 'required',
            'invoice_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'invoice_id.required' => 'سفارش را انتخاب کنید',
            'transporters.required' => 'افزودن حمل و نقل کننده الزامی است',
        ];
    }
}
