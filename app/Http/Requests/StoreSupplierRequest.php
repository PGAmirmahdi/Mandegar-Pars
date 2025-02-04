<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'supplier_type'=>'required',
            'national_number' => 'required|numeric',
            'postal_code' => 'required|numeric',
            'economical_number' => 'nullable|numeric',
            'province' => 'required',
            'city' => 'required',
            'phone1' => 'required|numeric',
            'phone2' => 'nullable|numeric',
            'address1' => 'required',
            'category' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'وارد کردن نام تامین کننده الزامی است.',
            'name.unique' => 'این نام قبلاً ثبت شده است.',
            'supplier_type.required' => 'نوع تأمین‌کننده را مشخص کنید.',
            'national_number.required' => 'کد ملی الزامی است.',
            'national_number.numeric' => 'کد ملی باید عددی باشد.',
            'postal_code.required' => 'کد پستی را وارد کنید.',
            'postal_code.numeric' => 'کد پستی باید عددی باشد.',
            'economical_number.numeric' => 'شماره اقتصادی باید عددی باشد.',
            'province.required' => 'استان را انتخاب کنید.',
            'city.required' => 'شهر را وارد کنید.',
            'phone1.required' => 'شماره تلفن اول الزامی است.',
            'phone1.numeric' => 'شماره تلفن اول باید عددی باشد.',
            'phone2.numeric' => 'شماره تلفن دوم باید عددی باشد.',
            'address1.required' => 'آدرس را وارد کنید.',
            'category.required' => 'انتخاب دسته بندی اجباری است'
        ];
    }

}
