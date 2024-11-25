@extends('panel.layouts.master')
@section('title', 'اطلاعات شرکت')
@section('content')
    <style>
        .download-btn {
            transition: background-color 0.3s, color 0.3s;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        .download-btn.loading {
            background-color: #6f42c1;
            color: #fff;
            cursor: not-allowed;
        }
    </style>
    <div class="mx-0 bg-white">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>اطلاعات شرکت</h6>
                    @can('information')
                        <a href="{{ route('baseinfo.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i> افزودن اطلاعات
                        </a>
                    @endcan
                </div>
                @foreach(['base' => 'اطلاعات پایه', 'bank' => 'اطلاعات حساب بانکی', 'manager' => 'اطلاعات مدیر عامل', 'call' => 'اطلاعات تماس'] as $type => $header2)
                    <div class="card-header bg-primary text-white text-center">
                        {{ $header2 }}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center dataTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان</th>
                                <th>اطلاعات</th>
                                <th>عملیات</th>
                                <th>دسترسی</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $counter = 0; // تعریف شمارنده کلی
                            @endphp
                            @foreach($baseInfos->where('type', $type) as $key => $info)
                                @if($info->access == 'public' || auth()->user()->can('information'))
                                    <tr>
                                        <td>{{ ++$counter }}</td>
                                        <td>{{ $info->title }}</td>
                                        <td>{{ $info->info }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-floating mx-1 copy-btn"
                                                    data-info="{{ $info->info }}">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                            @can('information')
                                                <a class="btn btn-warning btn-floating mx-1"
                                                   href="{{ route('baseinfo.edit', $info->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button class="btn btn-danger btn-floating mx-1 trash-btn"
                                                        data-url="{{ route('baseinfo.destroy', $info->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endcan
                                        </td>
                                        <td>
                                <span class="badge {{ $info->access == 'private' ? 'badge-danger' : 'badge-success' }}">
                                    <i class="fa-solid {{ $info->access == 'private' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
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
                            <th>دسترسی</th>
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
                                       class="btn btn-primary btn-floating download-btn"
                                       download onclick="downloadRowData(this)">
                                        <i class="fa-solid fa-down-long"></i>
                                    </a>
                                </td>
                                <td><i class="badge badge-danger"><i class="fa-solid fa-lock"></i></i></td>
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
                                <a href="{{ asset('assets/media/image/Info/Govahi_Ozviat.jpg') }}"
                                   class="btn btn-primary btn-floating download-btn"
                                   download onclick="downloadRowData(this)">
                                    <i class="fa-solid fa-down-long"></i>
                                </a>
                            </td>
                            <td><i class="badge badge-danger"><i class="fa-solid fa-lock"></i></i></td>
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
                                   class="btn btn-primary btn-floating download-btn"
                                   download onclick="downloadRowData(this)">
                                    <i class="fa-solid fa-down-long"></i>
                                </a>
                            </td>
                            <td><i class="badge badge-danger"><i class="fa-solid fa-lock"></i></i></td>
                            @else
                                <td colspan="4">خالی</td>
                            @endcan
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Copy Button Functionality
            document.querySelectorAll('.copy-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const text = button.dataset.info;
                    navigator.clipboard.writeText(text).then(() => {
                        button.textContent = 'کپی شد!';
                        setTimeout(() => button.textContent = '', 1500);
                    });
                });
            });

            // Trash Button Functionality
            document.querySelectorAll('.trash-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const url = button.dataset.url;
                    if (confirm('آیا مطمئن هستید؟')) {
                        fetch(url, {method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                            .then(response => response.json())
                            .then(data => location.reload());
                    }
                });
            });
        });
        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("download-btn")) {
                const button = e.target;

                // تغییر متن دکمه به "درحال دانلود!"
                button.textContent = "درحال دانلود!";
                button.disabled = true; // غیرفعال کردن دکمه

                // شبیه‌سازی دانلود
                setTimeout(() => {
                    button.textContent = "دانلود"; // بازگشت به متن اولیه
                    button.disabled = false; // فعال کردن دوباره دکمه
                }, 3000); // مدت زمان شبیه‌سازی
            }
        });
    </script>
@endsection
