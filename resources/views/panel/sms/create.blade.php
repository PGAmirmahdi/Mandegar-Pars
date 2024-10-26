@extends('panel.layouts.master')
@section('title', 'ارسال پیامک')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ارسال پیامک</h6>
            </div>
            <form id="sms-form" action="{{ route('sms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer_id">انتخاب مشتری<span class="text-danger">*</span></label>
                        <select name="customer_id" id="customer_id" class="js-example-basic-single">
                            <option value="">یک مشتری انتخاب کنید</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                        data-phone="{{ $customer->phone1 }}">
                                    {{ $customer->name }}
                                </option>
                        @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_name">نام گیرنده<span class="text-danger">*</span></label>
                        <input type="text" name="receiver_name" class="form-control" id="receiver_name"
                               value="{{ old('receiver_name') }}">
                        @error('receiver_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_phone">شماره گیرنده<span class="text-danger">*</span></label>
                        <input type="text" name="receiver_phone" class="form-control" id="receiver_phone"
                               value="{{ old('receiver_phone') }}">
                        @error('receiver_phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="message">متن پیام<span class="text-danger">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="10">
با عرض سلام خدمت [نام مخاطب]
خوشحالیم که شما را در جمع مشتریان ارزشمند خود داریم. برای اطلاع از جدیدترین اخبار و پیشنهادات ویژه، ما را در صفحات اجتماعی دنبال کنید:

دانلود کاتالوگ محصولات:
https://artintoner.com/folder/Catalog-v1.3.10.pdf

فروشگاه اینترنتی:
https://artintoner.com

اپلیکیشن:
https://mpsystem.ir/Discover

اینستاگرام:
www.instagram.com/artintoner.ir

شماره تماس
02165425052-54
09906424827
09014667657
09027386996
با سپاس،
ماندگارپارس
                        </textarea>
                        @error('message')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ارسال پیامک</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var receiverNameInput = $('#receiver_name');
            var receiverPhoneInput = $('#receiver_phone');
            var messageTextarea = $('#message');
            var defaultMessage = messageTextarea.val();

            $('#customer_id').on('change', function () {
                var selectedOption = $(this).find('option:selected');
                var customerName = selectedOption.data('name');
                var customerPhone = selectedOption.data('phone');

                receiverNameInput.val(customerName);
                receiverPhoneInput.val(customerPhone);

                // Updating message with customer name
                var personalizedMessage = defaultMessage.replace('[نام مخاطب]', customerName);
                messageTextarea.val(personalizedMessage);
            });
        });
    </script>
@endsection
