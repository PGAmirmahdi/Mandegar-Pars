@extends('panel.layouts.master')
@section('title', 'ویرایش پیش فاکتور')
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
    {{--  discount Modal  --}}
    <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalLabel">اعمال کد تخفیف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="code" class="col-form-label"> کد تخفیف:<span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="code">
                            <div class="invalid-feedback d-flex" id="error_div"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-primary" id="btn_apply_discount">اعمال</button>
                </div>
            </div>
        </div>
    </div>
    {{--  end discount Modal  --}}
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">ویرایش پیش فاکتور</h4>
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
                                    @if($invoice->status != 'invoiced')
                                        <div class="col-12 mb-4 text-center mt-5">
                                            <h4>درخواست برای</h4>
                                        </div>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label
                                                class="btn btn-outline-primary justify-content-center {{ $invoice->req_for == 'pre-invoice' && old('req_for') == null || old('req_for') == 'pre-invoice' ? 'active' : '' }}">
                                                <input type="radio" id="req_for1" name="req_for"
                                                       class="custom-control-input" value="pre-invoice"
                                                       form="invoice_form" {{ $invoice->req_for == 'pre-invoice' || old('req_for') == 'pre-invoice' ? 'checked' : '' }}>پیش
                                                فاکتور
                                            </label>
                                            <label
                                                class="btn btn-outline-primary justify-content-center {{ $invoice->req_for == 'invoice' && old('req_for') == null || old('req_for') == 'invoice' ? 'active' : '' }}">
                                                <input type="radio" id="req_for2" name="req_for"
                                                       class="custom-control-input" value="invoice"
                                                       form="invoice_form" {{ $invoice->req_for == 'invoice' || old('req_for') == 'invoice' ? 'checked' : '' }}>فاکتور
                                            </label>
                                            <label
                                                class="btn btn-outline-primary justify-content-center {{ $invoice->req_for == 'amani-invoice' && old('req_for') == null || old('req_for') == 'amani-invoice' ? 'active' : '' }}">
                                                <input type="radio" id="req_for2" name="req_for"
                                                       class="custom-control-input" value="amani-invoice"
                                                       form="invoice_form" {{ $invoice->req_for == 'amani-invoice' || old('req_for') == 'amani-invoice' ? 'checked' : '' }}>فاکتور
                                                امانی
                                            </label>
                                        </div>
                                    @else
                                        <input type="hidden" name="req_for" value="{{ $invoice->req_for }}"
                                               form="invoice_form">
                                    @endif
                                    <input type="hidden" name="type" value="official" form="invoice_form">
                                </div>
                            </div>
                            <form action="{{ route('invoices.update', $invoice->id) }}" method="post" id="invoice_form"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="row mb-4">
                                    <div class="col-12 mb-4 text-center">
                                        <h4>مشخصات خریدار</h4>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="buyer_name">شناسه سفارش<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="code" value="{{old('code',$invoice->order->code??'')}}"
                                               class="form-control"
                                               id="code"
                                               placeholder="شناسه سفارش را وارد کنید..." readonly>
                                        <div class="invalid-feedback text-info d-block" id="process_desc"></div>
                                        @error('code')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="buyer_name">نام شخص حقیقی/حقوقی <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="buyer_name"
                                               value="{{old('buyer_name',$invoice->customer->name)}}"
                                               class="form-control" id="buyer_name" readonly>
                                        <input type="hidden" name="buyer_id" id="buyer_id"
                                               value="{{old('buyer_id',$invoice->customer_id)}}">
                                        @error('buyer_name')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="economical_number">شماره اقتصادی
                                            @can('system-user')
                                                <span class="text-danger">*</span>
                                            @endcan
                                        </label>
                                        <input type="text" name="economical_number" class="form-control"
                                               id="economical_number" value="{{ $invoice->economical_number }}">
                                        @error('economical_number')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="national_number">شماره ثبت/ملی<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="national_number" class="form-control"
                                               id="national_number" value="{{ $invoice->national_number }}">
                                        @error('national_number')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="need_no">شماره نیاز</label>
                                        <input type="text" name="need_no" class="form-control" id="need_no"
                                               value="{{ $invoice->need_no }}">
                                        @error('need_no')
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
                                                    {{ (old('payment_type') ?? $invoice->payment_type) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="postal_code">کد پستی<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="postal_code" class="form-control" id="postal_code"
                                               value="{{ $invoice->postal_code }}">
                                        @error('postal_code')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="phone">شماره تماس<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" id="phone"
                                               value="{{ $invoice->phone }}">
                                        @error('phone')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="province">استان <span
                                                class="text-danger">*</span></label>
                                        <select name="province" id="province" class="form-control"
                                                data-toggle="select2">
                                            @foreach(\App\Models\Province::all() as $province)
                                                <option
                                                    value="{{ $province->name }}" {{ $invoice->province == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('province')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="city">شهر<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="city" class="form-control" id="city"
                                               value="{{ $invoice->city }}">
                                        @error('city')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="address">نشانی<span class="text-danger">*</span></label>
                                        <textarea name="address" id="address"
                                                  class="form-control">{{ $invoice->address }}</textarea>
                                        @error('address')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="description">توضیحات</label>
                                        <textarea name="description" rows="5" id="description"
                                                  class="form-control description">{{ $invoice->description }}</textarea>
                                        <span class="text-info fst-italic">خط بعد Shift + Enter</span>
                                        @error('description')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="payment_doc">رسید پرداخت</label>
                                        <input type="file" name="payment_doc" id="payment_doc" class="form-control"
                                               accept="application/pdf,image/png,image/jpg,image/jpeg">
                                        @if($invoice->payment_doc)
                                            <a href="{{ $invoice->payment_doc }}" target="_blank">دانلود پیش فاکتور</a>
                                        @endif
                                        @error('payment_doc')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @can('accountant')
                                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                            <label for="status">وضعیت <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control"
                                                    data-toggle="select2">
                                                <option
                                                    value="order" {{ $invoice->status == 'orders' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['orders'] }}</option>
                                                <option
                                                    value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['pending'] }}</option>
                                                <option
                                                    value="invoiced" {{ $invoice->status == 'invoiced' ? 'selected' : '' }}>{{ \App\Models\Invoice::STATUS['invoiced'] }}</option>
                                            </select>
                                            @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="{{ $invoice->status }}">
                                    @endcan

                                    @if($invoice->status != 'invoiced' || \Illuminate\Support\Facades\Gate::allows('sales-manager'))
                                        <div class="col-12 mb-4 mt-2 text-center">
                                            <hr>
                                            <h4>مشخصات کالا یا خدمات مورد معامله</h4>
                                        </div>
                                        <div class="col-12 mt-2 text-center">
                                            <h5>محصولات </h5>
                                        </div>
                                        @can('accountant')
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle font-size-20 align-middle"></i>
                                                <strong>توجه!</strong>
                                                حسابدار گرامی قیمت کالا های وارد شده در سفارش مشتری ، به صورت قیمت تمام
                                                شده
                                                (به همراه مالیات ، ارزش افزوده و ...) محاسبه شده است . در صورت نیاز به
                                                اطلاعات بیشتر با واحد فروش ارتباط برقرار کنید.
                                            </div>
                                        @endcan
                                        <div class="col-12 mt-4 text-center">
                                            <h5>محصولات</h5>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-between mb-3">
                                                <button class="btn btn-outline-success" type="button"
                                                        id="btn_other_add">
                                                    <i class="fa fa-plus mr-2"></i> افزودن کالا
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
                                                        <th>مبلغ واحد</th>
                                                        <th>مبلغ کل</th>
                                                        <th>مبلغ تخفیف</th>
                                                        <th>مبلغ اضافات</th>
                                                        <th>مبلغ کل پس از تخفیف و اضافات</th>
                                                        <th>جمع مالیات و عوارض</th>
                                                        <th>خالص فاکتور</th>
                                                        <th>حذف</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($invoice->other_products()->exists())
                                                        @foreach($invoice->other_products as $product)
                                                            <tr>
                                                                <td>
                                                                    <input type="text" name="other_products[]"
                                                                           class="form-control"
                                                                           value="{{ $product->title }}"
                                                                           placeholder="عنوان کالا"
                                                                           required readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="other_colors[]"
                                                                           class="form-control"
                                                                           value="{{ $product->color }}"
                                                                           placeholder="نام رنگ" required readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_counts[]"
                                                                           class="form-control"
                                                                           min="1" value="{{ $product->count }}"
                                                                           required readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="other_units[]"
                                                                            readonly>
                                                                        <option
                                                                            value="number" {{$product->unit == 'number' ? 'selected':''}}>
                                                                            عدد
                                                                        </option>
                                                                        <option
                                                                            value="pack" {{$product->unit == 'pack' ? 'selected':''}}>
                                                                            بسته
                                                                        </option>
                                                                        <option
                                                                            value="box" {{$product->unit == 'box' ? 'selected':''}}>
                                                                            جعبه
                                                                        </option>
                                                                        <option
                                                                            value="kg" {{$product->unit == 'kg' ? 'selected':''}}>
                                                                            کیلوگرم
                                                                        </option>
                                                                        <option
                                                                            value="ton" {{$product->unit == 'ton' ? 'selected':''}}>
                                                                            تن
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_prices[]"
                                                                           class="form-control"
                                                                           min="0" value="{{ $product->price }}"
                                                                           required>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->price )}}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_total_prices[]"
                                                                           class="form-control" min="0"
                                                                           value="{{ $product->total_price }}" readonly>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->total_price) }}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_discount_amounts[]"
                                                                           class="form-control" min="0"
                                                                           value="{{ $product->discount_amount }}"
                                                                           required>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->discount_amount)}}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_extra_amounts[]"
                                                                           class="form-control" min="0"
                                                                           value="{{ $product->extra_amount }}"
                                                                           readonly>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->extra_amount) }}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                           name="other_total_prices_with_off[]"
                                                                           class="form-control" min="0"
                                                                           value="{{ $product->total_price - ($product->extra_amount + $product->discount_amount) }}"
                                                                           readonly>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{number_format( $product->total_price - ($product->extra_amount + $product->discount_amount))}}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_taxes[]"
                                                                           class="form-control"
                                                                           min="0" value="{{ $product->tax }}">
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->tax) }}</span>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="other_invoice_nets[]"
                                                                           class="form-control" min="0"
                                                                           value="{{ $product->invoice_net }}" readonly>
                                                                    <span
                                                                        class="price_with_grouping text-primary">{{ number_format($product->invoice_net)}}</span>
                                                                </td>
                                                                <td>
                                                                    <button
                                                                        class="btn btn-danger btn-floating btn_remove"
                                                                        type="button"><i class="fa fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <span class="">مجموع سفارش مشتری (ریال) :<span
                                                    class="text-primary sum_total_price">{{number_format(old('sum_total_price',calculateTotal($order)))}}</span></span>
                                            <span class="">مجموع پیش فاکتور با مالیات و ارزش افزوده (ریال) :<span
                                                    class="text-primary total_invoice">{{number_format(old('total_invoice',calculateTotalInvoice($invoice->other_products)))}}</span></span>
                                            <input type="hidden" class="sum_total_price"
                                                   value="{{number_format(old('sum_total_price',calculateTotal($order)))}}"
                                                   name="sum_total_price">
                                            <input type="hidden" class="total_invoice"
                                                   value="{{number_format(old('total_invoice',calculateTotalInvoice($invoice->other_products)))}}"
                                                   name="total_invoice">

                                        </div>
                                        <div class="col-12 mb-2 mt-2 text-center">
                                            <hr>
                                            <h4>تخفیف نهایی</h4>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="final_discount">مبلغ تخفیف</label>
                                                <input type="text" class="form-control" name="final_discount"
                                                       id="final_discount" value="{{ $invoice->discount }}" required>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-primary float-start mx-1" type="submit" id="btn_form">ثبت فرم
                                </button>

                            </form>
                            <form action="{{ route('invoices.download') }}" method="post" class="">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                <button class="btn btn-danger"><i class="fa fa-file-pdf me-2"></i>دانلود</button>
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
        $(document).ready(function () {
            $('#btn_form').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
        var products = [];
        var colors = [];
        var totalTotalInvoice = 0;

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
                </td>
                <td>
                    <input type="number" name="other_discount_amounts[]" class="form-control" min="0" value="0" required>
                    <span class="price_with_grouping text-primary"></span>
                </td>
                <td>
                    <input type="number" name="other_extra_amounts[]" class="form-control" min="0" value="0" readonly>
                    <span class="price_with_grouping text-primary"></span>
                </td>
                <td>
                    <input type="number" name="other_total_prices_with_off[]" class="form-control" min="0" value="0" readonly>
                    <span class="price_with_grouping text-primary"></span>
                </td>
                <td>
                    <input type="number" name="other_taxes[]" class="form-control" min="0" value="0">
                    <span class="price_with_grouping text-primary"></span>
                </td>
                <td>
                    <input type="number" name="other_invoice_nets[]" class="form-control" min="0" value="0" readonly>
                    <span class="price_with_grouping text-primary"></span>
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
                var row = $(this).closest('tr');
                row.remove(); // حذف سطر
                updatePrice();

            })


            function handleInputChange(inputName) {
                $(document).on('keyup change', `#other_products_table input[name="${inputName}"]`, function (e) {
                    var defaultValue = $(this).prop('defaultValue');
                    if (defaultValue !== this.value) {
                        $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                    } else {
                        $('#btn_form').removeAttr('disabled').text('ثبت فرم');
                    }

                    if (e.type === 'change') {
                        CalcOtherProductInvoice(this);

                    }
                });
            }

            handleInputChange('other_counts[]');
            handleInputChange('other_prices[]');
            handleInputChange('other_discount_amounts[]');

            // end calc the product invoice

            // get customer info
            $(document).on('change', 'select[name="buyer_name"]', function () {
                let customer_id = this.value;

                $.ajax({
                    url: '/panel/get-customer-info/' + customer_id,
                    type: 'post',
                    success: function (res) {
                        console.log(res.data)
                        $('#economical_number').val(res.data.economical_number)
                        $('#national_number').val(res.data.national_number)
                        $('#postal_code').val(res.data.postal_code)
                        $('#phone').val(res.data.phone1)
                        $('#address').val(res.data.address1)
                        $('#province').val(res.data.province).trigger('change');
                        $('#city').val(res.data.city)
                    }
                });
            });
            // end get customer info
        });


        function CalcOtherProductInvoice(changeable) {

            var index = $(changeable).parent().parent().index();
            let count = $('#other_products_table input[name="other_counts[]"]')[index].value;
            let price = $('#other_products_table input[name="other_prices[]"]')[index].value;
            let discount_amount = $('#other_products_table input[name="other_discount_amounts[]"]')[index].value;


            // thousands grouping
            $($('#other_products_table input[name="other_prices[]"]')[index]).siblings()[0].innerText = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $($('#other_products_table input[name="other_discount_amounts[]"]')[index]).siblings()[0].innerText = discount_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $.ajax({
                url: "{{ route('calcOtherProductsInvoice') }}",
                type: 'post',
                data: {
                    'price': price,
                    'count': count,
                    'discount_amount': discount_amount,
                },
                success: function (res) {

                    $('#other_products_table input[name="other_prices[]"]')[index].value = res.data.price;
                    $('#other_products_table input[name="other_total_prices[]"]')[index].value = res.data.total_price;
                    $('#other_products_table input[name="other_discount_amounts[]"]')[index].value = res.data.discount_amount;
                    $('#other_products_table input[name="other_extra_amounts[]"]')[index].value = res.data.extra_amount;
                    $('#other_products_table input[name="other_total_prices_with_off[]"]')[index].value = res.data.total_price_with_off;
                    $('#other_products_table input[name="other_taxes[]"]')[index].value = res.data.tax;
                    $('#other_products_table input[name="other_invoice_nets[]"]')[index].value = res.data.invoice_net;
                    updatePrice();
                    updateTableData(index, res);

                    $('#btn_form').removeAttr('disabled').text('ثبت فرم');
                },
                error: function (request, status, error) {
                    //
                }
            })
        }


        $(document).ready(function () {
            $(document).on('input', '#code', function () {
                var inputVal = $(this).val().trim();
                var processDesc = $('#process_desc');
                if (inputVal === '') {
                    $('#buyer_name, #economical_number, #national_number, #postal_code, #phone, #address, #province, #city').val('');
                    $('#other_products_table tbody').empty();
                    processDesc.empty();
                    $('.sum_total_price').text('0').val('0');
                    $('.total_invoice').text('0').val('0');
                    return;
                }

                $.ajax({
                    url: '/panel/get-customer-order/' + $(this).val(),
                    method: 'GET',
                    beforeSend: function () {
                        processDesc.empty();
                        processDesc.html('در حال پردازش');
                    },
                    success: function (response) {

                        handleResponse(response);
                    },
                    error: function (xhr, status, error) {
                        processDesc.hide();
                        console.error('خطا در ارسال درخواست:', error);
                    }
                });

            });

            function handleResponse(response) {
                var processDesc = $('#process_desc');
                if (response.status === 'success') {
                    $('#buyer_name').val(response.data.customer.name)
                    $('#buyer_id').val(response.data.customer.id)
                    $('#economical_number').val(response.data.customer.economical_number ?? 0)
                    $('#national_number').val(response.data.customer.national_number ?? 0)
                    $('#postal_code').val(response.data.customer.postal_code)
                    $('#phone').val(response.data.customer.phone1)
                    $('#address').val(response.data.customer.address1)
                    $('#province').val(response.data.customer.province).trigger('change');
                    $('#payment_type').val(response.data.payment_type).trigger('change');
                    $('#city').val(response.data.customer.city)
                    $('.sum_total_price').text(formatNumber(response.data.total_price)).val(response.data.total_price)
                    $('#other_products_table tbody').empty();
                    add_products(response.data.order);
                    processDesc.html("<span class='text-success'>تایید ✓</span>");
                } else {
                    $('#buyer_name, #economical_number, #national_number, #postal_code, #phone, #address, #province, #city, #payment_type').val('');
                    $('.sum_total_price').text('0').val('0');
                    $('.total_invoice').text('0').val('0');
                    $('#other_products_table tbody').empty();
                    processDesc.html("<span class='text-danger'>شناسه پیگیری یافت نشد</span>");
                }

            }


            function add_products($data) {

                // console.log($data)
                // var otherProducts = productsData.other_products;
                $data.forEach(item => {
                    $('#other_products_table tbody').append(`
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="other_products[]" value="${item.title}" placeholder="عنوان کالا" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="other_colors[]" value="${item.color}" placeholder="نام رنگ" readonly>
                        </td>
                        <td>
                            <input type="number" name="other_counts[]" class="form-control" min="1" value="${item.count}" readonly>
                        </td>
                        <td>
                            <select class="form-control" name="other_units[]" readonly>
                                <option value="number" ${item.unit === 'number' ? 'selected' : ''}>عدد</option>
                                <option value="pack" ${item.unit === 'pack' ? 'selected' : ''}>بسته</option>
                                <option value="box" ${item.unit === 'box' ? 'selected' : ''}>جعبه</option>
                                <option value="kg" ${item.unit === 'kg' ? 'selected' : ''}>کیلوگرم</option>
                                <option value="ton" ${item.unit === 'ton' ? 'selected' : ''}>تن</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="other_prices[]" class="form-control" min="0" value="0" required>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_total_prices[]" class="form-control" min="0" value="0" readonly>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_discount_amounts[]" class="form-control" min="0" value="0" required>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_extra_amounts[]" class="form-control" min="0" value="0" readonly>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_total_prices_with_off[]" class="form-control" min="0" value="0" readonly>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_taxes[]" class="form-control" min="0" value="0" readonly>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <input type="number" name="other_invoice_nets[]" class="form-control" min="0" value="0" readonly>
                            <span class="price_with_grouping text-primary"></span>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `);
                });
            }
        });


        $('.description').keydown(function (e) {
            if (e.key === 'Enter' && e.shiftKey) {
                e.preventDefault();
                const cursorPos = this.selectionStart;
                const value = $(this).val();
                $(this).val(value.substring(0, cursorPos) + "\n" + value.substring(cursorPos));
                this.selectionStart = this.selectionEnd = cursorPos + 1;
            }
        });


        function updatePrice() {
            totalTotalInvoice = 0;

            $('#other_products_table input[name="other_invoice_nets[]"]').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    totalTotalInvoice += value;
                }
            });

            $('.total_invoice').val(totalTotalInvoice).text(formatNumber(totalTotalInvoice));

        }

        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function updateTableData(index, res) {
            // For total price
            var sibling = $($('#other_products_table input[name="other_total_prices[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.total_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // For discount amount
            sibling = $($('#other_products_table input[name="other_discount_amounts[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.discount_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // For extra amount
            sibling = $($('#other_products_table input[name="other_extra_amounts[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.extra_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // For total price with off
            sibling = $($('#other_products_table input[name="other_total_prices_with_off[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.total_price_with_off.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // For tax amount
            sibling = $($('#other_products_table input[name="other_taxes[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.tax.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // For invoice net
            sibling = $($('#other_products_table input[name="other_invoice_nets[]"]')[index]).siblings()[0];
            if (sibling) {
                sibling.innerText = res.data.invoice_net.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        }


    </script>
@endsection
