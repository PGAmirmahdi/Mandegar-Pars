@extends('panel.layouts.master')
@section('title', 'آپلود فایل')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>آپلود فایل</h6>
            </div>

            <!-- فرم آپلود فایل -->
            <form id="upload-form" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                    <label for="file">فایل خود را انتخاب کنید</label>
                    <input type="file" name="file" id="file-input" class="form-control">
                    @error('file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>

            <!-- دکمه ارسال -->
            <button class="btn btn-primary" id="upload-btn">ارسال فایل</button>

            <!-- نوار پیشرفت -->
            <div id="progress-container" class="mt-3" style="display: none;">
                <div class="progress">
                    <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
                </div>
                <p id="file-name" class="mt-2"></p>
            </div>
        </div>
    </div>

    <!-- استایل برای ظاهر نوار پیشرفت -->
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
            background-color: #28a745; /* رنگ سبز برای موفقیت */
        }
        .progress-container {
            margin-top: 10px;
        }
        .tick-mark {
            color: #28a745;
            font-size: 18px;
            display: inline-block;
            margin-left: 10px;
        }
    </style>

    <script>
        document.getElementById('upload-btn').addEventListener('click', function(e) {
            e.preventDefault();

            // گرفتن فایل از ورودی فایل
            var fileInput = document.getElementById('file-input');
            var file = fileInput.files[0];

            if (!file) {
                alert('لطفا یک فایل انتخاب کنید');
                return;
            }

            var formData = new FormData();
            formData.append('file', file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("file.upload") }}', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            // نمایش نوار پیشرفت
            var progressContainer = document.getElementById('progress-container');
            var progressBar = document.getElementById('progress-bar');
            var fileName = document.getElementById('file-name');
            progressContainer.style.display = 'block';
            fileName.innerHTML = 'آپلود فایل: ' + file.name;

            // نظارت بر پیشرفت آپلود
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                }
            });

            // موفقیت آمیز بودن آپلود
            xhr.onload = function() {
                if (xhr.status === 200) {
                    progressBar.classList.add('success');
                    progressBar.style.width = '100%';
                    fileName.innerHTML = 'آپلود موفقیت‌آمیز: ' + file.name + ' <span class="tick-mark">&#10003;</span>';
                } else {
                    alert('خطایی در آپلود رخ داد');
                }
            };

            // ارسال فرم
            xhr.send(formData);
        });
    </script>
@endsection
