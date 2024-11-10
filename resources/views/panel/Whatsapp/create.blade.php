@extends('panel.layouts.master')
@section('title', 'ارسال پیام واتساپ')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ارسال پیام واتساپ</h6>
                <button type="button" id="add-recipient" class="btn btn-primary">اضافه کردن گیرنده</button>
            </div>
            <form id="sms-form" action="{{ route('whatsapp.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="d-flex row justify-content-start align-items-center">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="manual_name">نام گیرنده</label>
                        <input type="text" class="form-control" id="manual_name" placeholder="نام گیرنده را وارد کنید">
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="manual_phone">شماره گیرنده</label>
                        <input type="text" class="form-control" id="manual_phone"
                               placeholder="شماره گیرنده را وارد کنید">
                    </div>
                </div>

                <div id="recipient-container"></div> <!-- محل قرارگیری فیلدهای نام و شماره گیرنده -->

                <div class="col-12 mb-3">
                    <label for="description">متن پیام<span class="text-danger">*</span></label>
                    <textarea id="description" name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="10">{{ old('description', $defaultMessage) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <input type="hidden" name="phones" id="phones"> <!-- فیلد مخفی برای ارسال شماره‌ها به صورت JSON -->
                <input type="hidden" name="receiver_names" id="receiver_names"> <!-- فیلد مخفی برای ارسال نام‌ها -->
                <button class="btn btn-primary" type="submit">ارسال پیامک</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var phonesArray = [];
            var namesArray = [];  // آرایه برای ذخیره نام‌ها
            var defaultMessage = `با عرض سلام خدمت
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
ماندگارپارس`;

            $('#add-recipient').on('click', function () {
                var customerName = $('#manual_name').val();
                var customerPhone = $('#manual_phone').val();

                // بررسی که نام و شماره وارد شده باشد
                if (customerName && customerPhone) {
                    phonesArray.push(customerPhone);
                    namesArray.push(customerName);

                    // ایجاد یک مجموعه جدید از فیلدهای نام و شماره گیرنده
                    var newRecipientFields = `
            <div class="recipient-fields mb-3">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label>نام گیرنده</label>
                        <input type="text" class="form-control" value="${customerName}" readonly>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label>شماره گیرنده</label>
                        <input type="text" class="form-control" value="${customerPhone}" readonly>
                    </div>
                </div>
            </div>`;

                    // اضافه کردن فیلدهای جدید به recipient-container
                    $('#recipient-container').append(newRecipientFields);

                    // تنظیم پیام با نام مخاطب
                    var personalizedMessage = defaultMessage.replace('[نام مخاطب]', customerName);
                    $('#description').val(personalizedMessage);

                    // تنظیم آرایه شماره‌ها و نام‌ها به صورت JSON در فیلد مخفی
                    $('#phones').val(JSON.stringify(phonesArray));
                    $('#receiver_names').val(JSON.stringify(namesArray));  // ارسال نام‌ها
                } else {
                    alert('لطفاً نام و شماره گیرنده را وارد کنید.');
                }
            });
        });

    </script>
@endsection
