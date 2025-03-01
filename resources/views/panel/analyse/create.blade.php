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
                <div class="form-row mb-5">
                    <!-- تاریخ شروع -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="date">تاریخ شروع</label>
                            <input type="text" id="date" name="date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                            @error('date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- تاریخ پایان -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="to_date">تاریخ پایان</label>
                            <input type="text" id="to_date" name="to_date" class="form-control date-picker-shamsi-list" autocomplete="off" required>
                            @error('to_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                            <label for="brand">برند<span class="text-danger">*</span></label>
                            <select class="form-control" name="brand_id" id="brand">
                                <option value="">انتخاب کنید</option>
                                @if(old('brand_id'))
                                    @foreach(\App\Models\ProductModel::where('category_id', old('category'))->get() as $productModel)
                                        <option value="{{ $productModel->id }}" {{ old('brand_id') == $productModel->id ? 'selected' : '' }}>{{ $productModel->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('brand_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- فیلد جستجوی محصول -->
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
                        <th>تعداد فروش رفته</th>
                        <th>موجودی انبار</th>
                    </tr>
                    </thead>
                    <tbody id="products-table-body">
                    <!-- محصولات از طریق Ajax در اینجا قرار می‌گیرند -->
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success" id="submit_button">ثبت آنالیز</button>
            </form>
        </div>
    </div>

    <!-- افزودن jQuery (در صورت عدم وجود) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // تابع ارسال فرم
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);
                button.prop('disabled', true).text('در حال ارسال...');
                button.closest('form').submit();
            });
        });

        // تابع دریافت محصولات با فیلتر دسته‌بندی، برند و جستجو
        $(document).ready(function () {
            function fetchProducts() {
                var category_id = $('#category').val();
                var brand_id = $('#brand').val();
                var searchTerm = $('#product_search').val();

                // در صورتی که دسته‌بندی و برند انتخاب شده باشند
                if (category_id && brand_id) {
                    $.ajax({
                        url: '{{ route('get.products') }}', // مسیر مربوط به دریافت محصولات
                        type: 'GET',
                        data: {
                            category_id: category_id,
                            brand_id: brand_id,
                            search: searchTerm // پارامتر جستجو
                        },
                        success: function (data) {
                            console.log(data);
                            $('#products-table-body').empty();
                            $.each(data.products, function (index, product) {
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
                            console.error(error);
                        }
                    });
                } else {
                    $('#products-table-body').empty();
                }
            }

            // فراخوانی تابع fetchProducts در تغییر دسته‌بندی، برند یا جستجو
            $('#category, #brand').change(fetchProducts);
            $('#product_search').on('input', fetchProducts);
        });

        // دریافت برندها بر اساس دسته‌بندی انتخابی
        $(document).ready(function () {
            $('select[name="category_id"]').on('change', function () {
                let categoryId = $(this).val();
                let brandSelect = $('select[name="brand_id"]');
                brandSelect.empty();
                if (categoryId) {
                    $.ajax({
                        url: '{{ route('get.models.by.category') }}',
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
                            alert('مشکلی در دریافت اطلاعات رخ داده است.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
