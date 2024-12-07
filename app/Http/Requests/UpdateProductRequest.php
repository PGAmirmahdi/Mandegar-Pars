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
//            'slug' => 'required|unique:products,slug,'.$this->product->id,
            'description' => 'nullable',
            'category' => 'required',
            'brand' => 'required',
            'image' => 'nullable|mimes:jpg,png,jpeg|max:5000',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'عنوان الزامی است.',
            'category.required' => 'دسته‌بندی الزامی است.',
            'brand.required' => 'برند الزامی است.',
            'image.mimes' => 'تصویر باید از نوع jpg، png یا jpeg باشد.',
            'image.max' => 'اندازه تصویر نباید بیشتر از 5000 کیلوبایت باشد.',
        ];
    }
}
