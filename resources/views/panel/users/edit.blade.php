@extends('panel.layouts.master')
@section('title', 'ویرایش کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش کاربر</h6>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Dropzone برای آپلود تصویر امضاء -->
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="sign_image">تصویر امضاء (PNG)</label>
                    <div class="dropzone" id="sign-image-dropzone"></div>
                    @if($user->sign_image)
                        <a href="{{ $user->sign_image }}" class="btn btn-link" target="_blank">مشاهده امضاء</a>
                    @endif
                    @error('sign_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dropzone برای آپلود تصویر پروفایل -->
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="profile">عکس پروفایل</label>
                    <div class="dropzone" id="profile-dropzone"></div>
                    @if($user->profile)
                        <a href="{{ asset('storage/' . $user->profile) }}" class="btn btn-link" target="_blank">مشاهده پروفایل</a>
                    @endif
                    @error('profile')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- سایر فیلدها -->
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="family">نام خانوادگی <span class="text-danger">*</span></label>
                        <input type="text" name="family" class="form-control" id="family" value="{{ $user->family }}">
                        @error('family')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone">شماره موبایل <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ $user->phone }}">
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="password">رمزعبور</label>
                        <input type="password" name="password" class="form-control" id="password">
                        @error('password')
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
                </div>

                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>

    <!-- تنظیمات Dropzone -->
    <script>
        Dropzone.autoDiscover = false;

        // Dropzone برای تصویر امضاء
        var signImageDropzone = new Dropzone("#sign-image-dropzone", {
            url: "{{ route('users.update', $user->id) }}",
            paramName: "sign_image", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".png", // فرمت‌های مجاز
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                console.log(response);
            },
            error: function (file, response) {
                console.log(response);
            }
        });

        // Dropzone برای تصویر پروفایل
        var profileDropzone = new Dropzone("#profile-dropzone", {
            url: "{{ route('users.update', $user->id) }}",
            paramName: "profile", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".jpeg,.jpg,.png,.gif", // فرمت‌های مجاز
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function (file, response) {
                console.log(response);
            },
            error: function (file, response) {
                console.log(response);
            }
        });
    </script>
@endsection
