<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'document_title'           => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'title.required'              => 'فیلد عنوان درخواست الزامی است.',
            'title.string'                => 'فیلد عنوان درخواست باید متنی باشد.',
            'title.max'                   => 'فیلد عنوان درخواست نباید بیشتر از 255 کاراکتر باشد.',
            'description.string'          => 'فیلد توضیحات باید متنی باشد.',
            'document_title.required'          => 'فیلد اسناد الزامی است.',
            'documents.array'             => 'فیلد اسناد باید به صورت آرایه ارسال شود.',
        ];
    }
}
