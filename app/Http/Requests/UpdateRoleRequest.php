<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
            'name' => 'required|unique:roles,name,'.$this->role->id,
            'label' => 'required|unique:roles,label,'.$this->role->id,
            'permissions' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'unique.required' => 'فیلد نام فارسی الزامی است',
            'label.required' => 'فیلد نام انگلیسی الزامی است',
            'label.unique' => 'این نام فارسی قبلا ثبت شده است',
            'name.unique' => 'این نام انگلیسی قبلا ثبت شده است',
            'permissions.required' => 'انتخاب دسترسی الزامی است',
        ];
    }
}
