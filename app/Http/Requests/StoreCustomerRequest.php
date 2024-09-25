<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name' => 'required|unique:customers,name',
            'customer_code' => 'nullable|unique:customers,code',
            'type' => 'required',
            'national_number' => 'required|numeric',
            'postal_code' => 'required|numeric',
            'economical_number' => 'nullable|numeric',
            'province' => 'required',
            'city' => 'required',
            'phone1' => 'required|numeric',
            'phone2' => 'nullable|numeric',
            'phone3' => 'nullable|numeric',
            'address1' => 'required',
        ];
    }
}
