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
        .description{

        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">ثبت سفارش مشتری</h4>
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
                                    <div class="col-12 mb-4 text-center mt-5">
                                        <h4>درخواست برای</h4>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" id="req_for1" name="req_for" class="btn-check"
                                               value="pre-invoice"
                                               form="invoice_form" {{ old('req_for') == 'pre-invoice' || old('req_for') == null ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary justify-content-center" for="req_for1">پیش
                                            فاکتور</label>

                                        <input type="radio" id="req_for2" name="req_for" class="btn-check"
                                               value="invoice"
                                               form="invoice_form" {{ old('req_for') == 'invoice' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-primary justify-content-center" for="req_for2">فاکتور</label>

                                        {{--                                        <input type="radio" id="req_for3" name="req_for" class="btn-check" value="amani-invoice" form="invoice_form" {{ old('req_for') == 'amani-invoice' ? 'checked' : '' }}>--}}
                                        {{--                                        <label class="btn btn-outline-primary justify-content-center" for="req_for3">فاکتور امانی</label>--}}
                                    </div>
                                    <input type="hidden" name="type" value="official" form="invoice_form">
                                </div>
                            </div>
                            <form action="{{ route('orders.store') }}" method="post" id="invoice_form">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-12 mb-4 text-center">
                                        <h4>مشخصات مشتری</h4>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                            <label class="form-label" for="buyer_name">مشتری حقیقی/حقوقی<span
                                                    class="text-danger">*</span></label>
                                            <select name="buyer_name" id="buyer_name" class="form-control"
                                                    data-toggle="select2">
                                                <option value="" disabled selected>انتخاب کنید...</option>
                                                @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                                    <option
                                                        value="{{ $customer->id }}" {{ old('buyer_name') == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('buyer_name')
                                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                            <label class="form-label" for="description">توضیحات بیشتر</label>
                                            <textarea  name="description" id="description" class="description form-control" rows="10">{{ old('description') }}</textarea>
                                            @error('description')
                                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                            @enderror
                                            <span class="text-info fst-italic">خط بعد Shift + Enter</span>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4 mt-2 text-center">
                                        <hr>
                                        <h4>مشخصات کالا یا خدمات مورد معامله</h4>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle font-size-20 align-middle"></i>
                                        <strong>توجه!</strong>
                                         همکار فروش گرامی قیمت کالا باید به صورت <u>قیمت تمام شده</u>(به همراه مالیات ، ارزش افزوده و...) قرار بگیرد.
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
                                                @if(old('products'))
                                                    @foreach(old('products') as $i => $productId)
                                                        <tr>
                                                            <td>
                                                                <select class="form-control" name="products[]"
                                                                        data-toggle="select2" required>
                                                                    <option value="" disabled selected>
                                                                        ..................... انتخاب کنید
                                                                        .....................
                                                                    </option>
                                                                    @foreach(\App\Models\Product::all(['id','title','code']) as $item)
                                                                        <option
                                                                            value="{{ $item->id }}" {{ $item->id == $productId ? 'selected' : '' }}>{{ $item->code.' - '.$item->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="colors[]" required>
                                                                    @foreach(\App\Models\Product::COLORS as $key => $value)
                                                                        <option
                                                                            value="{{ $key }}" {{ $key == old('colors')[$i] ? 'selected' : '' }}>{{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="counts[]"
                                                                       class="form-control" min="1"
                                                                       value="{{ old('counts')[$i] }}" required>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="units[]">
                                                                    <option value="number">عدد</option>
                                                                    <option value="pack">بسته</option>
                                                                    <option value="box">جعبه</option>
                                                                    <option value="kg">کیلوگرم</option>
                                                                    <option value="ton">تن</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="prices[]"
                                                                       class="form-control" min="0"
                                                                       value="{{ old('prices')[$i] }}" readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="total_prices[]"
                                                                       class="form-control" min="0"
                                                                       value="{{ old('total_prices')[$i] }}" readonly>
                                                            </td>
{{--                                                            <td>--}}
{{--                                                                <input type="number" name="discount_amounts[]"--}}
{{--                                                                       class="form-control"--}}
{{--                                                                       min="0" value="{{ old('discount_amounts')[$i] }}"--}}
{{--                                                                       readonly>--}}
{{--                                                            </td>--}}
{{--                                                            <td>--}}
{{--                                                                <input type="number" name="extra_amounts[]"--}}
{{--                                                                       class="form-control" min="0"--}}
{{--                                                                       value="{{ old('extra_amounts')[$i] }}" readonly>--}}
{{--                                                            </td>--}}
{{--                                                            <td>--}}
{{--                                                                <input type="number" name="total_prices_with_off[]"--}}
{{--                                                                       class="form-control"--}}
{{--                                                                       min="0"--}}
{{--                                                                       value="{{ old('total_prices_with_off')[$i] }}"--}}
{{--                                                                       readonly>--}}
{{--                                                            </td>--}}
{{--                                                            <td>--}}
{{--                                                                <input type="number" name="taxes[]" class="form-control"--}}
{{--                                                                       min="0"--}}
{{--                                                                       value="{{ old('taxes')[$i] }}" readonly>--}}
{{--                                                            </td>--}}
{{--                                                            <td>--}}
{{--                                                                <input type="number" name="invoice_nets[]"--}}
{{--                                                                       class="form-control" min="0"--}}
{{--                                                                       value="{{ old('invoice_nets')[$i] }}" readonly>--}}
{{--                                                            </td>--}}
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
                                    <div class="col-12 mt-4 text-center">
                                        <h5>محصولات دیگر</h5>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between mb-3">
                                            <button class="btn btn-outline-success" type="button" id="btn_other_add"><i
                                                    class="fa fa-plus mr-2"></i> افزودن کالا
                                            </button>
                                        </div>
                                        <div class="overflow-auto">
                                            <table class="table table-bordered table-striped text-center"
                                                   id="other_products_table">
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
                                                @if(old('other_products'))
                                                    @foreach(old('other_products') as $i => $otherProduct)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                       name="other_products[]"
                                                                       placeholder="عنوان کالا"
                                                                       value="{{ $otherProduct }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                       name="other_colors[]"
                                                                       placeholder="نام رنگ"
                                                                       value="{{ old('other_colors')[$i] }}"
                                                                       required>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="other_counts[]"
                                                                       class="form-control" min="1"
                                                                       value="{{ old('other_counts')[$i] }}" required>
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
                                                                <input type="number" name="other_prices[]"
                                                                       class="form-control" min="0"
                                                                       value="{{ old('other_prices')[$i] }}" required>
                                                                <span class="price_with_grouping text-primary">{{ number_format(old('other_prices')[$i]) }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="other_total_prices[]"
                                                                       class="form-control"
                                                                       min="0"
                                                                       value="{{ old('other_total_prices')[$i] }}"
                                                                       readonly>
                                                                <span class="total_price_with_grouping text-primary">{{number_format(old('other_total_prices')[$i])}}</span>
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
                                <button class="btn btn-primary" type="submit" id="btn_form">ثبت فرم</button>
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
                    <select class="form-control" name="products[]" data-toggle="select2" style="width: 300px !important;" required>
                        <option value="" disabled selected>..................... انتخاب کنید .....................</option>
                        ${products_options_html}
                    </select>
                </td>
                <td>
                    <select class="form-control" name="colors[]" required>
                        ${colors_options_html}
                    </select>
                </td>
                <td>
                    <input type="number" name="counts[]" class="form-control" min="1" value="1" required>
                </td>
                <td>
                    <select class="form-control" name="units[]">
                            <option value="number">عدد</option>
                            <option value="pack">بسته</option>
                            <option value="box">جعبه</option>
                            <option value="kg">کیلوگرم</option>
                            <option value="ton">تن</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="prices[]" class="form-control" min="0" value="0" readonly>
                </td>
                <td>
                    <input type="number" name="total_prices[]" class="form-control" min="0" value="0" readonly>
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
            })
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
            // $(document).on('input', '#other_products_table input[name="other_prices[]"]', function () {
            //     CalcOtherProductInvoice(this)
            // })

            $(document).on('input', '#other_products_table input[name="other_counts[]"], #other_products_table input[name="other_prices[]"]', function () {
                // بررسی تغییر مقدار
                // if (this.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                    CalcOtherProductInvoice(this);
                // }
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

            total = price *count;
            $('#other_products_table input[name="other_prices[]"]')[index].value = price;
            $('#other_products_table input[name="other_total_prices[]"]')[index].value = total;
            $($('#other_products_table input[name="other_total_prices[]"]')[index]).siblings()[0].innerText = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $('#btn_form').removeAttr('disabled').text('ثبت فرم');

        }
        $('.description').keydown(function(e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                const cursorPos = this.selectionStart;
                const value = $(this).val();
                $(this).val(value.substring(0, cursorPos) + "\n" + value.substring(cursorPos));
                this.selectionStart = this.selectionEnd = cursorPos + 1;
            }
        });

    </script>
@endsection
