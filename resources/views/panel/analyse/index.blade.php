@extends('panel.layouts.master')
@section('title', 'لیست آنالیز')
@section('content')
    <div class="card">
        <div class="card-body">
            <!-- نمایش خلاصه مجموع‌ها در بازه انتخابی -->
            <div class="card-title d-flex justify-content-between align-items-center">
                <a href="{{ route('analyse.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-2"></i>
                    ثبت آنالیز
                </a>
            </div>
            <form method="GET" action="{{ route('analyse.index') }}" id="search_form">
                <div class="row mb-3">
                    <!-- فیلترها: دسته‌بندی، برند، تاریخ از و تا -->
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 mt-2">
                        <select name="category_id" form="search_form"
                                class="js-example-basic-single select2-hidden-accessible">
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
                                        value="{{ old('brand_id') }}" {{ old('brand_id') == $productModel->id ? 'selected' : '' }}>
                                        {{ $productModel->name }}
                                    </option>
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
            @if($aggregate)
                <hr>
                <div class="col-12 row justify-content-center align-items-center mt-5 mb-0">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <!-- اضافه کردن کلاس counter و data-count -->
                                        <h2 class="counter font-weight-bold m-b-10 line-height-30 primary-font" data-count="{{ $aggregate->total_quantity ?? 0 }}">0</h2>
                                        <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">
                                            مجموع تعداد آنالیز
                                        </h6>
                                    </div>
                                    <div>
                                        <div style="width: 50px;height: 50px"
                                             class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                            <i class="fa-solid fa-dollar-sign font-size-30"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="counter font-weight-bold m-b-10 line-height-30 primary-font" data-count="{{ $aggregate->total_sold ?? 0 }}">0</h2>
                                        <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">
                                            مجموع تعداد فروش رفته
                                        </h6>
                                    </div>
                                    <div>
                                        <div style="width: 50px;height: 50px"
                                             class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                            <i class="fa-solid fa-magnifying-glass-chart font-size-30"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="counter font-weight-bold m-b-10 line-height-30 primary-font" data-count="{{ $aggregate->total_storage ?? 0 }}">0</h2>
                                        <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">
                                            مجموع تعداد انبار
                                        </h6>
                                    </div>
                                    <div>
                                        <div style="width: 50px;height: 50px"
                                             class="icon-block icon-block-sm bg-primary icon-block-floating mr-2">
                                            <i class="fa-solid fa-warehouse font-size-30"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="table-responsive">
                @foreach($analysesGrouped as $month => $monthAnalyses)
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
                                <th>ویرایش</th>
                            @endcan
                            @can('analyse-delete')
                                <th>حذف</th>
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
                                    <td>{{ verta($analyse->created_at)->format('H:i - Y/m/d') }}</td>
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
                                        <a class="btn btn-warning btn-floating {{ $isDisabled ? 'disabled' : '' }}"
                                           href="{{ $isDisabled ? '#' : route('analyse.edit', $analyse->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('analyse-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow"
                                                data-url="{{ $isDisabled ? '#' : route('analyse.destroy', $analyse->id) }}"
                                                data-id="{{ $analyse->id }}" {{ $isDisabled ? 'disabled' : '' }}>
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
    <!-- کدهای مربوط به دریافت برندها بر اساس دسته‌بندی -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // انیمیشن شمارش اعداد
            $('.counter').each(function () {
                var $this = $(this);
                var countTo = parseInt($this.attr('data-count'));
                $({ countNum: 0 }).animate({ countNum: countTo }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function () {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $this.text(this.countNum);
                    }
                });
            });

            // کدهای قبلی مربوط به دریافت برندها بر اساس دسته‌بندی
            $('select[name="category_id"]').on('change', function () {
                let categoryId = $(this).val();
                let brandSelect = $('select[name="brand_id"]');

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
