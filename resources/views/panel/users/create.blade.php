@extends('panel.layouts.master')
@section('title', 'ایجاد کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد کاربر</h6>
            </div>
            <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data"
                  class="dropzone" id="my-awesome-dropzone">
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
                        <input type="hidden" name="profile">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="submit-button">ثبت فرم</button>
                <div class="m-2"><label for="profile">عکس پروفایل</label></div>
            </form>
        </div>
    </div>

    <!-- تنظیمات Dropzone -->
        <script>
            // فرض کنید که متغیر role از سمت سرور به این صورت تعریف شده است
            var userRole = "{{ auth()->user()->role }}"; // دریافت نقش کاربر

            Dropzone.autoDiscover = false; // غیرفعال کردن auto discover

            var profileDropzone = new Dropzone("#profile-dropzone", {
            url: "{{ route('users.store') }}", // آدرس API آپلود فایل
            paramName: "profile", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".jpeg,.jpg,.png,.gif", // فرمت‌های مجاز
            addRemoveLinks: true,
            headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}" // توکن CSRF
        },
            autoProcessQueue: false, // غیرفعال کردن پردازش خودکار صف

            success: function(file, response) {
            console.log("ممنونتم مهندس: ", response);

            // هدایت بر اساس نقش کاربر
            if (userRole === 'admin') {
            window.location.href = "{{ route('users.index') }}"; // هدایت به صفحه users.index
        } else {
            window.location.href = "{{ route('users.edit') }}"; // هدایت به صفحه dashboard.index
        }
        },
            error: function(file, response) {
            console.error("Upload error: ", response);
        }
        });

            // پردازش صف هنگام کلیک بر روی دکمه ارسال
            document.getElementById("submit-button").addEventListener("click", function() {
            if (profileDropzone.getQueuedFiles().length > 0) {
            profileDropzone.processQueue(); // پردازش صف
        } else {
            console.log("هیچ فایلی برای آپلود وجود ندارد.");
        }
        });
    </script>
@endsection
