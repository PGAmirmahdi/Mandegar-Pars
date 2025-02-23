    @extends('panel.layouts.master')
    @section('title', 'ویرایش ' . (in_array(auth()->user()->role->name, [
        'setad_sale', 'internet_sale', 'free_sale',
        'industrial_sale', 'global_sale', 'organization_sale'
    ])
        ? ' درخواست ' . auth()->user()->role->label
        : 'درخواست های فروش'))
    @section('styles')
        <style>
            table tbody tr td input {
                text-align: center;
            }
        </style>
    @endsection
    @section('content')
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center mb-4">
                    <h6>ویرایش {{ in_array(auth()->user()->role->name, [
                                    'setad_sale', 'internet_sale', 'free_sale',
                                    'industrial_sale', 'global_sale', 'organization_sale'
                                ])
                                    ? ' درخواست ' . auth()->user()->role->label
                                    : 'درخواست های فروش' }}</h6>
                    <button type="button" class="btn btn-success" id="btn_add">
                        <i class="fa fa-plus mr-2"></i>
                        افزودن کالا
                    </button>
                </div>
                <form action="{{ route('sale_price_requests.update',$sale_price_request->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="col-12 mb-3">
                            <div class="col-12 row mb-4">
                                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                    <label class="form-label" for="customer">مشتری حقیقی/حقوقی<span class="text-danger">*</span></label>
                                    <select name="customer" id="customer" class="js-example-basic-single  select2-hidden-accessible" data-select2-id="1">
                                        <option value="" disabled selected>انتخاب کنید...</option>
                                        @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                            <option value="{{ $customer->id }}" {{ $sale_price_request->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->code.' - '.$customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('buyer_name')
                                    <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                    <label for="payment_type">نوع پرداختی</label>
                                    <select class="form-control" name="payment_type" id="payment_type">
                                        @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                            <option value="{{ $key }}" {{ $sale_price_request->payment_type == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                @can('setad_sale')
                                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                        <label for="date">تاریخ موعد<span class="text-danger">*</span></label>
                                        <input type="text" name="date" autocomplete="off" class="form-control date-picker-shamsi-list" id="date" value="{{ $sale_price_request->date }}">
                                        @error('date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4 clock-sec">
                                        <label>ساعت موعد<span class="text-danger">*</span></label>
                                        <div class="input-group clockpicker-autoclose-demo">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                            </div>
                                            <input type="text" autocomplete="off" name="hour" class="form-control text-left" value="{{ $sale_price_request->hour }}" dir="ltr">
                                        </div>
                                        @error('hour')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                        <label for="need_no">شماره نیاز<span class="text-danger">*</span></label>
                                        <input type="text" name="need_no" autocomplete="off" class="form-control" id="need_no" value="{{ $sale_price_request->need_no }}">
                                        @error('need_no')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endcan
                                @cannot('setad_sale')
                                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                        <label for="shipping_cost">هزینه ارسال(ریال)</label>
                                        <input type="text" name="shipping_cost" autocomplete="off" class="form-control" id="shipping_cost" value="{{ $sale_price_request->shipping_cost }}">
                                        <div id="shipping_cost_formatted" style="margin-top: 5px; font-weight: bold;"></div>
                                        @error('shipping_cost')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endcannot
                            </div>
                            <table class="table table-striped table-bordered text-center" id="products_table">
                                <thead class="bg-primary">
                                <tr>
                                    <th>عنوان کالا</th>
                                    <th>تعداد</th>
                                    <th>قیمت(ریال)</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(json_decode($sale_price_request->products) as $product)
                                    <tr>
                                        <td>
                                            <select class="js-example-basic-single" name="products[]" required>
                                                @foreach($products as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == $product->product_id ? 'selected' : '' }}>
                                                        {{ $item->category->slug . ' - ' . $item->title . ' - ' . $item->productModels->slug }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="counts[]" min="1" value="{{ $product->count }}" required autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control price-input" name="product_price[]" value="{{ $product->product_price }}" required autocomplete="off">
                                            <div class="formatted-price" style="margin-top: 5px; font-weight: bold;">
                                                {{ $product->product_price != 0 ? number_format($product->product_price) : '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-floating btn_remove">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2" class="text-center font-weight-bold" >مجموع قیمت(ریال):</td>
                                    <td colspan="2" id="price" class="font-weight-bold">{{$sale_price_request->price}}</td>
                                </tr>
                                </tfoot>
                            </table>
                            <input type="hidden" name="price" id="price_input" value="">
                            @error('products')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-12 row mb-4">
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                <label class="form-label" for="description">توضیحات</label>
                                <textarea name="description" id="description" class="description form-control" rows="10">{{ $sale_price_request->description }}</textarea>
                                @error('description')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-5" type="submit" id="submit_button">ثبت فرم</button>
                </form>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script>
            // رویداد تغییر فیلد هزینه ارسال
            $(document).on('input', '#shipping_cost', function () {
                let value = $(this).val().replace(/,/g, '');
                if (!isNaN(value) && value.trim() !== '') {
                    let formattedValue = new Intl.NumberFormat('fa-IR').format(value);
                    $('#shipping_cost_formatted').text(formattedValue + '  ریال ');
                } else {
                    $('#shipping_cost_formatted').text('');
                }
                calculateTotalPrice();
            });

            $(document).ready(function () {
                $('#submit_button').on('click', function () {
                    let button = $(this);
                    let dots = 0;
                    button.prop('disabled', true).text('در حال ارسال');
                    let interval = setInterval(() => {
                        dots = (dots + 1) % 4;
                        let text = 'در حال ارسال' + '.'.repeat(dots);
                        button.text(text).fadeOut(3000).fadeIn(3000);
                    }, 6000);
                    button.closest('form').submit();
                    setTimeout(() => clearInterval(interval), 10000);
                });
            });

            var products = [];
            var products_options_html = '';
            $(document).ready(function () {
                @foreach($products as $product)
                products.push({
                    "id": "{{ $product->id }}",
                    "title": "{{ $product->title }}",
                    "categorySlug": "{{ $product->category->slug }}",
                    "modelSlug": "{{ $product->productModels->slug }}"
                });
                @endforeach

                $.each(products, function (i, item) {
                    products_options_html += `<option value="${item.id}">${item.categorySlug + ' - ' + item.title + ' - ' + item.modelSlug}</option>`;
                });

                $('#store_form').on('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                    }
                });

                $('#submit_button').on('click', function () {
                    let button = $(this);
                    button.prop('disabled', true).text('در حال ارسال...');
                    button.closest('form').submit();
                });

                $(document).on('input', '.price-input', function () {
                    let value = $(this).val().replace(/,/g, '');
                    if (!isNaN(value) && value.trim() !== '') {
                        let formattedValue = new Intl.NumberFormat('fa-IR').format(value);
                        $(this).next('.formatted-price').text(formattedValue);
                    } else {
                        $(this).next('.formatted-price').text('');
                    }
                    calculateTotalPrice();
                });

                // تغییر تعداد کالا نیز باعث بروزرسانی مجموع می‌شود
                $(document).on('input', 'input[name="counts[]"]', function () {
                    calculateTotalPrice();
                });

                // افزودن ردیف جدید (در صورت نیاز)
                $(document).on('click', '#btn_add', function () {
                    $('table tbody').append(`
                    <tr>
                        <td>
                            <select class="js-example-basic-single" name="products[]" required>
                                <option value="" disabled selected>انتخاب کنید</option>
                                ${products_options_html}
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="counts[]" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control price-input" name="product_price[]" value="0" required>
                            <div class="formatted-price" style="margin-top: 5px; font-weight: bold;"></div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-floating btn_remove">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `);
                    $('.js-example-basic-single').select2();
                    calculateTotalPrice();
                });

                // حذف ردیف و بروزرسانی مجموع
                $(document).on('click', '.btn_remove', function () {
                    $(this).closest('tr').remove();
                    calculateTotalPrice();
                });

                // تابع محاسبه مجموع قیمت به همراه هزینه ارسال
                // به‌روزرسانی مجموع قیمت‌ها
                function calculateTotalPrice() {
                    let total = 0;
                    $('#products_table tbody tr').each(function () {
                        let count = parseFloat($(this).find('input[name="counts[]"]').val()) || 0;
                        let price = parseFloat($(this).find('input[name="product_price[]"]').val().replace(/,/g, '')) || 0;
                        total += count * price;
                    });

                    // اضافه کردن هزینه ارسال
                    let shippingCost = parseFloat($('#shipping_cost').val().replace(/,/g, '')) || 0;
                    total += shippingCost;

                    // نمایش مجموع
                    $('#price').text(new Intl.NumberFormat('fa-IR').format(total));
                    $('#price_input').val(total);
                }

                $(document).on('input', 'input[name="product_price[]"], input[name="counts[]"], #shipping_cost', function () {
                    calculateTotalPrice();
                });

                $(document).ready(function () {
                    calculateTotalPrice();
                });

            });
        </script>
    @endsection
