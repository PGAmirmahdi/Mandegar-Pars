<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCostRequest extends FormRequest
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
            'product'=>'required',
            'final_price'=>'required',
            'other_price'=>'required',
            'Logistic_price'=>'required',
            'price'=>'required',
            'count'=>'required',
        ];
    }
    public function message()
    {
        return [
            'product.required'=>'فیلد محصولات الزامی است.',
            'final_price.required'=>'فیلد قیمت نهایی الزامی است.',
            'other_price.required'=>'فیلد قیمت اضافی الزامی است.',
            'Logistic_price.required'=>'فیلد هزینه ارسال الزامی است.',
            'price.required'=>'فیلد قیمت الزامی است.',
            'count.required'=>'فیلد تعداد الزامی است.',
        ];
    }
}
