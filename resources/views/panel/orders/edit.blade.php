@extends('panel.layouts.master')
@section('title', 'ثبت سفارش مشتری')
@section('styles')
    <style>
        #products_table input, #products_table select {
            width: auto;
        }

        #other_products_table input, #other_products_table select {
            width: auto;
        }
    </style>
@endsection
@section('content')
    {{--    @dd(json_decode($order->products))--}}
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">ویرایش سفارش مشتری</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex justify-content-between align-items-center mb-5">
                                <div class="w-100">
                                    @if($order->status != 'invoiced')
                                        <div class="col-12 mb-4 text-center mt-5">
                                            <h4>درخواست برای</h4>
                                        </div>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">

                                            <label class="btn btn-outline-primary justify-content-center"
                                                   for="req_for1"> <input type="radio" id="req_for1" name="req_for"
                                                                          class="btn-check"
                                                                          value="pre-invoice"
                                                                          form="invoice_form" {{ $order->req_for == 'pre-invoice' && old('req_for') == null || old('req_for') == 'pre-invoice' ? 'checked' : '' }}>پیش
                                                فاکتور</label>
                                            <label class="btn btn-outline-primary justify-content-center"
                                                   for="req_for2"><input type="radio" id="req_for2" name="req_for"
                                                                         class="btn-check"
                                                                         value="invoice"
                                                                         form="invoice_form" {{ $order->req_for == 'invoice' || old('req_for') == 'invoice' ? 'checked' : '' }}>فاکتور</label>

                                        </div>
                                    @else
                                        <input type="hidden" name="req_for" value="{{ $order->req_for }}"
                                               form="invoice_form">
                                    @endif
                                    <input type="hidden" name="type" value="official" form="invoice_form">
                                </div>
                            </div>
                            <form action="{{ route('orders.update',$order->id) }}" method="post" id="invoice_form">
                                @csrf
                                @method('PATCH')
                                <div class="row mb-4">
                                    <div class="col-12 mb-4 text-center">
                                        <h4>مشخصات مشتری</h4>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="buyer_name">نام شخص حقیقی/حقوقی <span
                                                class="text-danger">*</span></label>
                                        <select name="buyer_name" id="buyer_name" class="js-example-basic-single w-100">
                                            <option value="" disabled selected>انتخاب کنید...</option>
                                            @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                                <option
                                                    value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('buyer_name')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label for="payment_type">
                                            <span class="text-danger">*</span> نوع پرداخت
                                        </label>
                                        <select class="form-control" name="payment_type" id="payment_type">
                                            @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ (old('payment_type') ?? $order->payment_type) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="shipping_cost">هزینه ارسال</label>
                                        <input type="text" id="shipping_cost" name="shipping_cost" value="{{$order->shipping_cost}}"
                                               class="form-control">
                                        <div id="shipping_cost_display" class="mt-1 text-muted"></div>
                                        @error('shipping_cost')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row w-100 mb-4">
                                        <div class="col-xl-12 col-lg-12 col-md-12 mb-3">
                                            <label class="form-label" for="description">توضیحات بیشتر</label>
                                            <textarea name="description" id="description"
                                                      class="description form-control"
                                                      rows="10">{{ old('description',$order->description) }}</textarea>
                                            @error('description')
                                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                            @enderror
                                            <span class="text-info fst-italic">خط بعد Shift + Enter</span>
                                        </div>
                                    </div>


                                    @can('accountant')
                                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                            <label for="status">وضعیت <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control"
                                                    data-toggle="select2">
                                                <option
                                                    value="order" {{ $order->status == 'orders' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['orders'] }}</option>
                                                <option
                                                    value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['pending'] }}</option>
                                                <option
                                                    value="invoiced" {{ $order->status == 'invoiced' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['invoiced'] }}</option>
                                            </select>
                                            @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="{{ $order->status }}">
                                    @endcan

                                    <div class="col-12 mb-4 mt-2 text-center">
                                        <hr>
                                        <h4>مشخصات کالا یا خدمات مورد معامله</h4>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle font-size-20 align-middle"></i>
                                        <strong>توجه!</strong>
                                        همکار فروش گرامی قیمت کالا باید به صورت <u>قیمت تمام شده</u>(به همراه مالیات ،
                                        ارزش افزوده و...) قرار بگیرد.
                                    </div>
                                    <div class="col-12 mt-2 text-center">
                                        <h5>محصولات شرکت</h5>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between mb-3">
                                            <button class="btn btn-outline-success" type="button" id="btn_add"><i
                                                    class="fa fa-plus mr-2"></i> افزودن کالا
                                            </button>
                                        </div>
                                        <div class="overflow-auto">
                                            <table class="table table-bordered table-striped text-center"
                                                   id="products_table">
                                                <thead>
                                                <tr>
                                                    <th>کالا</th>
                                                    <th>رنگ</th>
                                                    <th>تعداد</th>
                                                    <th>واحد اندازه گیری</th>
                                                    <th>مبلغ واحد (ریال)</th>
                                                    <th>مبلغ کل (ریال)</th>
                                                    <th>حذف</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!is_null(json_decode($order->products)))
                                                    @foreach(json_decode($order->products) as  $product)
                                                        {{-- @dd($products->products)--}}
                                                        <tr>
                                                            <td>
                                                                <select class="js-example-basic-single w-100"
                                                                        name="products[]"
                                                                        required>
                                                                    <option value="" disabled selected>
                                                                        ..................... انتخاب کنید
                                                                        .....................
                                                                    </option>
                                                                    @foreach(\App\Models\Product::all(['id','title','code']) as $item)
                                                                        <option
                                                                            value="{{ $item->id }}" {{ $item->id == $product->products ? 'selected' : '' }}>{{ $item->code.' - '.$item->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control w-100" name="colors[]"
                                                                        required>
                                                                    @foreach(\App\Models\Product::COLORS as $key => $value)
                                                                        <option
                                                                            value="{{ $key }}" {{ $key == $product->colors ? 'selected' : '' }}>{{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="counts[]"
                                                                       class="form-control w-100" min="1"
                                                                       value="{{ $product->counts }}" required>
                                                            </td>
                                                            <td>
                                                                <select class="form-control w-100" name="units[]">
                                                                    <option value="number">عدد</option>
                                                                    <option value="pack">بسته</option>
                                                                    <option value="box">جعبه</option>
                                                                    <option value="kg">کیلوگرم</option>
                                                                    <option value="ton">تن</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="prices[]"
                                                                       class="form-control w-100" min="0"
                                                                       value="{{ $product->prices }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="total_prices[]"
                                                                       class="form-control w-100" min="0"
                                                                       value="{{$product->total_prices }}" readonly>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-danger btn-floating btn_remove"
                                                                        type="button"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary mt-5" type="submit" id="submit_button">
                                    ثبت فرم
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('input', '#shipping_cost', function () {
            // حذف کاماهای موجود
            let value = $(this).val().replace(/,/g, '');
            if(!isNaN(value) && value.trim() !== ''){
                // فرمت کردن عدد به صورت سه رقم سه رقم
                let formattedValue = new Intl.NumberFormat('fa-IR').format(value);
                // نمایش مقدار فرمت‌شده در المنت زیر اینپوت
                $('#shipping_cost_display').text(formattedValue + ' ریال');
            } else {
                $('#shipping_cost_display').text('');
            }
        });
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
        var products = [];
        var colors = [];

        var form = document.getElementById('invoice_form');
        form.addEventListener('keypress', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        })

        @foreach(\App\Models\Product::all(['id','title','code']) as $product)
        products.push({
            "id": "{{ $product->id }}",
            "title": "{{ $product->title }}",
            "code": "{{ $product->code }}",
        })
        @endforeach
        @foreach(\App\Models\Product::COLORS as $key => $value)
        colors.push({
            "key": "{{ $key }}",
            "value": "{{ $value }}",
        })
        @endforeach

        var products_options_html = '';
        var colors_options_html = '';

        $.each(products, function (i, item) {
            products_options_html += `<option value="${item.id}">${item.code} - ${item.title}</option>`
        })

        $.each(colors, function (i, item) {
            colors_options_html += `<option value="${item.key}">${item.value}</option>`
        })

        $(document).ready(function () {
            // add artin property
            $('#btn_add').on('click', function () {
                $('#products_table tbody').append(`
                <tr>
                <td>
                    <select class="js-example-basic-single w-100" name="products[]" style="width: 300px !important;" required>
                        <option value="" disabled selected>..................... انتخاب کنید .....................</option>
                        ${products_options_html}
                    </select>
                </td>
                <td>
                    <select class="form-control w-100" name="colors[]" required>
                        ${colors_options_html}
                    </select>
                </td>
                <td>
                    <input type="number" name="counts[]" class="form-control w-100" min="1" value="1" required>
                </td>
                <td>
                    <select class="form-control w-100" name="units[]">
                        <option value="number">عدد</option>
                        <option value="pack">بسته</option>
                        <option value="box">جعبه</option>
                        <option value="kg">کیلوگرم</option>
                        <option value="ton">تن</option>

                    </select>
                </td>
                <td>
                    <input type="number" name="prices[]" class="form-control w-100" min="0" value="0">
                </td>
                <td>
                    <input type="number" name="total_prices[]" class="form-control w-100" min="0" value="0" readonly>
                </td>
                <td>
                    <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                </td>
            </tr>`);
                $('[data-toggle="select2"]').select2();

            })
            // end add artin property

            // add other property
            $('#btn_other_add').on('click', function () {
                $('#other_products_table tbody').append(`
                <tr>
                <td>
                    <input type="text" class="form-control" name="other_products[]" placeholder="عنوان کالا" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="other_colors[]" placeholder="نام رنگ" required>
                </td>
                <td>
                    <input type="number" name="other_counts[]" class="form-control" min="1" value="1" required>
                </td>
                <td>
                    <select class="form-control" name="other_units[]">
                        <option value="number">عدد</option>
                        <option value="pack">بسته</option>
                        <option value="box">جعبه</option>
                        <option value="kg">کیلوگرم</option>
                        <option value="ton">تن</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="other_prices[]" class="form-control" min="0" value="0" required>
                    <span class="price_with_grouping text-primary"></span>
                </td>
                <td>
                    <input type="number" name="other_total_prices[]" class="form-control" min="0" value="0" readonly>
                    <span class="total_price_with_grouping text-primary"></span>
                </td>

                <td>
                    <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                </td>
            </tr>

`);
            })
            // end add other property

            // remove property
            $(document).on('click', '.btn_remove', function () {
                $(this).parent().parent().remove();
            })
            // end remove property

            // calc the product invoice
            $(document).on('change', '#products_table select[name="products[]"]', function () {
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه. ..');
                CalcProductInvoice(this)
            })
            $(document).on('keyup', '#products_table input[name="counts[]"]', function () {
                if (this.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                }
            });
            $(document).on('change', '#products_table input[name="counts[]"]', function () {
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                CalcProductInvoice(this)
            });
            $(document).on('input', '#products_table input[name="prices[]"]', function () {
                $('#btn_form').attr('disabled', true).text('درحال محاسبه...');
                console.log($(this).val()); // استفاده صحیح از متد val
                changePriceOnInput(this);
                // CalcProductInvoice(this)
            });
            // $(document).on('keyup', '#other_products_table input[name="other_counts[]"]', function (e) {
            //     if (e.originalEvent && e.originalEvent.explicitOriginalTarget) {
            //         if (e.originalEvent.explicitOriginalTarget.defaultValue != this.value) {
            //             $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
            //         }
            //     } else {
            //
            //         if (this.defaultValue != this.value) {
            //             $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
            //         }
            //     }
            //
            // });
            //
            // $(document).on('change', '#other_products_table input[name="other_counts[]"]', function () {
            //     $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
            //     CalcOtherProductInvoice(this)
            // })
            // $(document).on('keyup', '#other_products_table input[name="other_prices[]"]', function (e) {
            //     if (this.defaultValue != this.value) {
            //         $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
            //     }
            // });
            //
            // $(document).on('change', '#other_products_table input[name="other_prices[]"]', function () {
            //     CalcOtherProductInvoice(this)
            // })
            $(document).on('input', '#other_products_table input[name="other_counts[]"], #other_products_table input[name="other_prices[]"]', function () {
                // بررسی تغییر مقدار
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                CalcOtherProductInvoice(this);
            });

            // end calc the product invoice

            // get customer info
            $(document).on('change', 'select[name="buyer_name"]', function () {
                let customer_id = this.value;

                $.ajax({
                    url: '/panel/get-customer-info/' + customer_id,
                    type: 'post',
                    success: function (res) {
                        $('#economical_number').val(res.data.economical_number)
                        $('#national_number').val(res.data.national_number)
                        $('#postal_code').val(res.data.postal_code)
                        $('#phone').val(res.data.phone1)
                        $('#address').val(res.data.address1)
                        $('#province').val(res.data.province).trigger('change');
                        $('#city').val(res.data.city)
                    }
                })
            })
            // end get customer info
        })

        function CalcProductInvoice(changeable) {
            var index = $(changeable).parent().parent().index()
            let product_id = $('#products_table select[name="products[]"]')[index].value;
            let count = $('#products_table input[name="counts[]"]')[index].value;

            $.ajax({
                url: "{{ route('calcProductsInvoice') }}",
                type: 'post',
                data: {
                    'product_id': product_id,
                    'count': count,
                },
                success: function (res) {
                    $('#products_table input[name="prices[]"]')[index].value = res.data.price;
                    $('#products_table input[name="total_prices[]"]')[index].value = res.data.total_price;
                    $('#btn_form').removeAttr('disabled').text('ثبت فرم');
                },
                error: function (request, status, error) {
                    //
                }
            })
        }

        function CalcOtherProductInvoice(changeable) {
            var index = $(changeable).parent().parent().index()
            let count = $('#other_products_table input[name="other_counts[]"]')[index].value;
            let price = $('#other_products_table input[name="other_prices[]"]')[index].value;
            var total = 0;

            // thousands grouping
            $($('#other_products_table input[name="other_prices[]"]')[index]).siblings()[0].innerText = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            // $($('#other_products_table input[name="other_discount_amounts[]"]')[index]).siblings()[0].innerText = discount_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            total = price * count;
            $('#other_products_table input[name="other_prices[]"]')[index].value = price;
            $('#other_products_table input[name="other_total_prices[]"]')[index].value = total;
            $($('#other_products_table input[name="other_total_prices[]"]')[index]).siblings()[0].innerText = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $('#btn_form').removeAttr('disabled').text('ثبت فرم');

        }

        $('.description').keydown(function (e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                const cursorPos = this.selectionStart;
                const value = $(this).val();
                $(this).val(value.substring(0, cursorPos) + "\n" + value.substring(cursorPos));
                this.selectionStart = this.selectionEnd = cursorPos + 1;
            }
        });

        function changePriceOnInput(changeable) {
            var index = $(changeable).parent().parent().index();
            var count = $('#products_table input[name="counts[]"]')[index].value;
            var price = $('#products_table input[name="prices[]"]')[index].value;
            console.log("gheymat:" + price);
            console.log("tedad:" + count);
            var total = count * price;
            $('#products_table input[name="total_prices[]"]')[index].value = total;
            $($('#products_table input[name="prices[]"]')[index]).siblings()[0].innerText = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $($('#products_table input[name="total_prices[]"]')[index]).siblings()[0].innerText = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('#btn_form').removeAttr('disabled').text('ثبت فرم');
        }
    </script>
@endsection
