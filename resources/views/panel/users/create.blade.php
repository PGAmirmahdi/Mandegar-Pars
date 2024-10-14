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

                    <!-- فیلد آپلود عکس پروفایل -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <input type="file" name="profile" id="profile-input">
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="submit-button">ثبت فرم</button>
                <div id="dynamic-button-container"></div>
                <div class="m-2"><label for="profile">عکس پروفایل</label></div>
            </form>
        </div>
    </div>

    <!-- تنظیمات Dropzone -->
    <script>
        document.getElementById('profile-input').addEventListener('change', function() {
            const dynamicButtonContainer = document.getElementById('dynamic-button-container');
            const submitButton = document.getElementById('submit-button');

            // حذف دکمه submit
            if (submitButton) {
                submitButton.remove(); // حذف دکمه submit
            }

            // بررسی اینکه آیا دکمه قبلاً وجود دارد یا خیر
            if (document.getElementById('dynamic-submit-button')) {
                return; // اگر دکمه وجود دارد، هیچ کاری نکنید
            }

            // ایجاد دکمه جدید
            const newButton = document.createElement('button');
            newButton.id = 'dynamic-submit-button';
            newButton.className = 'btn btn-secondary m-2'; // کلاس دکمه
            newButton.innerText = 'رفتن به صفحه کاربران'; // متن دکمه
            newButton.type = 'button'; // نوع دکمه را به button تنظیم کنید

            // اضافه کردن رویداد کلیک به دکمه جدید
            newButton.addEventListener('click', function() {
                window.location.href = "{{ route('users.index') }}"; // هدایت به صفحه users.index
            });

            // اضافه کردن دکمه جدید به DOM
            dynamicButtonContainer.appendChild(newButton);
        });
    </script>
@endsection
