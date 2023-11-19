<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleReportRequest extends FormRequest
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
            'person_name' => 'required',
            'national_code' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'national_code.numeric' => 'فیلد کد/شناسه ملی باید از نوع عددی باشد'
        ];
    }
}
