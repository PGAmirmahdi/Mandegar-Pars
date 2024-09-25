@extends('panel.layouts.master')
@section('title', 'ویرایش بسته ارسالی')
@section('content')
    {{--  Send Sms Modal  --}}
    <div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="smsModalLabel">ارسال پیامک</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="phone">شماره موبایل<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" maxlength="11" minlength="11">
                        <div class="invalid-feedback d-block" id="phone_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="bodyId">پیامک<span class="text-danger">*</span></label>
                        <select class="form-control" id="bodyId">
                            <option value="177554">کد رهگیری مرسوله</option>
                            <option value="178278">عودت فاکتور</option>
                            <option value="185679">یادآوری پرداخت فاکتور</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="text">متن پیامک<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="text" rows="5" readonly></textarea>
                        <div class="invalid-feedback d-block" id="text_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn_send_sms">
                        <i class="fa fa-paper-plane mr-2"></i>
                        <span>ارسال</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{--  End Send Sms Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش بسته ارسالی</h6>
            </div>
            <form action="{{ route('packets.update', $packet->id) }}" method="post">
                @csrf
                @method('PATCH')
                <input type="hidden" name="url" value="{{ $url }}">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice">سفارش<span class="text-danger">*</span></label>
                        <select class="form-control" name="invoice" id="invoice">
                            @if($invoices->count())
                                <option value="{{ $packet->invoice_id }}" selected> {{ $packet->invoice_id }} - {{ $packet->invoice->customer->name }}</option>
                                @foreach($invoices as $invoiceId => $customerName)
                                    <option value="{{ $invoiceId }}" {{ $packet->invoice_id == $invoiceId ? 'selected' : '' }}> {{ $invoiceId }} - {{ $customerName }}</option>
                                @endforeach
                            @else
                                <option value="{{ $packet->invoice_id }}" selected> {{ $packet->invoice_id }} - {{ $packet->invoice->customer->name }}</option>
                            @endif
                        </select>
                        @error('invoice')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver">گیرنده <span class="text-danger">*</span></label>
                        <input type="text" name="receiver" class="form-control" id="receiver" value="{{ $packet->receiver }}">
                        @error('receiver')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address">آدرس <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" id="address" value="{{ $packet->address }}">
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sent_time">زمان ارسال <span class="text-danger">*</span></label>
                        <input type="text" name="sent_time" class="form-control date-picker-shamsi-list" id="sent_time" value="{{ verta($packet->sent_time)->format('Y/m/d') }}">
                        @error('sent_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sent_type">نوع ارسال <span class="text-danger">*</span></label>
                        <select class="form-control" name="sent_type" id="sent_type">
                            @foreach(\App\Models\Packet::SENT_TYPE as $key => $value)
                                <option value="{{ $key }}" {{ $packet->sent_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('sent_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="send_tracking_code">کد رهگیری ارسالی</label>
                        <input type="text" name="send_tracking_code" class="form-control" id="send_tracking_code" value="{{ $packet->send_tracking_code }}">
                        @error('send_tracking_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receive_tracking_code">کد رهگیری دریافتی </label>
                        <input type="text" name="receive_tracking_code" class="form-control" id="receive_tracking_code" value="{{ $packet->receive_tracking_code }}">
                        @error('receive_tracking_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="packet_status">وضعیت بسته <span class="text-danger">*</span></label>
                        <select class="form-control" name="packet_status" id="packet_status">
                            @foreach(\App\Models\Packet::PACKET_STATUS as $key => $value)
                                <option value="{{ $key }}" {{ $packet->packet_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('packet_status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice_status">وضعیت فاکتور <span class="text-danger">*</span></label>
                        <select class="form-control" name="invoice_status" id="invoice_status">
                            @foreach(\App\Models\Packet::INVOICE_STATUS as $key => $value)
                                <option value="{{ $key }}" {{ $packet->invoice_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('invoice_status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ $packet->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary" type="submit">ثبت فرم</button>
                    <div>
                        <button class="btn btn-github" type="button" data-toggle="modal" data-target="#smsModal" id="btn_sms">
                            <i class="fa fa-message mr-2"></i>
                            ارسال پیامک
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var code;
            var bodyId;
            var receiver;
            var args;
            var text_error;

            $('#btn_sms').on('click', function (){
                code = $('#send_tracking_code').val().trim();
                receiver = $('#receiver').val();
                bodyId = $('#bodyId').val();
                changeBody(bodyId);
            })

            $('#bodyId').on('change', function () {
                bodyId = $(this).val();
                changeBody(bodyId);
            })

            // btn send sms
            $('#btn_send_sms').on('click', function () {
                let bodyId = $('#bodyId').val();
                let phone_error = false;
                let phone = $('#phone').val().trim();
                let text = $('#text').val()

                if(phone === ''){
                    $('#phone_error').text('شماره موبایل را وارد نمایید')
                    phone_error = true;
                }else{
                    if(phone.length !== 11){
                        $('#phone_error').text('شماره موبایل باید 11 رقم باشد')
                        phone_error = true;
                    }else{
                        $('#phone_error').text('')
                        phone_error = false;
                    }
                }

                if(!phone_error && !text_error){
                    $('#btn_send_sms').attr('disabled','disabled')
                    $('#btn_send_sms span').text('درحال ارسال...')

                    $.ajax({
                        url: "{{ route('sendSMS') }}",
                        type: 'post',
                        data: {
                            bodyId,
                            phone,
                            text,
                            args
                        },
                        success: function (res) {
                            if(res.recId == undefined || res.recId == 11){
                                Swal.fire({
                                    title: 'خطایی رخ داد',
                                    text: res.status,
                                    icon: 'error',
                                    showConfirmButton: false,
                                    toast: true,
                                    timer: 2000,
                                    timerProgressBar: true,
                                    position: 'top-start',
                                    customClass: {
                                        popup: 'my-toast',
                                        icon: 'icon-center',
                                        title: 'left-gap',
                                        content: 'left-gap',
                                    }
                                })
                            }else{
                                Swal.fire({
                                    title: 'با موفقیت ارسال شد',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    toast: true,
                                    timer: 2000,
                                    timerProgressBar: true,
                                    position: 'top-start',
                                    customClass: {
                                        popup: 'my-toast',
                                        icon: 'icon-center',
                                        title: 'left-gap',
                                        content: 'left-gap',
                                    }
                                })

                                // $('#smsModal').modal('hide')
                            }
                            $('#btn_send_sms').removeAttr('disabled')
                            $('#btn_send_sms span').text('ارسال')
                        }
                    })
                }
            })
            // end btn send sms

            function changeBody(bodyId) {
                if (bodyId == 177554){
                    if(code == ''){
                        $('#text_error').text('ابتدا فیلد کد رهگیری را وارد نمایید')
                        text_error = true;
                    }else{
                        $('#text_error').text('')
                        text_error = false;
                    }

                    args = [code];

                    $('#text').html(`کد پیگیری مرسوله شما: ${code} \n\n` +
                        `شرکت صنایع ماشین های اداری ماندگار پارس\n` +
                        `Artintoner.com\n`)
                }else if(bodyId == 185679){
                    if(receiver == ''){
                        $('#text_error').text('ابتدا فیلد گیرنده را وارد نمایید')
                        text_error = true;
                    }else {
                        text_error = false;
                        $('#text_error').text('')
                    }

                    $('#text').html(`مشتری گرامی ${receiver} \n` +
                        `لطفا جهت پرداخت فاکتور خود اقدام نمایید. \n` +
                        ` با تشکر\n` +
                        `شرکت صنایع ماشین های اداری ماندگار پارس \n` +
                        `Artintoner.com`)

                    args = [receiver];
                }else{
                    if(receiver == ''){
                        $('#text_error').text('ابتدا فیلد گیرنده را وارد نمایید')
                        text_error = true;
                    }else {
                        text_error = false;
                        $('#text_error').text('')
                    }

                    $('#text').html(`مشتری گرامی ${receiver} \n` +
                        `لطفا پس از دریافت مرسوله خود، دو نسخه از فاکتورها را مهر و امضا و به آدرس زیر ارسال کنید. با تشکر \n` +
                        `آدرس: تهران، شهرستان ملارد، شهرک صنعتی صفادشت، بلوار خرداد، بین خیابان پنجم و ششم غربی، پلاک 228\n` +
                        `کد پستی: 3164114855 \n` +
                        `شرکت صنایع ماشین های اداری ماندگار پارس \n` +
                        `Artintoner.com`)

                    args = [receiver];
                }
            }
        })
    </script>
@endsection
