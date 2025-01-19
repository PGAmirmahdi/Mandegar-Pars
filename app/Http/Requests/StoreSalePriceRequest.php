<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalePriceRequest extends FormRequest
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
            'customer' => 'required|exists:customers,id',
            'date' => 'required|date',
            'hour' => 'required|date_format:H:i',
            'payment_type' => 'required',
            'need_no' => 'required|string|max:255',
//            'products' => 'required|array',
//            'products.*.id' => 'required|exists:products,id',
//            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
    public function messages()
    {
        return [
            'customer.required' => 'انتخاب مشتری الزامی است.',
            'customer.exists' => 'مشتری انتخاب‌ شده معتبر نیست.',
            'date.required' => 'تاریخ را وارد کنید.',
            'date.date' => 'تاریخ وارد شده معتبر نیست.',
            'hour.required' => 'ساعت را وارد کنید.',
            'hour.date_format' => 'فرمت ساعت باید به صورت HH:mm باشد.',
            'payment_type.required' => 'نوع پرداخت الزامی است.',
            'payment_type.in' => 'نوع پرداخت انتخاب‌شده معتبر نیست.',
//            'products.required' => 'محصولات نمی‌توانند خالی باشند.',
//            'products.*.id.required' => 'شناسه محصول الزامی است.',
//            'products.*.id.exists' => 'محصول انتخاب‌شده معتبر نیست.',
//            'products.*.quantity.required' => 'تعداد محصول الزامی است.',
//            'products.*.quantity.integer' => 'تعداد محصول باید عدد صحیح باشد.',
//            'products.*.quantity.min' => 'تعداد محصول نمی‌تواند کمتر از ۱ باشد.',
            'need_no.required' => 'شماره نیاز الزامی است'
        ];
    }
}
