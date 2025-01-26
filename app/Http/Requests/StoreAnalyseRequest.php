<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalyseRequest extends FormRequest
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
            'date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:date',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:product_models,id',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:0',
            'products.*.storage_count' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'تاریخ شروع الزامی است.',
            'date.date' => 'تاریخ شروع باید فرمت معتبر داشته باشد.',
            'to_date.required' => 'تاریخ پایان الزامی است.',
            'to_date.date' => 'تاریخ پایان باید فرمت معتبر داشته باشد.',
            'to_date.after_or_equal' => 'تاریخ پایان باید برابر یا بعد از تاریخ شروع باشد.',
            'category_id.required' => 'انتخاب دسته‌بندی الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخاب شده نامعتبر است.',
            'brand_id.required' => 'انتخاب برند الزامی است.',
            'brand_id.exists' => 'برند انتخاب شده نامعتبر است.',
            'products.required' => 'حداقل یک محصول باید انتخاب شود.',
            'products.array' => 'فرمت محصولات ارسالی معتبر نیست.',
            'products.min' => 'حداقل یک محصول باید انتخاب شود.',
            'products.*.quantity.required' => 'تعداد محصول الزامی است.',
            'products.*.quantity.integer' => 'تعداد محصول باید یک عدد صحیح باشد.',
            'products.*.quantity.min' => 'تعداد محصول نمی‌تواند کمتر از ۰ باشد.',
            'products.*.storage_count.required' => 'مقدار موجودی انبار الزامی است.',
            'products.*.storage_count.integer' => 'مقدار موجودی انبار باید یک عدد صحیح باشد.',
            'products.*.storage_count.min' => 'مقدار موجودی انبار نمی‌تواند کمتر از ۰ باشد.',
        ];
    }

}
