@extends('panel.layouts.master')
@section('title', 'ثبت خروجی')

@section('styles')
    <style>
        .input-group > .custom-select:not(:first-child),
        .input-group > .form-control:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: .25rem;
            border-bottom-right-radius: .25rem;
        }
        .input-group > .input-group-append:last-child > .btn:not(:last-child):not(.dropdown-toggle),
        .input-group > .input-group-append:last-child > .input-group-text:not(:last-child),
        .input-group > .input-group-append:not(:last-child) > .btn,
        .input-group > .input-group-append:not(:last-child) > .input-group-text,
        .input-group > .input-group-prepend > .btn,
        .input-group > .input-group-prepend > .input-group-text {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: .25rem;
            border-bottom-left-radius: .25rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت خروجی</h6>
                <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
            </div>
            <form action="{{ route('inventory-reports.store') }}" method="post">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <input type="hidden" name="type" value="{{ request()->type }}">

                <div class="row">
                    <!-- Order Selection -->
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <label for="invoice_id">سفارش</label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="invoice_id" id="invoice_id">
                            <option value="">انتخاب کنید...</option>
                            @if(\App\Models\Invoice::doesntHave('inventory_report')->count())
                                @foreach(\App\Models\Invoice::doesntHave('inventory_report')->get() as $invoice)
                                    <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                        {{ $invoice->id }} - {{ $invoice->customer->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled selected>سفارشی موجود نیست!</option>
                            @endif
                        </select>
                        <span id="factor_link">
                            <a href="" class="btn-link" target="_blank">نمایش سفارش</a>
                        </span>
                        @error('invoice_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Guarantee Serial -->
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <label for="guarantee_serial">سریال گارانتی</label>
                        <div class="input-group mb-2" style="direction: ltr">
                            <div class="input-group-prepend">
                                <div class="input-group-text">MP</div>
                            </div>
                            <input type="text" name="guarantee_serial" id="guarantee_serial" class="form-control" value="{{ old('guarantee_serial') }}" maxlength="8">
                        </div>
                        <div id="serial_status"></div>

                        @error('guarantee_serial')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recipient -->
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="person"> تحویل گیرنده <span class="text-danger">*</span></label>
                            <input type="text" name="person" class="form-control" id="person" value="{{ old('person') }}">
                            @error('person')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Output Date -->
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="output_date"> تاریخ خروج <span class="text-danger">*</span></label>
                            <input type="text" name="output_date" class="form-control date-picker-shamsi-list" id="output_date" value="{{ old('output_date') ?? verta()->format('Y/m/d') }}">
                            @error('output_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Table -->
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                <tr>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(old('inventory_id') ?? [] as $key => $inventory_id)
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
                                            <input type="number" name="counts[]" class="form-control" min="1" value="{{ old('counts')[$key] ?? 1 }}" required>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <!-- Error Handling for Inventory Count -->
                            @error('inventory_count')
                            <div class="alert alert-danger">
                                <p><strong>توجه!</strong> موجودی کالا در انبار جهت خروج کافی نمی باشد: </p>
                                <ul>
                                    @foreach(session('error_data') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button class="btn btn-primary" type="submit" id="btn_submit">ثبت فرم</button>
            </form>
        </div>
    </div>
    <span>عکس لیبل:</span>
    <div class="label col-6">
        <div class="row justify-content-center align-items-center" style="gap: 20px">
            <div class="d-flex flex-column" style="gap: 10px">
                <img src="{{asset('/assets/media/image/logo-192x192.png')}}" alt="Logo">
                <span class="font-weight-bolder">صنایع ماشین های اداری</span>
                <h4>ماندگار پارس</h4>
            </div>
            <div>
                <h5>فرستنده: ماندگار پارس</h5>
                <h5>شماره تماس: 09029463357</h5>
                <span>آدرس: تهران، صفادشت، شهرک صنعتی صفادشت خیابان خرداد، بین خیابان 5 و 6 غربی، پلاک 212</span>
                <h6 class="font-weight-bold">((با تشکر از خرید شما))</h6>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var inventory = [];

        var options_html = "";  // تعریف متغیر options_html به صورت رشته‌ای

        @foreach(\App\Models\Inventory::with(['product' => function ($q) {
            $q->with('category:id,name')->select('id','title','code','category_id');
        }])->where('warehouse_id', $warehouse_id)->get(['id', 'product_id']) as $item)

        inventory.push({
            "id": "{{ $item->id }}",
            "title": "{{ $item->product->title }}",
            "code": "{{ $item->product->code }}",
            "category": "{{ $item->product->category->name }}",
        })
        @endforeach

        $.each(inventory, function (i, item) {
            options_html += `<option value="${item.id}">${item.category} - ${item.title}</option>`;  // نمایش کد و عنوان محصول
        });
        $(document).ready(function () {
            // Add property
            $('#btn_add').on('click', function () {
                $('#properties_table tbody').append(`
                    <tr>
                        <td>
                            <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">${options_html}</select>
                        </td>
                        <td><input type="number" name="counts[]" class="form-control" min="1" value="1" required></td>
                        <td><button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `);

                $('.js-example-basic-single').select2();
            });

            // Remove property
            $(document).on('click', '.btn_remove', function () {
                $(this).parent().parent().remove();
            });

            // Check invoice changes
            $(document).on('change', '#invoice_id', function () {
                if (this.value !== '') {
                    let invoice_id = this.value;
                    $.ajax({
                        type: 'post',
                        url: '/api/get-invoice-products',
                        data: {
                            invoice_id
                        },
                        success: function (res) {
                            let url = `/panel/invoices/${res.invoice_id}`;
                            $('#factor_link a').attr('href', url);

                            if (res.missed) {
                                $('#alert_section').removeClass('d-none');
                                $('#alert_section #miss_products').removeClass('d-none');
                                $('#alert_section #miss_products #codes').text(res.miss_products);
                            } else {
                                $('#alert_section').addClass('d-none');
                                $('#alert_section #miss_products').addClass('d-none');
                            }

                            if (res.other_products.length) {
                                $('#alert_section').removeClass('d-none');
                                $('#alert_section #other_products').removeClass('d-none');
                                $('#alert_section #other_products #items').html('');

                                $.each(res.other_products, function (i, product) {
                                    $('#alert_section #other_products #items').append(`<li>${product.title}</li>`);
                                });
                            } else {
                                $('#alert_section').addClass('d-none');
                                $('#alert_section #other_products').addClass('d-none');
                            }

                            $('#properties_table tbody').html('');

                            $.each(res.data, function (i, product) {
                                var options_html2;

                                $.each(inventory, function (i, item) {
                                    options_html2 += `<option value="${item.id}" ${item.code === product.code ? 'selected' : ''}>${item.category} - ${item.title}</option>`;
                                });

                                $('#properties_table tbody').append(`
                                    <tr>
                                        <td>
                                            <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">${options_html2}</select>
                                        </td>
                                        <td><input type="number" name="counts[]" class="form-control" min="1" value="${product.pivot.count}" required></td>
                                        <td><button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                `);
                            });

                            $('.js-example-basic-single').select2();
                        }
                    });
                }
            });

            // Serial check
            var last_value_length;
            serialCheck($('#guarantee_serial').val());

            $('#guarantee_serial').on('keyup', function () {
                serialCheck(this.value);
            });

            function serialCheck(serial) {
                if (serial.length === 8 && last_value_length !== 8) {
                    $.ajax({
                        url: "{{ route('serial.check') }}",
                        type: 'post',
                        data: {
                            serial
                        },
                        success: function (res) {
                            if (res.data.error) {
                                $('#serial_status').html(`<small class="text-danger">${res.data.message}</small>`);
                                $('#btn_submit').attr('disabled', 'disabled');
                            } else {
                                $('#serial_status').html(`<small class="text-success">${res.data.message}</small>`);
                                $('#btn_submit').removeAttr('disabled');
                            }
                        }
                    });
                } else if (serial.length < 8 && serial.length !== 0) {
                    $('#serial_status').html(`<small class="text-danger">سریال گارانتی معتبر نیست</small>`);
                    $('#btn_submit').attr('disabled', 'disabled');
                } else if (serial.length === 0) {
                    $('#serial_status').html('');
                    $('#btn_submit').removeAttr('disabled');
                }

                last_value_length = serial.length;
            }
        });
    </script>
@endsection
