@extends('panel.layouts.master')
@section('title', 'لیست کالاها')
@section('content')
    <style>
        .table-color {
            border:2px dashed #bdad7a !important;
            color: #856404;
            box-shadow: 0px 0px 50px 0px inset #c5ac61;
        }
        .highlight {
            background-color: #ffeb3b !important; /* هایلایت با اولویت بالا */
        }

    </style>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست کالاها</h6>
                <div>
                    <form action="{{ route('products.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('products-create')
                        <a href="{{ route('products.request') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            @if(auth()->user()->isAdmin()|| auth()->user()->isOfficeManager())
                                لیست درخواست ثبت کالا
                            @else
                                لیست درخواست ثبت کالاهای من
                            @endif
                        </a>
                    @endcan
                </div>

            </div>
            <form action="{{ route('products.search') }}" method="get" id="search_form">
                <div class="row mb-3">
                    <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                        <label for="code">کد کالا</label>
                        <input type="text" name="code" class="form-control" placeholder="کد کالا"
                               value="{{ request()->code ?? null }}" form="search_form">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="category">شرح کالا</label>
                        <select class="form-control" name="category" id="category">
                            <option value="">انتخاب کنید</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option
                                    value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="brand">برند</label>
                        <select class="form-control" name="brand" id="brand">
                            <option value="">انتخاب کنید</option>
                            @if(old('brand'))
                                <option value="{{ old('brand') }}" selected>{{ old('brand_name') }}</option>
                            @endif
                        </select>
                        @error('model')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <label for="product">مدل کالا</label>
                        <select name="product" form="search_form"
                                class="js-example-basic-single select2-hidden-accessible"
                                data-select2-id="3">
                            <option value="all">مدل کالا (همه)</option>
                            @foreach(\App\Models\Product::all(['id','title','status'])->where('status','=' , 'approved') as $product)
                                <option
                                    value="{{ $product->id }}" {{ old('product') == $product->id ? 'selected' : '' }}>
                                    {{ $product->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{--                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">--}}
                    {{--                    <select name="payment_type" form="search_form"--}}
                    {{--                            class="js-example-basic-single select2-hidden-accessible" data-select2-id="6">--}}
                    {{--                        <option value="all">وضعیت (همه)</option>--}}
                    {{--                        @foreach(App\Models\Product::STATUS as $key => $value)--}}
                    {{--                            <option--}}
                    {{--                                value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>{{ $value }}</option>--}}
                    {{--                        @endforeach--}}
                    {{--                    </select>--}}
                    {{--                </div>--}}
                    <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                        <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                    </div>

                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کد کالا</th>
                        <th>شرح کالا</th>
                        <th>برند</th>
                        <th>مدل</th>
                        @canany(['admin','OfficeManager'])
                            <th>وضعیت</th>
                        @endcanany
                        @canany(['admin','accountant','OfficeManager'])
                            <th>موجودی</th>
                        @endcanany
                        @can('admin')
                            <th>تاریخ ایجاد</th>
                        @endcan
                        @can('products-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('products-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        <tr id="row-{{ $product->id }}" @if($product->latestInventory() < 10) @canany(['admin','accountant','OfficeManager']) class="table-color" @endcanany @endif>
                            <td>{{ ++$key }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $product->code }}</td>
                            <td>{{ $product->category->name ?? 'شرح نامشخص' }}</td>
                            <td>{{ $product->productModels->slug ?? 'برند نامشخص' }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $product->title }}</td>
                            @canany(['admin','OfficeManager'])
                                <td>
                                    @if($product->status == 'approved')
                                        <span class="badge badge-success">تایید شده</span>
                                    @elseif($product->status == 'pending')
                                        <span class="badge badge-warning">منتظر تایید</span>
                                    @elseif($product->status == 'rejected')
                                        <span class="badge badge-danger">رد شده</span>
                                    @else
                                        <span class="badge badge-info">نامشخص</span>
                                    @endif
                                </td>
                            @endcanany

                            @canany(['admin','accountant','OfficeManager'])
                                <td>{{ $product->latestInventory() }}</td>
                            @endcanany
                            @can('admin')
                                <td>{{ verta($product->created_at)->format('H:i - Y/m/d') }}</td>
                            @endcan
                            @can('products-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating"
                                       href="{{ route('products.edit', $product->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('products-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('products.destroy',$product->id) }}"
                                            data-id="{{ $product->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $products->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('select[name="category"]').on('change', function () {
                let categoryId = $(this).val();
                let brandSelect = $('select[name="brand"]'); // تغییر از 'model' به 'brand'

                // پاک کردن گزینه‌های قبلی
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
                            // اضافه کردن گزینه‌های جدید به لیست برندها
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
        document.addEventListener("DOMContentLoaded", function () {
            const highlightedProductId = "{{ $highlightedProductId ?? '' }}";
            const category = "{{ request()->category ?? '' }}"; // خواندن دسته‌بندی از URL

            if (highlightedProductId) {
                const row = document.getElementById(`row-${highlightedProductId}`);
                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // اضافه کردن کلاس table-color برای هایلایت
                    row.classList.add('table-warning');

                    // حذف کلاس بعد از 2 ثانیه
                    setTimeout(() => {
                        row.classList.remove('table-warning');
                    }, 2000);
                }
            }
        });


    </script>
@endsection
