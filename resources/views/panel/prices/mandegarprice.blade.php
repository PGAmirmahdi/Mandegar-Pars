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

        .tableFixHead {
            overflow: auto !important;
            height: 800px !important;
        }

        .tableFixHead thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 1 !important;
        }

        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        th, td {
            padding: 8px 16px !important;
        }

        .tableFixHead thead th {
            background: #fff !important;
            border: 1px solid #dee2e6 !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت محصولات ماندگار پارس (ریال)</h3>

            <!-- Modal انتخاب محصول -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
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
                                @foreach(\App\Models\Product::all(['id','title']) as $product)
                                    <option value="{{ $product->id }}"
                                        {{ request()->product_id == $product->id ? 'selected' : '' }}>
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

            <!-- جدول محصولات -->
            <div class="table-responsive tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th colspan="12">
                            @if(in_array(auth()->user()->role->name,['admin','ceo','office-manager']))
                                <button class="btn btn-primary btn-floating" id="btn_open_modal">
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif
                            <button onclick="downloadPDF()" class="btn btn-youtube btn-floating">
                                <i class="fa fa-file-pdf"></i>
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <th>دسته بندی</th>
                        <th>برند</th>
                        <th>عنوان محصول</th>
                        <th>قیمت</th>
                        @if(in_array(auth()->user()->role->name,['admin','ceo','office-manager']))
                            <th>عملیات</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mandegar_price as $product)
                        <tr data-price-id="{{ $product->product_id }}">
                            <td>{{ $product->product->category->name }}</td>
                            <td>{{ $product->product->productModels->name }}</td>
                            <td>{{ $product->product->title }}</td>
                            <td>
                                <input type="text" class="item price-input" data-id="{{ $product->product_id }}"
                                       data-field="price" value="{{ number_format($product->price) }}"
                                       @if(!in_array(auth()->user()->role->name, ['admin', 'ceo', 'office-manager'])) disabled @endif>
                            </td>
                            @if(in_array(auth()->user()->role->name,['admin','ceo','office-manager']))
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
@endsection

@section('scripts')
    <script src="/assets/js/sweetalert2@11"></script>
    <script>
        function downloadPDF() {
            window.open("{{ route('downloadPDF') }}", "_blank");
        }

        @if(in_array(auth()->user()->role->name,['admin','ceo','office-manager']))
        document.addEventListener("DOMContentLoaded", function () {
            const tableBody = document.querySelector("#price_table tbody");

            // فعال کردن درگ اند دراپ
            new Sortable(tableBody, {
                animation: 150,
                onEnd: function (evt) {
                    updateOrder();
                }
            });

            // تابع ارسال ترتیب به سرور
            function updateOrder() {
                let rows = document.querySelectorAll("#price_table tbody tr");
                let orderData = [];

                rows.forEach((row, index) => {
                    orderData.push({
                        id: row.dataset.priceId,
                        order: index + 1 // ترتیب جدید
                    });
                });

                // ارسال داده به سرور با AJAX
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
                        if (data.status === "success") {
                            Swal.fire({
                                title: 'ترتیب با موفقیت ذخیره شد',
                                icon: 'success',
                                showConfirmButton: false,
                                toast: true,
                                timer: 1500,
                                position: 'top-start'
                            });
                        } else {
                            Swal.fire({
                                title: 'خطا در ذخیره ترتیب',
                                icon: 'error',
                                showConfirmButton: true
                            });
                        }
                    })
                    .catch(error => console.error('خطا:', error));
            }
        });

        $(document).ready(function () {
            // باز کردن مدال انتخاب محصول
            $('#btn_open_modal').on('click', function () {
                $('#productModal').modal('show');
            });
            // هنگام تغییر انتخاب در سلکت
            $('#select_product').on('change', function () {
                var prodId = $(this).val();
                if (!prodId || prodId === 'all') return;
                var prodTitle = $('#select_product option:selected').text();

                // بررسی تکراری بودن محصول
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

                // دریافت اطلاعات جزئیات محصول از سرور
                $.ajax({
                    url: "{{ route('getProductDetails', ['id' => '%%ID%%']) }}".replace('%%ID%%', prodId),
                    type: 'get',
                    success: function (res) {
                        if (res.status === 'success') {
                            var newRow = `<tr data-price-id="${prodId}">
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
                            // پاکسازی انتخاب و بستن مدال
                            $('#select_product').val(null).trigger('change');
                            $('#productModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        console.error('خطا در دریافت اطلاعات:', xhr.responseText);
                    }
                });
            });

            // حذف محصول
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

            // به‌روز رسانی قیمت هنگام خروج از فیلد (blur)
            $(document).on('blur', '.price-input', function () {
                var input = $(this);
                var prodId = input.data('id');
                var field = input.data('field');
                var price = input.val().replace(/,/g, '');
                $.ajax({
                    url: "{{ route('MandegarPriceUpdate') }}",
                    type: 'post',
                    data: {
                        items: JSON.stringify([{'id': prodId, 'field': field, 'price': price}]),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
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

            // فرمت دهی قیمت هنگام تایپ
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
