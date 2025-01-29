@extends('panel.layouts.master')
@section('title', 'کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>کاربران</h6>
                @can('users-create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد کاربر
                    </a>
                @endcan
            </div>
            <form id="user-form" action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام کاربر<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="family">نام خانوادگی<span class="text-danger">*</span></label>
                        <input type="text" name="family" class="form-control" id="family" value="{{ old('family') }}">
                        @error('family')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone"> شماره تلفن <span class="text-danger">*</span> </label>
                        <input type="text" name="phone" class="form-control" id="phone"
                               value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="gender">جنسیت</label>
                        <select class="form-control" name="gender">
                            <option value="" disabled selected>انتخاب کنید</option>
                            <option value="male">آقا<i class="fa fa-male"></i></option>
                            <option value="female">خانم<i class="fa fa-female"></i></option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    @can('admin')
                        @if(auth()->id() != $user->id)
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="role">نقش<span class="text-danger">*</span></label>
                                <select class="form-control" name="role" id="role">
                                    @foreach(\App\Models\Role::all() as $role)
                                        <option
                                            value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    @endcan
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="password">رمز عبور <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="profile">تصویر پروفایل<span class="text-danger">*</span></label>
                        <input type="file" name="profile" id="profile" class="dropzone">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal" id="uploadModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <p class="loading-text">در حال بارگذاری</p>
                                    <div class="progress-circle">
                                        <svg viewBox="0 0 36 36" class="circular-chart">
                                            <path class="circle-bg"
                                                  d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            <path class="circle"
                                                  stroke-dasharray="0, 100"
                                                  d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                            <text x="18" y="20.35" class="percentage">0%</text>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="submit_button">بارگذاری</button>
            </form>
        </div>
    </div>

    <style>
        .modal-content {
            width: 300px;
            background-color: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(6.9px);
            box-shadow: 0px 5px 5px 2px gainsboro;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }

        .progress-circle {
            width: 100px;
            margin: auto;
        }

        .circular-chart {
            display: block;
            max-width: 100%;
            max-height: 100%;
        }

        .circle-bg {
            fill: none;
            stroke: rgba(255, 255, 255, 0.72);
            stroke-width: 3.8;
        }

        .circle {
            fill: none;
            stroke-width: 2.8;
            stroke-linecap: round;
            stroke: #9418ff;
            transition: stroke-dasharray 0.3s;
        }

        .percentage {
            fill: #666;
            font-family: sans-serif;
            font-size: 0.5em;
            text-anchor: middle;
        }

        .loading-text {
            font-size: 16px;
            font-weight: bold;
            color: #555;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
        $(document).ready(function () {
            var form = $('#user-form');
            var modal = $('#uploadModal');
            var circle = $('.circle');
            var percentageText = $('.percentage');
            var loadingText = $('.loading-text');

            form.on('submit', function (event) {
                event.preventDefault();
                var formData = new FormData(this);
                modal.css('display', 'flex');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                circle.attr('stroke-dasharray', percentComplete + ', 100');
                                percentageText.text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (response) {
                        modal.hide();
                        window.location.href = "{{ route('users.index') }}";
                    },
                    error: function (xhr) {
                        modal.hide();
                        alert('خطا در آپلود فایل!');
                    }
                });
            });
        });
    </script>

@endsection
