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
                        <input type="hidden" name="profile" id="profile">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
                <div>
                    <label for="profile">عکس پروفایل <span class="text-danger">*</span></label>
                </div>
            </form>
        </div>
    </div>

    <!-- تنظیمات Dropzone -->
    <script>
        Dropzone.autoDiscover = false;

        // تنظیم Dropzone برای input فایل
        var profileDropzone = new Dropzone("#profile-input", {
            url: "{{ route('profile.upload') }}", // مسیر آپلود فایل
            paramName: "profile", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (MB)
            acceptedFiles: ".jpeg,.jpg,.png,.gif", // فرمت‌های مجاز
            autoProcessQueue: false, // جلوگیری از آپلود خودکار
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                var myDropzone = this;

                // آپلود فایل‌ها هنگام کلیک بر روی دکمه ثبت
                document.getElementById("submit-btn").addEventListener("click", function(e) {
                    e.preventDefault();
                    if (myDropzone.getQueuedFiles().length > 0) {
                        myDropzone.processQueue(); // آپلود فایل‌ها
                    } else {
                        document.getElementById("user-form").submit(); // اگر فایلی در Dropzone نیست، فرم ارسال شود
                    }
                });

                // آپلود موفق فایل
                myDropzone.on("success", function(file, response) {
                    // مقدار مسیر فایل آپلود شده را در input hidden قرار دهید
                    document.getElementById("profile-path").value = response.filepath;

                    // بعد از آپلود فایل، فرم ارسال شود
                    document.getElementById("user-form").submit();
                });

                // خطا در آپلود
                myDropzone.on("error", function(file, response) {
                    console.log(response);
                });
            }
        });
    </script>
@endsection
