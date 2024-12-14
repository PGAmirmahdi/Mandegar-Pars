<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePacketRequest extends FormRequest
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
            'invoice' => 'required',
            'receiver' => 'required',
            'address' => 'required',
            'sent_type' => 'required',
            'send_tracking_code' => 'nullable|numeric|unique:packets,send_tracking_code,'.$this->packet->id,
            'receive_tracking_code' => 'nullable|numeric',
            'packet_status' => 'required',
            'invoice_status' => 'required',
            'sent_time' => 'required|size:10',
        ];
    }

    public function messages()
    {
        return [
            'sent_time.size' => 'فرمت زمان ارسال صحیح نیست'
        ];
    }
}
