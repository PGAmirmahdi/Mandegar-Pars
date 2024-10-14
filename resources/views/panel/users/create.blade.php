@extends('panel.layouts.master')
@section('title', 'ایجاد کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد کاربر</h6>
            </div>
            <form id="userForm" action="{{ route('users.store') }}" method="post" enctype="multipart/form-data" class="dropzone">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="family">نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="family" class="form-control" id="family" value="{{ old('family') }}">
                        @error('family')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone">شماره موبایل <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="password">رمزعبور <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" id="password" value="{{ old('password') }}">
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="role">نقش <span class="text-danger">*</span></label>
                        <select class="form-control" name="role" id="role">
                            @foreach(\App\Models\Role::all() as $role)
                                <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- فیلد آپلود عکس پروفایل با Dropzone -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="profile">عکس پروفایل <span class="text-danger">*</span></label>
                        <input type="file" name="profile" id="profile">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>

    <!-- تنظیمات Dropzone -->
    <script>
        Dropzone.autoDiscover = false;

        var profileDropzone = new Dropzone("#profile", {
            url: "{{ route('users.store') }}", // آدرس API آپلود فایل
            paramName: "profile", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".jpeg,.jpg,.png,.gif", // فرمت‌های مجاز
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                // در صورت موفقیت آپلود
                console.log(response);
            },
            error: function (file, response) {
                // در صورت خطا در آپلود
                console.log(response);
            }
        });

        // وقتی که فرم ارسال می‌شود
        document.getElementById('userForm').onsubmit = function () {
            // بررسی اینکه آیا فایل آپلود شده است یا خیر
            if (profileDropzone.getAcceptedFiles().length === 0) {
                alert("لطفاً یک فایل برای آپلود انتخاب کنید.");
                return false; // جلوگیری از ارسال فرم
            }
        };
    </script>
@endsection
