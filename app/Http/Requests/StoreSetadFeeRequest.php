<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSetadFeeRequest extends FormRequest
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
            'order' => 'required|digits:8|exists:orders,code',
            'tracking_number' => 'required',
            'price' => 'required',
            'shaba_number' => 'required',
            'description' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'order.required' => 'شماره سفارش الزامی است.',
            'order.exists' => 'شماره سفارش معتبر نیست.',
            'order.digits' => 'شماره سفارش باید 8 رقمی باشد.',
            'tracking_number.required' => 'شماره پیگیری الزامی است.',
            'price.required' => 'مبلغ الزامی است.',
            'shaba_number.required' => 'شماره شبا الزامی است.',
        ];
    }
}
