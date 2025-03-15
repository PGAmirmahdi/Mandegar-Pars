@extends('panel.layouts.master')
@section('title', 'ثبت ورودی')
@php
    $inventory_id = $inventory_id ?? null;
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت ورودی</h6>
                <button class="btn btn-outline-success" type="button" id="btn_add">
                    <i class="fa fa-plus mr-2"></i> افزودن کالا
                </button>
            </div>
            <!-- تمامی فیلدها داخل فرم قرار دارند -->
            <form action="{{ route('inventory-reports.store') }}" method="post" class="dropzone border-bottom" id="my-dropzone" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <input type="hidden" name="type" value="{{ request()->type }}">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="person"> تحویل دهنده <span class="text-danger">*</span></label>
                            <input type="text" name="person" class="form-control" id="person" value="{{ old('person') }}">
                            @error('person')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="input_date"> تاریخ ورود <span class="text-danger">*</span></label>
                            <input type="text" name="input_date" class="form-control date-picker-shamsi-list" id="input_date" value="{{ old('input_date') ?? verta()->format('Y/m/d') }}">
                            @error('input_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- فیلد آپلود فایل به همراه کانتینر پیش‌نمایش -->
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <input type="hidden" name="file" id="file" required>
                            <!-- این div محل نمایش پیش‌نمایش فایل است -->
                            <div id="file-preview"></div>
                            @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-xl-9 col-lg-9 col-md-12 col-sm-12">
                        <div class="table-responsive overflow-auto">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                <tr>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($errors->any())
                                    @foreach(old('inventory_id', []) as $key => $inventory_id)
                                        <tr>
                                            <td>
                                                <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                                    @foreach($inventories as $item)
                                                        <option value="{{ $item->id }}" {{ $inventory_id == $item->id ? 'selected' : '' }}>
                                                            {{ $item->product->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="counts[]" class="form-control" min="1" value="{{ old('counts')[$key] }}" required>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-floating btn_remove" type="button">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                                @foreach($inventories as $item)
                                                    <option value="{{ $item->id }}" {{ in_array($item->id, old('inventory_id', [])) ? 'selected' : '' }}>
                                                        {{ $item->product->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="counts[]" class="form-control" min="1" value="1" required>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-floating btn_remove" type="button">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
                <label class="col-12 my-3 pt-2 border-top" for="file"> حواله ورود: <span class="text-danger">*</span></label>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- اضافه کردن کتابخانه‌های Dropzone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <!-- استایل برای حذف خط دور فرم Dropzone -->
    <style>
        .dropzone {
            border: none;
        }
    </style>
    <script>
        // تنظیمات Dropzone برای فرم با آیدی my-dropzone
        Dropzone.options.myDropzone = {
            paramName: "file", // نام فیلد فایل در سرور
            autoProcessQueue: false, // جلوگیری از آپلود خودکار
            dictDefaultMessage: "فایل خود را بکشید و رها کنید", // تغییر پیام پیش‌فرض
            maxFilesize: 2, // حداکثر اندازه فایل به مگابایت
            acceptedFiles: "image/*,application/pdf", // فایل‌های مجاز
            init: function () {
                var myDropzone = this;
                // رویداد کلیک روی دکمه "ثبت فرم"
                // در این مثال دکمه ارسال فرم داخل فرم قرار دارد و از نوع submit است.
                document.getElementById("my-dropzone").addEventListener("submit", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // پردازش صف فایل‌های آپلودی
                    myDropzone.processQueue();
                });
                // پس از اتمام آپلود همه فایل‌ها، هدایت به صفحه index
                this.on("queuecomplete", function () {
                    window.location.href = "{{ route('inventory-reports.index') }}";
                });
                this.on("success", function(file, response) {
                    console.log("فایل با موفقیت آپلود شد", response);
                });
                this.on("error", function(file, response) {
                    console.log("خطا در آپلود", response);
                });
            }
        };

        var inventory = [];
        var options_html = '';

        @foreach(\App\Models\Inventory::with('product')->where('warehouse_id', $warehouse_id)->get() as $item)
        inventory.push({
            "id": "{{ $item->id }}",
            "title": "{{ $item->product->title }}",
        });
        @endforeach

        $.each(inventory, function (i, item) {
            options_html += `<option value="${item.id}">${item.title}</option>`;
        });

        $(document).ready(function () {
            // اضافه کردن ردیف جدید برای انتخاب کالا
            $('#btn_add').on('click', function () {
                $('#properties_table tbody').append(`
                    <tr>
                        <td>
                            <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                ${options_html}
                            </select>
                        </td>
                        <td>
                            <input type="number" name="counts[]" class="form-control" min="1" value="1" required>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-floating btn_remove" type="button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
                $('.js-example-basic-single').select2();
            });
            // حذف ردیف انتخاب کالا
            $(document).on('click', '.btn_remove', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
