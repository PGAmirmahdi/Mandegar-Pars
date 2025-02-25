@extends('panel.layouts.master')
@section('title', 'لیست قیمت محصولات ماندگار پارس')
@section('styles')
    <style>
        #price_table td:hover {
            background-color: #e3daff !important;
        }

        #price_table .item {
            text-align: center;
            background: transparent;
            border: 0;
        }

        #price_table .item:focus {
            border-bottom: 2px solid #5d4a9c;
        }

        #btn_save {
            width: 100%;
            justify-content: center;
            border-radius: 0;
            padding: .8rem;
            font-size: larger;
        }

        #price_table {
            box-shadow: 0 5px 5px 0 lightgray;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .drag-handle{
            cursor: move;
        }
        th, td {
            padding: 8px 16px !important;
        }

        .drag-handle i {
            font-size: 16px;
        }
    </style>
@endsection

@section('content')
    <!-- استفاده از container-fluid برای تمام صفحه -->
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mb-4">لیست قیمت محصولات ماندگار پارس (ریال)</h3>

                <!-- مدال انتخاب محصول -->
                <div class="modal fade" id="productModal" tabindex="-1" role="dialog"
                     aria-labelledby="productModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel">انتخاب محصول</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <select name="product_id" id="select_product" class="js-example-basic-single">
                                    <option value="all">نام کالا</option>
                                    @foreach(\App\Models\Product::all(['id', 'title']) as $product)
                                        <option
                                            value="{{ $product->id }}" {{ request()->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول محصولات در یک row و col-12 -->
                <div class="row">
                    <div class="col-12">
                        <div class="tableFixHead table-responsive overflow-auto">
                            <table class="table-bordered table dtr-inline text-center" id="price_table">
                                <thead>
                                <tr>
                                    <th colspan="{{ in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']) ? 4 : 3 }}">
                                        @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
                                            <button class="btn btn-primary btn-floating" id="btn_open_modal">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endif
                                        <button onclick="downloadPDF()" class="btn btn-youtube btn-floating">
                                            <i class="fa fa-file-pdf"></i>
                                        </button>
                                    </th>
                                    <th colspan="3">
                                        <form action="{{ route('MandegarPrice.search') }}" method="get"
                                              id="search_form">
                                            <div class="row">
                                                <div class="col-xl-3 col-lg-2 col-md-3 col-sm-12">
                                                    <select class="form-control" name="category" id="category">
                                                        <option value="">شرح کالا (همه)</option>
                                                        @foreach(\App\Models\Category::all() as $category)
                                                            <option
                                                                value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-xl-3 col-lg-2 col-md-3 col-sm-12">
                                                    <select class="form-control" name="brand" id="brand">
                                                        <option value="">برند (همه)</option>
                                                        @if(request()->category)
                                                            @foreach(App\Models\ProductModel::where('category_id', request()->category)->get() as $productModel)
                                                                <option
                                                                    value="{{ $productModel->id }}" {{ request()->brand == $productModel->id ? 'selected' : '' }}>
                                                                    {{ $productModel->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('brand')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-xl-3 col-lg-2 col-md-3 col-sm-12">
                                                    <select name="product" form="search_form"
                                                            class="js-example-basic-single">
                                                        <option value="all">مدل کالا (همه)</option>
                                                        @foreach(\App\Models\Product::all(['id', 'title', 'status'])->where('status', 'approved') as $product)
                                                            <option
                                                                value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                                                {{ $product->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <button class="btn btn-primary" type="submit">
                                                        جست و جو
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </th>
                                </tr>
                                <tr>
                                    @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
                                        <th>جابه‌جایی</th>
                                    @endif
                                    <th>#</th>
                                    <th>دسته بندی</th>
                                    <th>برند</th>
                                    <th>عنوان محصول</th>
                                    <th>قیمت</th>
                                    @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
                                        <th>عملیات</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($mandegar_price as $key => $product)
                                    <tr data-price-id="{{ $product->product_id }}">
                                        @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
                                            <td class="drag-handle"><i class="fa fa-arrows-alt"></i></td>
                                        @endif
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $product->product->category->name }}</td>
                                        <td>{{ $product->product->productModels->name }}</td>
                                        <td>{{ $product->product->title }}</td>
                                        <td>
                                            <input type="text" class="item price-input"
                                                   data-id="{{ $product->product_id }}"
                                                   data-field="price" value="{{ number_format($product->price) }}"
                                                   @if(!in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager'])) disabled @endif>
                                        </td>
                                        @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
                                            <td>
                                                <button class="btn btn-danger btn-floating btn-delete-row"
                                                        data-id="{{ $product->product_id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- صفحه‌بندی و نمایش اطلاعات -->
                <div class="row">
                    <div class="col-sm-12 col-md-6 text-left">
                        <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">
                            نمایش {{ $mandegar_price->firstItem() }} تا {{ $mandegar_price->lastItem() }}
                            از {{ $mandegar_price->total() }} رکورد
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                            {{ $mandegar_price->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/sweetalert2@11"></script>
    <script>
        function downloadPDF() {
            window.open("{{ route('downloadPDF') }}", "_blank");
        }
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
        @if(in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager']))
        document.addEventListener("DOMContentLoaded", function () {
            const tableBody = document.querySelector("#price_table tbody");

            new Sortable(tableBody, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function () {
                    updateOrder();
                }
            });

            function updateOrder() {
                let rows = document.querySelectorAll("#price_table tbody tr");
                let orderData = Array.from(rows).map((row, index) => ({
                    id: row.dataset.priceId,
                    order: index + 1
                }));

                fetch("{{ route('updateOrder') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({items: orderData})
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: data.status === "success" ? 'ترتیب با موفقیت ذخیره شد' : 'خطا در ذخیره ترتیب',
                            icon: data.status === "success" ? 'success' : 'error',
                            showConfirmButton: data.status !== "success",
                            toast: data.status === "success",
                            timer: 1500,
                            position: 'top-start'
                        });
                    })
                    .catch(error => console.error('خطا:', error));
            }
        });

        $(document).ready(function () {
            $('#btn_open_modal').on('click', function () {
                $('#productModal').modal('show');
            });

            $('#select_product').on('change', function () {
                var prodId = $(this).val();
                if (!prodId || prodId === 'all') return;
                var prodTitle = $('#select_product option:selected').text();

                if ($('#price_table tbody tr[data-price-id="' + prodId + '"]').length) {
                    Swal.fire({
                        title: 'این محصول قبلاً اضافه شده است!',
                        icon: 'warning',
                        timer: 2000,
                        toast: true,
                        position: 'top-start',
                        showConfirmButton: false,
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('getProductDetails', ['id' => '%%ID%%']) }}".replace('%%ID%%', prodId),
                    type: 'get',
                    success: function (res) {
                        if (res.status === 'success') {
                            var newRow = `<tr data-price-id="${prodId}">
                                    <td class="drag-handle"><i class="fa fa-arrows-alt"></i></td>
                                    <td>${res.data.index || '--'}</td>
                                    <td>${res.data.category || '--'}</td>
                                    <td>${res.data.brand || '--'}</td>
                                    <td>${prodTitle}</td>
                                    <td>
                                        <input type="text" class="item price-input" data-id="${prodId}" data-field="price" value="0">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-floating btn-delete-row" data-id="${prodId}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                            $('#price_table tbody').append(newRow);
                            $('#select_product').val(null).trigger('change');
                            $('#productModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        console.error('خطا در دریافت اطلاعات:', xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.btn-delete-row', function () {
                var row = $(this).closest('tr');
                var prodId = $(this).data('id');
                $.ajax({
                    url: "{{ route('MandegarPriceDelete') }}",
                    type: 'post',
                    data: {
                        product_id: prodId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            row.remove();
                            Swal.fire({
                                title: 'محصول با موفقیت حذف شد',
                                icon: 'success',
                                showConfirmButton: false,
                                toast: true,
                                timer: 1500,
                                position: 'top-start'
                            });
                        }
                    }
                });
            });

            $(document).on('blur', '.price-input', function () {
                var input = $(this);
                var prodId = input.data('id');
                var field = input.data('field');
                var price = input.val().replace(/,/g, '');
                $.ajax({
                    url: "{{ route('MandegarPriceUpdate') }}",
                    type: 'post',
                    data: {
                        items: JSON.stringify([{id: prodId, field: field, price: price}]),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        Swal.fire({
                            title: 'با موفقیت به روز شد',
                            icon: 'success',
                            showConfirmButton: false,
                            toast: true,
                            timer: 1500,
                            position: 'top-start'
                        });
                    }
                });
            });

            $(document).on('keyup', '.price-input', function () {
                $(this).val(addCommas($(this).val()));
            });

            function addCommas(nStr) {
                nStr = nStr.replace(/,/g, '');
                let x = nStr.split('.');
                let x1 = x[0];
                let x2 = x.length > 1 ? '.' + x[1] : '';
                let rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            }
        });
        @endif
    </script>
@endsection
