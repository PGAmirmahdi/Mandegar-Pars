@extends('panel.layouts.master')
@section('title', 'اطلاعات شرکت')
@section('content')
    <div class="mx-0">
        <div class="card mt-5">
            <div class="card-header bg-primary text-white text-center">
                اطلاعات مربوط به شرکت
            </div>
            <div class="card-body">
                <!-- اطلاعات پایه -->
                <div class="card-header bg-primary text-white text-center">
                    اطلاعات پایه
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>اطلاعات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>نوع مودی</td>
                            <td>شخص حقوقی</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>نام شرکت/نام تجاری</td>
                            <td>صنایع ماشین های اداری ماندگار پارس</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        @can('information')
                        <tr>
                            <td>شماره اقتصادی/کد ملی</td>
                            <td>14011383061</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>نوع مالکیت</td>
                            <td>خصوصی</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>نوع شخص حقوقی</td>
                            <td>مسئولیت محدود</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>شماره ثبت</td>
                            <td>9931</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>کد ایسیک</td>
                            <td>515311</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>تاریخ ثبت</td>
                            <td>1401/05/10</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>تاریخ شروع فعالیت</td>
                            <td>1401/04/01</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>نوع فعالیت</td>
                            <td>تجاری</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                            @endcan
                        </tbody>
                    </table>
                </div>
                @can('information')
                <div class="card-header bg-primary text-white text-center">
                    اطلاعات حساب بانکی
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>اطلاعات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>کد شعبه</td>
                            <td>0101</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>شماره حساب</td>
                            <td>0103967138001</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>شماره شبا</td>
                            <td>IR550110000000103967138001</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @endcan
                <!-- اطلاعات مدیرعامل -->
                <div class="card-header bg-primary text-white text-center">
                    اطلاعات مدیر عامل
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>اطلاعات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>نام مدیرعامل</td>
                            <td>غلامی نظامی</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>سمت</td>
                            <td>مدیرعامل</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        @can('information')
                        <tr>
                            <td>شماره ملی</td>
                            <td>0010963601</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>تلفن همراه</td>
                            <td>09121447598</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>تاریخ تولد</td>
                            <td>1368/06/24</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>شماره صنفی</td>
                            <td>1402443503</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        @endcan
                        </tbody>
                    </table>
                </div>
                <!-- اطلاعات تماس -->
                <div class="card-header bg-primary text-white text-center">
                    اطلاعات تماس
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>اطلاعات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>استان</td>
                            <td>تهران</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>شهرستان</td>
                            <td>صفادشت</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>آدرس</td>
                            <td>صفادشت،بلوار خرداد،بین خیابان پنجم و ششم غربی،پلاک 228</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        @can('information')
                            <tr>
                                <td>کد پستی</td>
                                <td>3164114855</td>
                                <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                            </tr>
                        @endcan
                        <tr>
                            <td>شماره تماس</td>
                            <td>02165425052-4</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>ایمیل</td>
                            <td>mandegarparsco@gmail.com</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>سایت</td>
                            <td>www.artintoner.com</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        <tr>
                            <td>اتوماسیون</td>
                            <td>www.mpsystem.ir</td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">کپی</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-header bg-primary text-white text-center">
                    اطلاعات تماس
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>عنوان</th>
                            <th>اطلاعات</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>پروانه کسب</td>
                            <td><a href="{{asset('assets/media/image/Info/Parvane_Kasb.jpg')}}"><img
                                        src="{{asset('assets/media/image/Info/Parvane_Kasb.jpg')}}" alt="پروانه کسب"
                                        width="125px"
                                        height="fit-content"></a></td>
                            <td><button class="btn btn-primary" onclick="copyRowData(this)">دانلود</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyRowData(button) {
            // گرفتن داده‌های ردیف
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');
            let textToCopy = '';

            // جمع‌آوری متن از ستون اطلاعات (ستون اول را نگیریم)
            textToCopy = cells[1].textContent.trim();

            // کپی کردن متن به کلیپ‌بورد
            const tempInput = document.createElement('input');
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // نمایش پیام کپی شدن اطلاعات
            button.textContent = 'کپی شد!';
            setTimeout(function () {
                button.textContent = 'کپی';
            }, 1500); // بعد از 1.5 ثانیه دکمه به حالت اولیه باز می‌گردد
        }
    </script>
@endsection
