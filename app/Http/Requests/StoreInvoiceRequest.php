<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
                'buyer_name' => 'required',
                'buyer_id' => 'required',
                'code' => 'required|exists:orders,code',
                'national_number' => 'required|numeric',
                'postal_code' => 'required|numeric',
                'economical_number' => (auth()->user()->isSystemUser() ? 'required|numeric' : 'nullable|numeric'),
                'need_no' => 'nullable|numeric',
                'phone' => 'required',
                'province' => 'required',
                'city' => 'required',
                'address' => 'required',

            ];
    }
}
