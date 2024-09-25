<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFactorRequest extends FormRequest
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
        if ($this->request->get('type') == 'unofficial'){
            return [
                'seller_name' => 'required',
                'seller_phone' => 'required',
                'seller_province' => 'required',
                'seller_city' => 'required',
                'seller_address' => 'required',

                'economical_number' => (auth()->user()->isSystemUser() ? 'required|numeric' : 'nullable|numeric'),
                'national_number' => 'required|numeric',
                'need_no' => 'nullable|numeric',
                'postal_code' => 'required|numeric',
                'phone' => 'required',
                'province' => 'required',
                'city' => 'required',
                'address' => 'required',
                'deposit_doc' => 'nullable|mimes:jpg,png,jpeg,pdf'
            ];
        }else{
            return [
                'economical_number' => (auth()->user()->isSystemUser() ? 'required|numeric' : 'nullable|numeric'),
                'national_number' => 'required|numeric',
                'need_no' => 'nullable|numeric',
                'postal_code' => 'required|numeric',
                'phone' => 'required',
                'province' => 'required',
                'city' => 'required',
                'address' => 'required',
                'deposit_doc' => 'nullable|mimes:jpg,png,jpeg,pdf'
            ];
        }
    }
}
