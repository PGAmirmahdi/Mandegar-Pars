@extends('panel.layouts.master')
@section('title', 'اطلاعات شرکت')

@section('content')
    <div class="mx-0 ">
        <div class="card mt-5">
            <div class="card-header bg-primary text-white text-center">
                اطلاعات مربوط به شرکت
            </div>
            <div class="card-body row"> <!-- Use Bootstrap row class for layout -->
                <div class="mb-3 col-lg-6 col-md-6 col-sm-12">
                    <h5 class="card-title">اطلاعات پایه</h5>
                    <p onclick="copyToClipboard('شخص حقوقی', this)"> نوع مودی: <strong>شخص حقوقی</strong></p>
                    <p onclick="copyToClipboard('صنایع ماشین های اداری ماندگار پارس', this)">نام شرکت/نام تجاری: <strong>صنایع ماشین های اداری ماندگار پارس</strong></p>
                    <p onclick="copyToClipboard('14011383061', this)"> شماره اقتصادی/کد ملی: <strong>14011383061</strong></p>
                    <p onclick="copyToClipboard('خصوصی', this)"> نوع مالکیت: <strong>خصوصی</strong></p>
                    <p onclick="copyToClipboard('مسئولیت محدود', this)"> نوع شخص حقوقی: <strong>مسئولیت محدود</strong></p>
                    <p onclick="copyToClipboard('9931', this)"> شماره ثبت: <strong>9931</strong></p>
                    <p onclick="copyToClipboard('515311', this)"> کد ایسیک: <strong>515311</strong></p>
                    <p onclick="copyToClipboard('1401/05/10', this)"> تاریخ ثبت: <strong>1401/05/10</strong></p>
                    <p onclick="copyToClipboard('1401/04/01', this)"> تاریخ شروع فعالیت: <strong>1401/04/01</strong></p>
                    <p onclick="copyToClipboard('تجاری', this)"> نوع فعالیت: <strong>تجاری</strong></p>
                </div>
                <div class="mb-3 col-lg-3 col-md-6 col-sm-12">
                    <h5 class="card-title">اطلاعات مدیر عامل</h5>
                    <p onclick="copyToClipboard('جناب آقای غلامی نظامی', this)"> نام: <strong>جناب آقای غلامی نظامی</strong></p>
                    <p onclick="copyToClipboard('09121447598', this)">شماره تماس: <strong>09121447598</strong></p>
                    <p onclick="copyToClipboard('0010963601', this)">شماره ملی: <strong>0010963601</strong></p>
                    <p onclick="copyToClipboard('1368/06/24', this)">تاریخ تولد: <strong>1368/06/24</strong></p>
                    <p onclick="copyToClipboard('1402443503', this)">شماره صنفی: <strong>1402443503</strong></p>
                </div>
                <div class="mb-3 col-lg-3 col-md-6 col-sm-12">
                    <h5 class="card-title">اطلاعات تماس</h5>
                    <p onclick="copyToClipboard('mandegarparsco@gmail.com', this)">ایمیل: <strong>mandegarparsco@gmail.com</strong></p>
                    <p onclick="copyToClipboard('www.artintoner.com', this)">وب‌سایت: <strong>www.artintoner.com</strong></p>
                    <p onclick="copyToClipboard('www.mpsystem.ir', this)">اتوماسیون: <strong>www.mpsystem.ir</strong></p>
                    <p onclick="copyToClipboard('02165425052-4', this)">شماره های تماس: <strong>02165425052-4</strong></p>
                    <p onclick="copyToClipboard('3164114855', this)">کد پستی: <strong>3164114855</strong></p>
                    <p onclick="copyToClipboard('تهران', this)">استان: <strong>تهران</strong></p>
                    <p onclick="copyToClipboard('ملارد', this)">شهرستان: <strong>ملارد</strong></p>
                    <p onclick="copyToClipboard('صفادشت', this)">شهر: <strong>صفادشت</strong></p>
                    <p onclick="copyToClipboard('صفادشت،بلوار خرداد،بین خیابان پنجم و ششم غربی،پلاک 228', this)">آدرس: <strong>صفادشت،بلوار خرداد،بین خیابان پنجم و ششم غربی،پلاک 228</strong></p>
                </div>
                <div class="mb-3 col-lg-3 col-md-6 col-sm-12">
                    <h5 class="card-title">اطلاعات حساب بانکی</h5>
                    <p onclick="copyToClipboard('0101', this)">کد شعبه: <strong>0101    </strong></p>
                    <p onclick="copyToClipboard('0103967138001', this)">شماره حساب: <strong>0103967138001</strong></p>
                    <p onclick="copyToClipboard('IR550110000000103967138001', this)">شماره شبا: <strong>IR550110000000103967138001</strong></p>
                    <p onclick="copyToClipboard('www.mpsystem.ir', this)">اتوماسیون: <strong>www.mpsystem.ir</strong></p>
                    <p onclick="copyToClipboard('02165425052-4', this)">شماره های تماس: <strong>02165425052-4</strong></p>
                </div>
{{--                <div class="mb-3 col-lg-3 col-md-6 col-sm-12">--}}
{{--                    <h5 class="card-title">فایلهای مربوطه</h5>--}}
{{--                    <a href="{{asset('assets/media/image/Info/Parvane_Kasb.jpg')}}"><img src="{{asset('assets/media/image/Info/Parvane_Kasb.jpg')}}" alt="پروانه کسب" width="200px" height="fit-content"><strong>پروانه کسب</strong></a>--}}
{{--                </div>--}}
            </div>
        </div>
{{--        <div id="copyNotification" class="notification"></div>--}}
    </div>

    <script>
        function copyToClipboard(text, element) {
            // Create a temporary input element to hold the text
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // Show "کپی شد!" next to the item
            const copiedText = document.createElement('span');
            copiedText.className = 'copy-highlight text-success';
            copiedText.textContent = '  کپی شد!';
            element.appendChild(copiedText);

            // Hide the "کپی شد!" message after 3 seconds
            setTimeout(() => {
                element.removeChild(copiedText);
            }, 3000);

            // Show notification
            const notification = document.getElementById('copyNotification');
            notification.textContent = text + ' کپی شد!';
            notification.style.display = 'block';
            notification.style.opacity = 1;

            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.style.opacity = 0;
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 500); // Delay before hiding
            }, 3000);
        }
    </script>
@endsection
