@extends('panel.layouts.master')
@section('title', 'موجودی ماهانه انبار')
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('inventorysnapshot.search') }}" method="get" id="search_form">
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mb-3">
                    <select class="form-control" name="category" id="category" form="search_form">
                        <option value="">شرح کالا(همه)</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option
                                value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mb-3">
                    <select class="form-control" name="brand" id="brand" form="search_form">
                        <option value="">برند(همه)</option>
                        @if(request()->category)
                            @foreach(App\Models\ProductModel::where('category_id', request()->category)->get() as $productModel)
                                <option
                                    value="{{ $productModel->id }}" {{ request()->brand == $productModel->id ? 'selected' : '' }}>{{ $productModel->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="3">
                        <option value="all">مدل کالا (همه)</option>
                        @foreach(\App\Models\Product::all(['id','title']) as $product)
                            <option
                                value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="warehouse" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="4">
                        <option value="all">نام انبار (همه)</option>
                        @foreach(\App\Models\Warehouse::all(['id','name']) as $warehouse)
                            <option
                                value="{{ $warehouse->id }}" {{ request()->warehouse == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <input type="text" id="start_date" name="start_date"
                           class="form-control date-picker-shamsi-list"
                           autocomplete="off" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}"
                           form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list"
                           autocomplete="off" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}"
                           form="search_form">
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <hr>
            <div class="col-12 row justify-content-center align-items-center mt-5 mb-0">
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="counter font-weight-bold m-b-10 line-height-30 primary-font"
                                        data-count="{{ $total_count }}">{{ $total_count }}</h2>
                                    <h6 class="font-size-13 mb-2 text-primary font-weight-bold primary-font">
                                        مجموع تعداد کالای در انبار
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
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title m-b-20">نمودار موجودی انبارها</h6>
                            <canvas id="pie_chart_inventory" style="width: auto"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @foreach($snap_shots_grouped as $month => $monthSnapShot)
                <h5 class="mt-4">{{ $monthNames[$month] }}</h5>
                <div class="table-responsive overflow-auto">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>دسته بندی کالا</th>
                            <th>برند کالا</th>
                            <th>نام کالا</th>
                            <th>تعداد در انبار</th>
                            <th>نام انبار</th>
                            <th>تاریخ ثبت و ذخیره</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($snap_shots as $key => $item)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $item->product->category->name }}</td>
                                <td>{{ $item->product->productModels->name }}</td>
                                <td>{{ $item->product->title }}</td>
                                <td>{{ $item->stock_count }}</td>
                                <td>{{ $item->warehouse->name }}</td>
                                <td>{{verta($item->created_at)->format('Y/m/d H:i')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach
            <div class="d-flex justify-content-center">{{ $snap_shots->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // انیمیشن شمارش اعداد
            $('.counter').each(function () {
                var $this = $(this);
                var countTo = parseInt($this.attr('data-count'));
                $({ countNum: 0 }).animate({ countNum: countTo }, {
                    duration: 1000,
                    easing: 'swing',
                    step: function () {
                        $this.text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $this.text(this.countNum);
                    }
                });
            });
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
        $(document).ready(function () {
            $('.btn_move').on('click', function () {
                var inventory_id = $(this).data('id');
                $('#inventory_id').val(inventory_id)
            })
        })
        if ($('#pie_chart_inventory').length) {
            var inventory_labels = @json($chartData->pluck('warehouse_name'));
            var inventory_data = @json($chartData->pluck('total_inventory'));


            var elementInventory = document.getElementById("pie_chart_inventory");
            elementInventory.height = 146;

            new Chart(elementInventory, {
                type: 'pie',
                data: {
                    labels: inventory_labels,
                    datasets: [
                        {
                            label: "تعداد موجودی",
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF',
                                '#FF9F40'
                            ],
                            data: inventory_data,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'right',
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return value.toLocaleString('fa-IR') + ' عدد';
                            }
                        }
                    }
                },
            });
        }
    </script>
@endsection
