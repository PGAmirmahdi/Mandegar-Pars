@extends('panel.layouts.master')
@section('title', 'اطلاعات شرکت')
@section('content')
    <div class="mx-0 bg-white">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>اطلاعات شرکت</h6>
                    @can('information')
                        <a href="{{ route('baseinfo.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            افزودن اطلاعات
                        </a>
                    @endcan
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
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>نام شرکت/نام تجاری</td>
                                <td>صنایع ماشین های اداری ماندگار پارس</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            @can('information')
                                <tr>
                                    <td>شماره اقتصادی/کد ملی</td>
                                    <td>14011383061</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>نوع مالکیت</td>
                                    <td>خصوصی</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>نوع شخص حقوقی</td>
                                    <td>مسئولیت محدود</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>شماره ثبت</td>
                                    <td>9931</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>کد ایسیک</td>
                                    <td>515311</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>تاریخ ثبت</td>
                                    <td>1401/05/10</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>تاریخ شروع فعالیت</td>
                                    <td>1401/04/01</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>نوع فعالیت</td>
                                    <td>تجاری</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
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
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>شماره حساب</td>
                                    <td>0103967138001</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>شماره شبا</td>
                                    <td>IR550110000000103967138001</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                @foreach($baseInfos as $baseInfo)
                                    <tr>
                                        <td>{{$baseInfo->title}}</td>
                                        <td>{{$baseInfo->info}}</td>
                                        <td>
                                            <button class="btn btn-primary btn-floating mx-1"
                                                    onclick="copyRowData(this)"><i class="fa-solid fa-copy"></i>
                                            </button>
                                            <a class="btn btn-warning btn-floating mx-1"
                                               href="{{ route('baseinfo.edit', $baseInfo->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-floating trashRow mx-1"
                                                    data-url="{{ route('baseinfo.destroy',$baseInfo->id) }}"
                                                    data-id="{{ $baseInfo->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
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
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>سمت</td>
                                <td>مدیرعامل</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            @can('information')
                                <tr>
                                    <td>شماره ملی</td>
                                    <td>0010963601</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>تلفن همراه</td>
                                    <td>09121447598</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>تاریخ تولد</td>
                                    <td>1368/06/24</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>شماره صنفی</td>
                                    <td>1402443503</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
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
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>شهرستان</td>
                                <td>صفادشت</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>آدرس</td>
                                <td>صفادشت،بلوار خرداد،بین خیابان پنجم و ششم غربی،پلاک 228</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            @can('information')
                                <tr>
                                    <td>کد پستی</td>
                                    <td>3164114855</td>
                                    <td>
                                        <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                                class="fa-solid fa-copy"></i></button>
                                    </td>
                                </tr>
                            @endcan
                            <tr>
                                <td>شماره تماس</td>
                                <td>02165425052-4</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>ایمیل</td>
                                <td>mandegarparsco@gmail.com</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>سایت</td>
                                <td>www.artintoner.com</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>اتوماسیون</td>
                                <td>www.mpsystem.ir</td>
                                <td>
                                    <button class="btn btn-primary btn-floating" onclick="copyRowData(this)"><i
                                            class="fa-solid fa-copy"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-header bg-primary text-white text-center">
                        فایل اطلاعاتی
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                            <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>تصویر</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @can('information')
                                    <td>پروانه کسب</td>
                                    <td>
                                        <img src="{{ asset('assets/media/image/Info/Parvane_Kasb.jpg') }}"
                                             alt="پروانه کسب"
                                             width="125px"
                                             style="height:auto;">
                                    </td>
                                    <td>
                                        <a href="{{ asset('assets/media/image/Info/Parvane_Kasb.jpg') }}"
                                           class="btn btn-primary btn-floating"
                                           download onclick="downloadRowData(this)">
                                            <i class="fa-solid fa-down-long"></i>
                                        </a>
                                    </td>
                            </tr>
                            <tr>
                                <td>گواهی عضویت</td>
                                <td>
                                    <img src="{{ asset('assets/media/image/Info/Govahi_Ozviat.jpg') }}"
                                         alt="گواهی عضویت"
                                         width="125px"
                                         style="height:auto;">
                                </td>
                                <td>
                                    <a href="{{ asset('assets/media/image/Info/Govahi_Ozviat.jpg.jpg') }}"
                                       class="btn btn-primary btn-floating"
                                       download onclick="downloadRowData(this)">
                                        <i class="fa-solid fa-down-long"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>روزنامه رسمی</td>
                                <td>
                                    <img src="{{ asset('assets/media/image/Info/Rouzname.png') }}"
                                         alt="روزنامه رسمی"
                                         width="125px"
                                         style="height:auto;">
                                </td>
                                <td>
                                    <a href="{{ asset('assets/media/image/Info/Rouzname.png') }}"
                                       class="btn btn-primary btn-floating"
                                       download onclick="downloadRowData(this)">
                                        <i class="fa-solid fa-down-long"></i>
                                    </a>
                                </td>
                                @else
                                    <td colspan="3">خالی</td>
                                @endcan
                            </tr>
                            </tbody>
                        </table>
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

                    // <i class="fa-solid fa-copy"></i> کردن متن به کلیپ‌بورد
                    const tempInput = document.createElement('input');
                    tempInput.value = textToCopy;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);

                    // نمایش پیام <i class="fa-solid fa-copy"></i> شدن اطلاعات
                    button.textContent = 'کپی شد!';
                    setTimeout(function () {
                        button.textContent = 'کپی مجدد';
                    }, 1500); // بعد از 1.5 ثانیه دکمه به حالت اولیه باز می‌گردد
                }

                function downloadRowData(a) {
                    // تغییر متن دکمه به "در حال دانلود"
                    const originalText = a.textContent; // ذخیره متن اصلی
                    a.textContent = 'در حال دانلود!';

                    // افزودن کلاس غیرفعال برای جلوگیری از کلیک مجدد تا پایان عملیات
                    a.classList.add('disabled');

                    // بازگرداندن متن دکمه به حالت اصلی پس از 1.5 ثانیه
                    setTimeout(function () {
                        a.textContent = originalText; // بازگرداندن متن اصلی
                        a.classList.remove('disabled'); // فعال کردن مجدد دکمه
                    }, 1500);
                }
            </script>
@endsection
