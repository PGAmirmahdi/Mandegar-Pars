@extends('panel.layouts.master')
@section('title', 'ویرایش کاربر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش کاربر</h6>
            </div>

            <!-- فرم آپلود تصویر امضاء -->
            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                <label for="sign_image">تصویر امضاء (PNG)</label>
                <input type="file" name="sign_image" id="sign-image-input" class="form-control">
                @if($user->sign_image)
                    <a href="{{ $user->sign_image }}" class="btn btn-link" target="_blank">مشاهده امضاء</a>
                @endif
                @error('sign_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <!-- نوار پیشرفت -->
                <div id="sign-image-progress-container" class="progress mt-2" style="display: none;">
                    <div id="sign-image-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
                </div>
                <p id="sign-image-file-name" class="mt-2"></p>
            </div>

            <!-- فرم آپلود تصویر پروفایل -->
            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                <label for="profile">عکس پروفایل</label>
                <input type="file" name="profile" id="profile-input" class="form-control">
                @if($user->profile)
                    <a href="{{ asset('storage/' . $user->profile) }}" class="btn btn-link" target="_blank">مشاهده پروفایل</a>
                @endif
                @error('profile')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <!-- نوار پیشرفت -->
                <div id="profile-progress-container" class="progress mt-2" style="display: none;">
                    <div id="profile-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
                </div>
                <p id="profile-file-name" class="mt-2"></p>
            </div>

            <!-- سایر فیلدها -->
            <form id="form-details">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <!-- سایر فیلدها -->
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
                </div>
            </form>
            <button class="btn btn-primary" id="submit">ثبت فرم</button>
        </div>
    </div>

    <style>
        .progress {
            height: 25px;
            background-color: #f5f5f5;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background-color: #007bff;
            transition: width 0.4s;
        }
        .progress-bar.success {
            background-color: #28a745;
        }
    </style>

    <script>
        document.getElementById('submit').addEventListener('click', function(e) {
            e.preventDefault();

            var formDetails = new FormData(document.getElementById('form-details'));

            fetch('{{ route("users.update", $user->id) }}', {
                method: 'POST',
                body: formDetails,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                if (data.success) {
                    uploadFile('sign-image-input', 'sign-image-progress-container', 'sign-image-progress-bar', 'sign-image-file-name', data.sign_image_upload_route);
                    uploadFile('profile-input', 'profile-progress-container', 'profile-progress-bar', 'profile-file-name', data.profile_upload_route);
                } else {
                    alert(data.message || 'خطایی در ثبت اطلاعات رخ داد');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        });

        function uploadFile(inputId, progressContainerId, progressBarId, fileNameId, uploadRoute) {
            var fileInput = document.getElementById(inputId);
            var file = fileInput.files[0];
            if (!file) return;

            var formData = new FormData();
            formData.append(inputId.split('-')[0], file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', uploadRoute, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            var progressContainer = document.getElementById(progressContainerId);
            var progressBar = document.getElementById(progressBarId);
            var fileName = document.getElementById(fileNameId);
            progressContainer.style.display = 'block';
            fileName.innerHTML = 'آپلود فایل: ' + file.name;

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                }
            });

            xhr.onload = function() {
                if (xhr.status === 200) {
                    progressBar.classList.add('success');
                    progressBar.style.width = '100%';
                    fileName.innerHTML = 'آپلود موفقیت‌آمیز: ' + file.name + ' <i class="fas fa-check"></i>';
                } else {
                    alert('خطایی در آپلود فایل رخ داد.');
                }
            };

            xhr.send(formData);
        }
    </script>
@endsection
