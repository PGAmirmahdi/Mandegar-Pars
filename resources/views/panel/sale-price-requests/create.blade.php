@extends('panel.layouts.master')
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
        }
    </style>
@endsection
@section('title', 'ایجاد ' . (in_array(auth()->user()->role->name, [
    'setad_sale', 'internet_sale', 'free_sale',
    'industrial_sale', 'global_sale', 'organization_sale'
])
    ? ' درخواست ' . auth()->user()->role->label
    : 'درخواست های فروش'))
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>ثبت {{ in_array(auth()->user()->role->name, [
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
            <form action="{{ route('sale_price_requests.store') }}" method="post" id="store_form">
                @csrf
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="col-12 row mb-4">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label class="form-label" for="customer">مشتری حقیقی/حقوقی<span
                                        class="text-danger">*</span></label>
                                <select name="customer" id="customer"
                                        class="js-example-basic-single  select2-hidden-accessible"
                                        data-select2-id="1">
                                    <option value="" disabled selected>انتخاب کنید...</option>
                                    @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                        <option
                                            value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="payment_type">نوع پرداختی</label>
                                <select class="form-control" name="payment_type" id="payment_type">
                                    @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                        <option
                                            value="{{ $key }}" {{ old('payment_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('payment_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            @can('setad_sale')
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="date">تاریخ موعد<span class="text-danger">*</span></label>
                                    <input type="text" name="date" autocomplete="off"
                                           class="form-control date-picker-shamsi-list" id="date"
                                           value="{{ old('date') }}">
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
                                        <input type="text" autocomplete="off" name="hour" class="form-control text-left"
                                               value="{{ old('hour') }}" dir="ltr">
                                    </div>
                                    @error('hour')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="need_no">شماره نیاز<span class="text-danger">*</span></label>
                                    <input type="text" name="need_no" autocomplete="off" class="form-control"
                                           id="need_no"
                                           value="{{ old('need_no') }}">
                                    @error('need_no')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endcan
                        </div>
                        <table class="table table-striped table-bordered text-center" id="products_table">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>تعداد</th>
                                <th>قیمت</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(old('products'))
                                @foreach(old('products') as $key => $odlProduct)
                                    <tr>
                                        <td>
                                            <select class="js-example-basic-single" name="products[]" required>
                                                <option value="" disabled selected>انتخاب کنید</option>
                                                @foreach($products as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $odlProduct == $item->id ? 'selected' : '' }}>
                                                        {{ $item->category->slug . ' - ' . $item->title . ' - ' . $item->productModels->slug }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="counts[]" min="1" value="{{ old('counts')[$key] }}" required></td>
                                        <td><input type="number" class="form-control price-input" name="price[]" value="{{ old('price')[$key] }}" required>
                                            <div class="formatted-price" style="margin-top: 5px; font-weight: bold;">{{ old('price')[$key] != 0 ? number_format(old('price')[$key]) : '' }}</div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <select class="js-example-basic-single" name="products[]" required>
                                            <option value="" disabled selected>انتخاب کنید</option>
                                            @foreach($products as $item)
                                                <option
                                                    value="{{ $item->id }}"
                                                    {{ isset($productId) && $item->id == $productId ? 'selected' : '' }}>
                                                    {{ $item->category->slug . ' - ' . $item->title . ' - ' . $item->productModels->slug }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control" name="counts[]" min="1"
                                               required></td>
                                    <td><input type="number" class="form-control price-input" name="price[]"
                                               required>
                                        <div class="formatted-price" style="margin-top: 5px; font-weight: bold;"></div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-floating btn_remove"><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                        @error('products')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-12 row mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description"
                                      class="description form-control"
                                      rows="10">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit" id="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var products = [];
            var products_options_html = '';

            @foreach($products as $product)
                products.push({
                        "id": "{{ $product->id }}",
                        "title": "{{ $product->title }}",
                        "categorySlug": "{{ $product->category->slug }}",
                        "modelSlug": "{{ $product->productModels->slug }}",
                    })
            @endforeach

            $.each(products, function (i, item) {
                products_options_html += `<option value="${item.id}">${item.categorySlug + ' - ' + item.title + ' - ' + item.modelSlug}</option>`
            })

            $('#store_form').on('keydown', function (e){
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            })
            $("#submit").on("submit", function () {
                $(this).prop("disabled", true).text("در حال ارسال...");
            });
            // Format price input and display formatted price below
            $(document).on('input', '.price-input', function () {
                let value = $(this).val().replace(/,/g, ''); // Remove existing commas
                if (!isNaN(value) && value.trim() !== '') {
                    let formattedValue = new Intl.NumberFormat('fa-IR').format(value); // Format with three-digit separation
                    $(this).next('.formatted-price').text(formattedValue); // Update the formatted price display
                } else {
                    $(this).next('.formatted-price').text(''); // Clear the display if input is invalid
                }
            });
            // Add new row
            $(document).on('click', '#btn_add', function () {
                $('#products_table tbody').append(`
                <tr>
                    <td>
                        <select class="js-example-basic-single" name="products[]" required>
                            <option value="" disabled selected>انتخاب کنید</option>
                            ${products_options_html}
                </select>
            </td>
            <td><input type="number" class="form-control" name="counts[]" min="1" value="1" required></td>
            <td>
                <input type="number" class="form-control price-input" name="price[]" value="0" required>
                <div class="formatted-price" style="margin-top: 5px; font-weight: bold;"></div> <!-- نمایش قیمت فرمت‌شده -->
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-floating btn_remove">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
`);

                // Reinitialize select2 after adding new row
                $('.js-example-basic-single').select2();
            });

            // Remove row
            $(document).on('click', '.btn_remove', function () {
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection
