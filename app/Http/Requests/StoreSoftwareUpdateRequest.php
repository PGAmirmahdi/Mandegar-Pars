<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSoftwareUpdateRequest extends FormRequest
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
            'version_number' => 'required',
            'release_date' => 'required',
            'description' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'فیلد تغییرات الزامی است'
        ];
    }
}
