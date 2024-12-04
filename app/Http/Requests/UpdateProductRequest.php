<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'code' => 'required',
//            'slug' => 'required|unique:products,slug,'.$this->product->id,
            'description' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'image' => 'nullable|mimes:jpg,png,jpeg|max:5000',
            'system_price' => 'required',
            'partner_price_tehran' => 'required',
            'partner_price_other' => 'required',
            'single_price' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'عنوان الزامی است.',
            'code.required' => 'کد الزامی است.',
            'description.required' => 'توضیحات الزامی است.',
            'category.required' => 'دسته‌بندی الزامی است.',
            'brand.required' => 'برند الزامی است.',
            'image.mimes' => 'تصویر باید از نوع jpg، png یا jpeg باشد.',
            'image.max' => 'اندازه تصویر نباید بیشتر از 5000 کیلوبایت باشد.',
            'system_price.required' => 'قیمت سیستم الزامی است.',
            'partner_price_tehran.required' => 'قیمت همکار (تهران) الزامی است.',
            'partner_price_other.required' => 'قیمت همکار (سایر مناطق) الزامی است.',
            'single_price.required' => 'قیمت تک ‌فروشی الزامی است.',
        ];
    }
}
