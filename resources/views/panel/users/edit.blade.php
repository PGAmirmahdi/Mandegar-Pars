@extends('panel.layouts.master')
@section('title', 'ویرایش کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش کاربر</h6>
            </div>

            <!-- فرم آپلود تصویر امضاء -->
            <form id="form-sign-image" enctype="multipart/form-data" class="dropzone">
                @csrf
                @method('PATCH')
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="sign_image">تصویر امضاء (PNG)</label>
                    <input type="file" name="sign_image" id="sign-image-dropzone">
                    @if($user->sign_image)
                        <a href="{{ $user->sign_image }}" class="btn btn-link" target="_blank">مشاهده امضاء</a>
                    @endif
                    @error('sign_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>

            <!-- فرم آپلود تصویر پروفایل -->
            <form id="form-profile" enctype="multipart/form-data" class="dropzone">
                @csrf
                @method('PATCH')
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="profile">عکس پروفایل</label>
                    <input type="file" name="profile" id="profile-dropzone">
                    @if($user->profile)
                        <a href="{{ asset('storage/' . $user->profile) }}" class="btn btn-link" target="_blank">مشاهده پروفایل</a>
                    @endif
                    @error('profile')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>

            <!-- سایر فیلدها -->
            <form id="form-details">
                @csrf
                @method('PATCH')
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
            </form>
            <button class="btn btn-primary" id="submit">ثبت فرم</button>
        </div>
    </div>

    <script>
        Dropzone.autoDiscover = false;

        // Dropzone برای تصویر امضاء
        var signImageDropzone = new Dropzone("#sign-image-dropzone", {
            url: "{{ route('users.update', $user->id) }}",
            paramName: "sign_image", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".png", // فرمت‌های مجاز
            addRemoveLinks: true,
            autoProcessQueue: false, // فایل‌ها به‌طور خودکار ارسال نمی‌شوند
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        // Dropzone برای تصویر پروفایل
        var profileDropzone = new Dropzone("#profile-dropzone", {
            url: "{{ route('users.update', $user->id) }}",
            paramName: "profile", // نام فیلد آپلود
            maxFilesize: 2, // حداکثر حجم فایل (به مگابایت)
            acceptedFiles: ".jpeg,.jpg,.png,.gif", // فرمت‌های مجاز
            addRemoveLinks: true,
            autoProcessQueue: false, // فایل‌ها به‌طور خودکار ارسال نمی‌شوند
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        document.getElementById('submit').addEventListener('click', function(e) {
            e.preventDefault();

            // جمع‌آوری داده‌های فرم سوم
            var formDetails = new FormData(document.getElementById('form-details'));

            // ارسال داده‌های فرم سوم
            fetch('{{ route("users.update", $user->id) }}', {
                method: 'POST',
                body: formDetails,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                // بعد از موفقیت‌آمیز بودن فرم سوم، فایل‌ها ارسال شوند
                signImageDropzone.processQueue(); // شروع آپلود تصویر امضاء
                profileDropzone.processQueue(); // شروع آپلود تصویر پروفایل
            }).catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
@endsection
