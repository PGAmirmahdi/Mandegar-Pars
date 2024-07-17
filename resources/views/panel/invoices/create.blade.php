@extends('panel.layouts.master')
@section('title', 'ثبت سفارش')
@section('styles')
    <style>
        #products_table input, #products_table select{
            width: auto;
        }

        #other_products_table input, #other_products_table select{
            width: auto;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-5">
                <div class="w-100">
                    <h6>ثبت سفارش</h6>
                    @can('unofficial-sales')
{{--                        <div class="col-12 mb-4 text-center">--}}
{{--                            <h4>--}}
{{--                                @can('accountants')--}}
{{--                                    نوع فاکتور--}}
{{--                                @else--}}
{{--                                    نوع سفارش--}}
{{--                                @endcan--}}
{{--                            </h4>--}}
{{--                        </div>--}}
{{--                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">--}}
{{--                            <label class="btn btn-outline-primary justify-content-center {{ old('type') == 'official' || old('type') == null ? 'active' : '' }}">--}}
{{--                                <input type="radio" id="type1" name="type" class="custom-control-input" value="official" form="invoice_form" {{ old('type') == 'official' || old('type') == null ? 'checked' : '' }}>رسمی--}}
{{--                            </label>--}}
{{--                            <label class="btn btn-outline-primary justify-content-center {{ old('type') == 'unofficial' ? 'active' : '' }}">--}}
{{--                                <input type="radio" id="type2" name="type" class="custom-control-input" value="unofficial" form="invoice_form" {{ old('type') == 'unofficial' ? 'checked' : '' }}>غیر رسمی--}}
{{--                            </label>--}}
{{--                        </div>--}}
                            <input type="hidden" name="type" value="unofficial" form="invoice_form">
                            <input type="hidden" name="req_for" value="invoice" form="invoice_form">
                    @else
                            <div class="col-12 mb-4 text-center mt-5">
                                <h4>درخواست برای</h4>
                            </div>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary justify-content-center {{ old('req_for') == 'pre-invoice' || old('req_for') == null ? 'active' : '' }}">
                                    <input type="radio" id="req_for1" name="req_for" class="custom-control-input" value="pre-invoice" form="invoice_form" {{ old('req_for') == 'pre-invoice' || old('req_for') == null ? 'checked' : '' }}>پیش فاکتور
                                </label>
                                <label class="btn btn-outline-primary justify-content-center {{ old('req_for') == 'invoice' ? 'active' : '' }}">
                                    <input type="radio" id="req_for2" name="req_for" class="custom-control-input" value="invoice" form="invoice_form" {{ old('req_for') == 'invoice' ? 'checked' : '' }}>فاکتور
                                </label>
                                <label class="btn btn-outline-primary justify-content-center {{ old('req_for') == 'amani-invoice' ? 'active' : '' }}">
                                    <input type="radio" id="req_for2" name="req_for" class="custom-control-input" value="amani-invoice" form="invoice_form" {{ old('req_for') == 'amani-invoice' ? 'checked' : '' }}>فاکتور امانی
                                </label>
                            </div>

                            <input type="hidden" name="type" value="official" form="invoice_form">
                    @endcan
                </div>
            </div>
            <form action="{{ route('invoices.store') }}" method="post" id="invoice_form">
                @csrf
                <div class="form-row mb-4">
                    <div class="col-12 mb-4 text-center {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec">
                        <h4>مشخصات فروشنده</h4>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec mb-3">
                        <label for="seller_name">فروشنده <span class="text-danger">*</span></label>
                        <input type="text" name="seller_name" class="form-control" id="seller_name" value="{{ $seller->name }}" readonly>
                        @error('seller_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec mb-3">
                        <label for="seller_phone">شماره تماس<span class="text-danger">*</span></label>
                        <input type="text" name="seller_phone" class="form-control" id="seller_phone" value="{{ $seller->phone }}" readonly>
                        @error('seller_phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec mb-3">
                        <label for="seller_province">استان <span class="text-danger">*</span></label>
                        <select name="seller_province" id="seller_province" class="form-control" readonly style="pointer-events: none">
                            <option value="{{ $seller->province }}">{{ $seller->province }}</option>
{{--                            @foreach(\App\Models\Province::all() as $province)--}}
{{--                                <option value="{{ $province->name }}" {{ old('seller_province') == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>--}}
{{--                            @endforeach--}}
                        </select>
                        @error('seller_province')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec mb-3">
                        <label for="seller_city">شهر<span class="text-danger">*</span></label>
                        <input type="text" name="seller_city" class="form-control" id="seller_city" value="{{ $seller->city }}" readonly>
                        @error('seller_city')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 {{ old('type') == 'unofficial' ? '' : 'd-none' }} seller_sec mb-3">
                        <label for="seller_address">نشانی<span class="text-danger">*</span></label>
                        <textarea name="seller_address" id="seller_address" class="form-control" readonly>{{ $seller->address }}</textarea>
                        @error('seller_address')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-4 text-center">
                        <h4>مشخصات خریدار</h4>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="buyer_name">نام شخص حقیقی/حقوقی <span class="text-danger">*</span></label>
                        <select name="buyer_name" id="buyer_name" class="js-example-basic-single select2-hidden-accessible" data-select2-id="5" tabindex="-2" aria-hidden="true">
                            <option value="" disabled selected>انتخاب کنید...</option>
                            @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                <option value="{{ $customer->id }}" {{ old('buyer_name') == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                            @endforeach
                        </select>
                        @error('buyer_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="economical_number">شماره اقتصادی @can('system-user')<span class="text-danger">*</span>@endcan</label>
                        <input type="text" name="economical_number" class="form-control" id="economical_number" value="{{ old('economical_number') }}">
                        @error('economical_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="national_number">شماره ثبت/ملی<span class="text-danger">*</span></label>
                        <input type="text" name="national_number" class="form-control" id="national_number" value="{{ old('national_number') }}">
                        @error('national_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="need_no">شماره نیاز</label>
                        <input type="text" name="need_no" class="form-control" id="need_no" value="{{ old('need_no') }}">
                        @error('need_no')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="postal_code">کد پستی<span class="text-danger">*</span></label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code" value="{{ old('postal_code') }}">
                        @error('postal_code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone">شماره تماس<span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="province">استان <span class="text-danger">*</span></label>
                        <select name="province" id="province" class="js-example-basic-single select2-hidden-accessible" data-select2-id="15" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\Province::all() as $province)
                                <option value="{{ $province->name }}" {{ old('province') == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="city">شهر<span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" id="city" value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address">نشانی<span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-4 mt-2 text-center">
                        <hr>
                        <h4>مشخصات کالا یا خدمات مورد معامله</h4>
                    </div>
                    <div class="col-12 mt-2 text-center">
                        <h5>محصولات آرتین</h5>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-between mb-3">
                            <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
                        </div>
                        <div class="overflow-auto">
                            <table class="table table-bordered table-striped text-center" id="products_table">
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
                                    @if(old('products'))
                                        @foreach(old('products') as $i => $productId)
                                            <tr>
                                                <td>
                                                    <select class="js-example-basic-single" name="products[]" required>
                                                        <option value="" disabled selected>..................... انتخاب کنید .....................</option>
                                                        @foreach(\App\Models\Product::all(['id','title','code']) as $item)
                                                            <option value="{{ $item->id }}" {{ $item->id == $productId ? 'selected' : '' }}>{{ $item->code.' - '.$item->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="colors[]" required>
                                                        @foreach(\App\Models\Product::COLORS as $key => $value)
                                                            <option value="{{ $key }}" {{ $key == old('colors')[$i] ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="counts[]" class="form-control" min="1" value="{{ old('counts')[$i] }}" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="units[]">
                                                        <option value="number">عدد</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="prices[]" class="form-control" min="0" value="{{ old('prices')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="total_prices[]" class="form-control" min="0" value="{{ old('total_prices')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="discount_amounts[]" class="form-control" min="0" value="{{ old('discount_amounts')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="extra_amounts[]" class="form-control" min="0" value="{{ old('extra_amounts')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="total_prices_with_off[]" class="form-control" min="0" value="{{ old('total_prices_with_off')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="taxes[]" class="form-control" min="0" value="{{ old('taxes')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="invoice_nets[]" class="form-control" min="0" value="{{ old('invoice_nets')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
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
                            <button class="btn btn-outline-success" type="button" id="btn_other_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
                        </div>
                        <div class="overflow-auto">
                            <table class="table table-bordered table-striped text-center" id="other_products_table">
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
                                    @if(old('other_products'))
                                        @foreach(old('other_products') as $i => $otherProduct)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" name="other_products[]" placeholder="عنوان کالا" value="{{ $otherProduct }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="other_colors[]" placeholder="نام رنگ" value="{{ old('other_colors')[$i] }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_counts[]" class="form-control" min="1" value="{{ old('other_counts')[$i] }}" required>
                                                </td>
                                                <td>
                                                    <select class="form-control" name="other_units[]">
                                                        <option value="number">عدد</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_prices[]" class="form-control" min="0" value="{{ old('other_prices')[$i] }}" required>
                                                    <span class="price_with_grouping text-primary"></span>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_total_prices[]" class="form-control" min="0" value="{{ old('other_total_prices')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_discount_amounts[]" class="form-control" min="0" value="{{ old('other_discount_amounts')[$i] }}" required>
                                                    <span class="price_with_grouping text-primary"></span>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_extra_amounts[]" class="form-control" min="0" value="{{ old('other_extra_amounts')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_total_prices_with_off[]" class="form-control" min="0" value="{{ old('other_total_prices_with_off')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_taxes[]" class="form-control" min="0" value="{{ old('other_taxes')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="other_invoice_nets[]" class="form-control" min="0" value="{{ old('other_invoice_nets')[$i] }}" readonly>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 mb-2 mt-2 text-center">
                        <hr>
                        <h4>تخفیف نهایی</h4>
                    </div>
                    <div class="form-group">
                        <label for="final_discount">مبلغ تخفیف</label>
                        <input type="text" class="form-control" name="final_discount" id="final_discount" value="{{ old('final_discount') ?? 0 }}" required>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="btn_form">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var products = [];
        var colors = [];
        var unofficials = "{{ \Illuminate\Support\Facades\Gate::allows('unofficial-sales') }}";

        var form = document.getElementById('invoice_form');
        form.addEventListener('keypress', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });

        @foreach(\App\Models\Product::all(['id','title','code']) as $product)
        products.push({
            "id": "{{ $product->id }}",
            "title": "{{ $product->title }}",
            "code": "{{ $product->code }}",
        });
        @endforeach
        @foreach(\App\Models\Product::COLORS as $key => $value)
        colors.push({
            "key": "{{ $key }}",
            "value": "{{ $value }}",
        });
        @endforeach

        var products_options_html = '';
        var colors_options_html = '';

        $.each(products, function (i, item) {
            products_options_html += `<option value="${item.id}">${item.code} - ${item.title}</option>`;
        });

        $.each(colors, function (i, item) {
            colors_options_html += `<option value="${item.key}">${item.value}</option>`;
        });

        $(document).ready(function () {
            $('#btn_add').on('click', function () {
                $('#products_table tbody').append(`
                    <tr>
                        <td>
                            <select class="js-example-basic-single" name="products[]" style="width: 300px !important;" required>
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
                            </select>
                        </td>
                        <td>
                            <input type="number" name="prices[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="total_prices[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="discount_amounts[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="extra_amounts[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="total_prices_with_off[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="taxes[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="invoice_nets[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `);
                $('.js-example-basic-single').select2();
            });

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
                        </td>
                        <td>
                            <input type="number" name="other_total_prices_with_off[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="other_taxes[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" name="other_invoice_nets[]" class="form-control" min="0" value="0" readonly>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `);
            });

            $(document).on('click','.btn_remove', function () {
                $(this).parent().parent().remove();
            });

            $(document).on('change', '#products_table select[name="products[]"]', function () {
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                CalcProductInvoice(this);
            });

            $(document).on('keyup', '#products_table input[name="counts[]"]', function (e) {
                if (e.originalEvent.explicitOriginalTarget.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                }
            });

            $(document).on('change', '#products_table input[name="counts[]"]', function () {
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                CalcProductInvoice(this);
            });

            $(document).on('keyup', '#other_products_table input[name="other_counts[]"]', function (e) {
                if (e.originalEvent.explicitOriginalTarget.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                }
            });

            $(document).on('change', '#other_products_table input[name="other_counts[]"]', function () {
                $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                CalcOtherProductInvoice(this);
            });

            $(document).on('keyup', '#other_products_table input[name="other_prices[]"]', function (e) {
                if (e.originalEvent.explicitOriginalTarget.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                }
            });

            $(document).on('change', '#other_products_table input[name="other_prices[]"]', function () {
                CalcOtherProductInvoice(this);
            });

            $(document).on('keyup', '#other_products_table input[name="other_discount_amounts[]"]', function (e) {
                if (e.originalEvent.explicitOriginalTarget.defaultValue != this.value) {
                    $('#btn_form').attr('disabled', 'disabled').text('درحال محاسبه...');
                }
            });

            $(document).on('change', '#other_products_table input[name="other_discount_amounts[]"]', function () {
                CalcOtherProductInvoice(this);
            });

            $(document).on('change', 'select[name="buyer_name"]', function () {
                let customer_id = this.value;

                $.ajax({
                    url: '/panel/get-customer-info/'+customer_id,
                    type: 'post',
                    success: function(res) {
                        $('#economical_number').val(res.data.economical_number);
                        $('#national_number').val(res.data.national_number);
                        $('#postal_code').val(res.data.postal_code);
                        $('#phone').val(res.data.phone1);
                        $('#address').val(res.data.address1);
                        $('#province').val(res.data.province).trigger('change');
                        $('#city').val(res.data.city);
                    }
                });
            });
        });

        function CalcProductInvoice(changeable) {
            var index = $(changeable).parent().parent().index();
            let product_id =  $('#products_table select[name="products[]"]')[index].value;
            let count =  $('#products_table input[name="counts[]"]')[index].value;

            $.ajax({
                url: "{{ route('calcProductsInvoice') }}",
                type: 'post',
                data: {
                    'product_id': product_id,
                    'count': count,
                    'unofficial': unofficials,
                },
                success: function (res) {
                    $('#products_table input[name="prices[]"]')[index].value = parseFloat(res.data.price);
                    $('#products_table input[name="total_prices[]"]')[index].value = parseFloat(res.data.total_price);
                    $('#products_table input[name="discount_amounts[]"]')[index].value = parseFloat(res.data.discount_amount);
                    $('#products_table input[name="extra_amounts[]"]')[index].value = parseFloat(res.data.extra_amount);
                    $('#products_table input[name="total_prices_with_off[]"]')[index].value = parseFloat(res.data.total_price_with_off);
                    $('#products_table input[name="taxes[]"]')[index].value = parseFloat(res.data.tax);
                    $('#products_table input[name="invoice_nets[]"]')[index].value = parseFloat(res.data.invoice_net);

                    $('#btn_form').removeAttr('disabled').text('ثبت فرم');
                },
                error: function (request, status, error) {
                    //
                }
            });
        }

        function CalcOtherProductInvoice(changeable) {
            var index = $(changeable).parent().parent().index();
            let price =  $('#other_products_table input[name="other_prices[]"]')[index].value;
            let count =  $('#other_products_table input[name="other_counts[]"]')[index].value;
            let discount_amount =  $('#other_products_table input[name="other_discount_amounts[]"]')[index].value;

            $.ajax({
                url: "{{ route('calcOtherProductsInvoice') }}",
                type: 'post',
                data: {
                    'price': price,
                    'count': count,
                    'discount_amount': discount_amount,
                    'unofficial': unofficials,
                },
                success: function (res) {
                    let total_price_with_off = parseFloat(res.data.total_price_with_off);
                    if (!isNaN(total_price_with_off)) {
                        $('#other_products_table input[name="other_total_prices_with_off[]"]')[index].value = total_price_with_off;
                    }

                    $('#other_products_table input[name="other_prices[]"]')[index].value = parseFloat(res.data.price);
                    $('#other_products_table input[name="other_total_prices[]"]')[index].value = parseFloat(res.data.total_price);
                    $('#other_products_table input[name="other_discount_amounts[]"]')[index].value = parseFloat(res.data.discount_amount);
                    $('#other_products_table input[name="other_extra_amounts[]"]')[index].value = parseFloat(res.data.extra_amount);
                    $('#other_products_table input[name="other_taxes[]"]')[index].value = parseFloat(res.data.tax);
                    $('#other_products_table input[name="other_invoice_nets[]"]')[index].value = parseFloat(res.data.invoice_net);

                    $('#btn_form').removeAttr('disabled').text('ثبت فرم');
                },
                error: function (request, status, error) {
                    //
                }
            });
        }
    </script>
@endsection


