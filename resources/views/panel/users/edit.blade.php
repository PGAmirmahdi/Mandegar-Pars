@extends('panel.layouts.master')
@section('title', 'ویرایش کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش کاربر</h6>
            </div>

            <!-- فرم اصلی برای به‌روزرسانی سایر اطلاعات کاربر -->
            <form id="user-form" action="{{ route('users.update', $user->id) }}" method="post"
                  enctype="multipart/form-data">
                @method('put')
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام کاربر<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="family">نام خانوادگی<span class="text-danger">*</span></label>
                        <input type="text" name="family" class="form-control" id="family" value="{{ $user->family }}">
                        @error('family')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone"> شماره تلفن <span class="text-danger">*</span> </label>
                        <input type="text" name="phone" class="form-control" id="phone"
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="gender">
                            <i class="fa fa-venus-mars"></i> جنسیت
                        </label>
                        <select class="form-control" name="gender" id="gender" required>
                            <option value="" disabled selected>انتخاب کنید</option>
                            <option value="male">آقا</option>
                            <option value="female">خانم</option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                @can('admin')
                        @if(auth()->id() != $user->id)
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="role">نقش <span class="text-danger">*</span></label>
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
                    <!-- آپلود تصویر امضا -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sign_image">تصویر امضا<span class="text-danger">*</span></label>
                        <div id="sign-dropzone" class="dropzone"></div>
                        <input type="hidden" name="sign_image" id="sign_image">
                        @error('sign_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="profile">تصویر پروفایل<span class="text-danger">*</span></label>
                        <div id="profile" class="dropzone"></div>
                        <input type="hidden" name="profile" id="profile">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info" id="submit-btn">ثبت فرم</button>
            </form>
        </div>
    </div>

    <!-- استایل‌ها -->
    <style>
        .invalid-feedback {
            color: red;
        }

        .dropzone {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
        }
    </style>

    <!-- اسکریپت‌ها برای Dropzone -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        // Dropzone برای آپلود تصویر امضا
        let signDropzone = new Dropzone("#sign-dropzone", {
            url: "#", // از AJAX استفاده می‌کنیم، پس نیازی به URL نیست
            autoProcessQueue: false,
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png",
            dictDefaultMessage: "تصویر امضا را اینجا بکشید یا کلیک کنید",
            addRemoveLinks: true
        });

        // Dropzone برای آپلود تصویر پروفایل
        let profileDropzone = new Dropzone("#profile", {
            url: "#", // از AJAX استفاده می‌کنیم، پس نیازی به URL نیست
            autoProcessQueue: false,
            maxFiles: 1,
            acceptedFiles: ".jpg,.jpeg,.png",
            dictDefaultMessage: "تصویر پروفایل را اینجا بکشید یا کلیک کنید",
            addRemoveLinks: true
        });

        let submitButton = document.querySelector("#submit-btn");

        submitButton.addEventListener("click", function (e) {
            e.preventDefault();

            // جمع‌آوری داده‌های فرم
            let formElement = document.querySelector("#user-form");
            let formData = new FormData(formElement);

            // اضافه کردن فایل امضا به FormData
            if (signDropzone.getAcceptedFiles().length > 0) {
                formData.append('sign_image', signDropzone.getAcceptedFiles()[0]);
            }

            // اضافه کردن فایل پروفایل به FormData
            if (profileDropzone.getAcceptedFiles().length > 0) {
                formData.append('profile', profileDropzone.getAcceptedFiles()[0]);
            }

            // ارسال درخواست AJAX
            $.ajax({
                url: "{{ route('users.update', $user->id) }}",
                method: 'post',
                data: formData,
                processData: false, // جلوگیری از پردازش خودکار داده‌ها
                contentType: false, // جلوگیری از تنظیم خودکار هدر Content-Type
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (response) {
                    console.log("Form and files uploaded successfully");
                    window.location.href = "{{ route('users.index') }}";
                },
                error: function (xhr) {
                    console.log("Upload failed");
                    console.error(xhr.responseText);
                }
            });
        });
    </script>
@endsection
