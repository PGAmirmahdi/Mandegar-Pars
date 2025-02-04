@extends('panel.layouts.master')
@section('title', 'لیست آنالیز')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست آنالیز</h6>
                <a href="{{ route('analyse.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-2"></i>
                    ثبت آنالیز
                </a>
            </div>
            <form method="GET" action="{{ route('analyse.index') }}" id="search_form">
                <div class="row mb-3">
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                        <select name="category_id" form="search_form"
                                class="js-example-basic-single select2-hidden-accessible"
                                data-select2-id="1">
                            <option value="all">شرح کالا (همه)</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                        <select class="form-control" name="brand_id" id="brand">
                            <option value="">برند (همه)</option>
                            @if(old('brand_id'))
                                @foreach(\App\Models\ProductModel::where('category_id', old('category'))->get() as $productModel)
                                    <option
                                        value="{{ old('brand_id') }}" {{ old('brand_id') == $productModel->id ? 'selected' : '' }}>{{ $productModel->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                        <input type="text" id="start_date" name="start_date"
                               class="form-control date-picker-shamsi-list"
                               autocomplete="off" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}"
                               form="search_form">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                        <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list"
                               autocomplete="off" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}"
                               form="search_form">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                @foreach($analyses as $month => $monthAnalyses)
                    <h5 class="mt-4">{{ $monthNames[$month] }}</h5> <!-- نمایش نام ماه به فارسی -->
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>دسته‌بندی</th>
                            <th>برند</th>
                            <th>از تاریخ</th>
                            <th>تا تاریخ</th>
                            @can('admin')
                                <th>تاریخ ایجاد</th>
                            @endcan
                            <th>محصولات</th>
                            @can('analyse-edit')
                                <th>
                                    ویرایش
                                </th>
                            @endcan
                            @can('analyse-delete')
                                <th>
                                    حذف
                                </th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($monthAnalyses as $key => $analyse)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $analyse->category->name }}</td>
                                <td>{{ $analyse->brand->name }}</td>
                                <td>{{ \Verta::parse($analyse->date)->format('%d %B %Y') }}</td>
                                <td>{{ \Verta::parse($analyse->to_date)->format('%d %B %Y') }}</td>
                                @can('admin')
                                    <td>{{verta($analyse->created_at)->format('H:i - Y/m/d')}}</td>
                                @endcan
                                <td>
                                    <a href="{{ route('analyse.show', $analyse->id) }}"
                                       class="btn btn-lg btn-outline-behance btn-floating">
                                        <i class="fa fa-chart-simple"></i>
                                    </a>
                                </td>
                                @php
                                    $isDisabled = $analyse->created_at->addMonth() < \Carbon\Carbon::now();
                                @endphp
                                @can('analyse-edit')
                                    <td>
                                        <a
                                            class="btn btn-warning btn-floating {{ $isDisabled ? 'disabled' : '' }}"
                                            href="{{ $isDisabled ? '#' : route('analyse.edit', $analyse->id) }}"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('analyse-delete')
                                    <td>
                                        <button
                                            class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ $isDisabled ? '#' : route('analyse.destroy', $analyse->id) }}"
                                            data-id="{{ $analyse->id }}"
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('select[name="category_id"]').on('change', function () {
                let categoryId = $(this).val();
                let brandSelect = $('select[name="brand_id"]'); // تغییر از 'model' به 'brand'

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
    </script>
@endsection
