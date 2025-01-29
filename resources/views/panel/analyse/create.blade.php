@extends('panel.layouts.master')
@section('title', 'ایجاد آنالیز')
@section('content')
    <div class="card">
        <div class="card-title d-flex justify-content-between align-items-center m-3">
            <h6>مرحله اول انتخاب تاریخ</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('analyse.store') }}">
                @csrf
                <div class="form-group">
                    <label for="date">تاریخ شروع</label>
                    <input type="text" id="date" name="date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="to_date">تاریخ پایان</label>
                    <input type="text" id="to_date" name="to_date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="category">دسته‌بندی</label>
                    <select id="category" name="category_id" class="form-control js-example-basic-single select2-hidden-accessible" data-select2-id="1" required>
                        <option value="">انتخاب کنید</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand">برند</label>
                    <select id="brand" name="brand_id" class="form-control js-example-basic-single select2-hidden-accessible" data-select2-id="2" required>
                        <option value="">انتخاب کنید</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                @csrf
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                        <th>تعداد فروش رفته</th>
                        <th>موجودی انبار</th>
                    </tr>
                    </thead>
                    <tbody id="products-table-body">
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success" id="submit_button">ثبت آنالیز</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
        $(document).ready(function () {
            $('#category, #brand').change(function () {
                var category_id = $('#category').val();
                var brand_id = $('#brand').val();

                // Check if both category and brand are selected
                if (category_id && brand_id) {
                    $.ajax({
                        url: '{{ route('get.products') }}', // URL to your route that returns products
                        type: 'GET',
                        data: {
                            category_id: category_id,
                            brand_id: brand_id
                        },
                        success: function (data) {
                            console.log(data); // بررسی داده‌ها در کنسول
                            $('#products-table-body').empty(); // پاک کردن جدول محصولات
                            $.each(data.products, function (index, product) {
                                console.log(product); // بررسی هر محصول در کنسول
                                $('#products-table-body').append(
                                    '<tr>' +
                                    '<td>' + product.title + '</td>' +
                                    '<td><input type="number" name="products[' + product.id + '][quantity]" min="0" class="form-control" value="' + (product.quantity || 0) + '"></td>' +
                                    '<td><input type="number" name="products[' + product.id + '][sold_count]" min="0" class="form-control" value="' + (product.sold_count || 0) + '"></td>' +
                                    '<td><input type="number" name="products[' + product.id + '][storage_count]" min="0" class="form-control" value="' + (product.storage_count || 0) + '"></td>' +
                                    '</tr>'
                                );

                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(error); // نمایش خطا در کنسول برای دیباگ
                        }
                    });
                } else {
                    $('#products-table-body').empty(); // پاک کردن جدول در صورت عدم انتخاب
                }
            });
        });
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
    </script>
@endsection
