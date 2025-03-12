@extends('panel.layouts.master')
@section('title', 'ایجاد آنالیز')
@section('content')
    <div class="card">
        <div class="card-title d-flex justify-content-between align-items-center m-3">
            <h6>مرحله اول انتخاب تاریخ و محصولات</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('analyse.store') }}">
                @csrf
                <div class="form-row mb-5">
                    <!-- تاریخ شروع -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="date">تاریخ شروع</label>
                            <input type="text" id="date" name="date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                        </div>
                    </div>
                    <!-- تاریخ پایان -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="to_date">تاریخ پایان</label>
                            <input type="text" id="to_date" name="to_date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                        </div>
                    </div>
                    <!-- دسته‌بندی -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="category">دسته‌بندی</label>
                            <select id="category" name="category_id" class="form-control js-example-basic-single" required>
                                <option value="">انتخاب کنید</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- برند -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="brand">برند</label>
                            <select class="form-control" name="brand_id" id="brand" required>
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>
                    </div>
                    <!-- جستجوی محصول -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="product_search">جستجوی محصول</label>
                            <input type="text" id="product_search" class="form-control" placeholder="نام محصول">
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                        <th>فروش رفته</th>
                        <th>موجودی انبار</th>
                    </tr>
                    </thead>
                    <tbody id="products-table-body">
                    <!-- محصولات از طریق Ajax یکبار رندر می‌شوند -->
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success" id="submit_button">ثبت آنالیز</button>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // متغیر برای ذخیره محصولات دریافت‌شده
        var productsData = [];

        // تابعی برای رندر کردن ردیف‌های جدول
        function renderProducts() {
            var tbody = $('#products-table-body');
            tbody.empty();
            $.each(productsData, function(index, product) {
                var row = '<tr data-product-id="'+ product.id +'">' +
                    '<td class="product-title">' + product.title + '</td>' +
                    '<td><input type="number" name="products['+ product.id +'][quantity]" class="form-control" value="'+ (product.quantity || 0) +'"></td>' +
                    '<td><input type="number" name="products['+ product.id +'][sold_count]" class="form-control" value="'+ (product.sold_count || 0) +'"></td>' +
                    '<td><input type="number" name="products['+ product.id +'][storage_count]" class="form-control" value="'+ (product.storage_count || 0) +'"></td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        $(document).ready(function () {
            // دریافت برندها بر اساس دسته‌بندی انتخاب شده
            $('select[name="category_id"]').on('change', function () {
                var categoryId = $(this).val();
                var brandSelect = $('#brand');
                brandSelect.empty();
                brandSelect.append('<option value="">انتخاب کنید</option>');
                if (categoryId) {
                    $.ajax({
                        url: '{{ route("get.models.by.category") }}',
                        type: 'POST',
                        data: {
                            category_id: categoryId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (data) {
                            $.each(data, function (key, value) {
                                brandSelect.append(`<option value="${value.id}">${value.name}</option>`);
                            });
                        },
                        error: function () {
                            alert('مشکلی در دریافت برندها رخ داده است.');
                        }
                    });
                }
            });

            // دریافت محصولات زمانی که برند انتخاب شد (یکبار)
            $('#brand').on('change', function () {
                fetchProducts();
            });

            // تابع دریافت محصولات از سرور
            function fetchProducts() {
                var category_id = $('#category').val();
                var brand_id = $('#brand').val();
                if (category_id && brand_id) {
                    $.ajax({
                        url: '{{ route("get.products") }}',
                        type: 'GET',
                        data: {
                            category_id: category_id,
                            brand_id: brand_id
                        },
                        success: function (data) {
                            productsData = data.products; // ذخیره محصولات در متغیر
                            renderProducts(); // رندر جدول محصولات
                        },
                        error: function () {
                            alert('مشکلی در دریافت محصولات رخ داده است.');
                        }
                    });
                } else {
                    $('#products-table-body').empty();
                }
            }

            // فیلتر کردن سطرهای جدول با استفاده از hide/show به جای رندر مجدد
            $('#product_search').on('keyup', function () {
                var searchTerm = $(this).val().toLowerCase();
                $('#products-table-body tr').each(function () {
                    var title = $(this).find('.product-title').text().toLowerCase();
                    if (title.indexOf(searchTerm) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endsection
