@extends('panel.layouts.master')
@section('title', 'تایید درخواست')
@section('styles')
    <style>
        /*table tbody tr td input {
            text-align: center;
            width: fit-content !important;
        }*/
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>تایید درخواست</h6>
            </div>
            <form action="{{ route('sale_price_requests.actionStore') }}" method="post">
                @csrf
                <input type="hidden" value="{{$sale_price_request->id}}" name="sale_id">
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="col-12 row mb-4">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="customer">نام سازمان/فروشگاه:</label>
                                <input type="text" value="{{$sale_price_request->customer->name}}"
                                       class="readonly form-control" readonly>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="payment_type">نوع پرداختی</label>
                                <select class="form-control" name="payment_type_display" id="payment_type_display"
                                        disabled>
                                    @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                        <option
                                            value="{{ $key }}" {{ old('payment_type', $sale_price_request->payment_type ?? '') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="payment_type"
                                       value="{{ old('payment_type', $sale_price_request->payment_type ?? '') }}">
                                @error('payment_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($sale_price_request->type == 'setad_sale')
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="date">مهلت پرداخت</label>
                                    <input type="text" class="form-control readonly" readonly
                                           value="{{$sale_price_request->date . ' - ' . $sale_price_request->hour}}">
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="need_no">شماره نیاز</label>
                                    <input type="text" class="form-control readonly" readonly
                                           value="{{$sale_price_request->need_no}}">
                                </div>
                            @endif
                            @if($sale_price_request->type !== 'setad_sale')
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="shipping_cost">هزینه ارسال(ریال)</label>
                                    <input type="text" name="shipping_cost" class="form-control" autocomplete="off"
                                           value="{{$sale_price_request->shipping_cost}}" id="shipping_cost">
                                    <div id="shipping_cost_formatted" style="margin-top: 5px; font-weight: bold;"></div>
                                </div>
                            @endif
                        </div>
                        <table id="products_table" class="table table-striped table-bordered text-center">
                            <thead>
                            <tr>
                                <th>عنوان کالا</th>
                                <th>مدل</th>
                                <th>دسته‌بندی</th>
                                <th>تعداد</th>
                                {{-- <th>قیمت پیشنهادی سیستم</th> --}}
                                <th>قیمت پیشنهادی کارشناس فروش(ریال)</th>
                                <th>قیمت نهایی(ریال)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($sale_price_request->products) as $index => $item)
                                <tr>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_name[{{ $index }}]" value="{{ $item->product_name }}"
                                               readonly autocomplete="off">
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_model[{{ $index }}]" value="{{ $item->product_model }}"
                                               readonly autocomplete="off">
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="category_name[{{ $index }}]" value="{{ $item->category_name }}"
                                               readonly autocomplete="off">
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="number" name="count[{{ $index }}]"
                                               value="{{ $item->count }}" readonly autocomplete="off">
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="product_price[{{ $index }}]"
                                               value="{{ isset($item->product_price) ? number_format($item->product_price) : "بدون قیمت" }}"
                                               readonly autocomplete="off">
                                    </td>
                                    <td>
                                        <input class="form-control final_price_input" type="number"
                                               name="final_price[{{ $index }}]"
                                               value="{{$item->product_price}}" autocomplete="off">
                                        <span class="price-display">{{ isset($item->product_price) ? number_format($item->product_price) : '0' }} ریال </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                    <?php
                                    // محاسبه اولیه قیمت کارشناس (برای نمایش ثابت)
                                    $total_expert_price = 0;
                                    foreach(json_decode($sale_price_request->products) as $item2) {
                                        $total_expert_price += isset($item2->product_price) ? $item2->product_price * $item2->count : 0;
                                    }
                                    $total_expert_price += $sale_price_request->shipping_cost;
                                    ?>
                                <td class="text-center font-weight-bold" colspan="1">
                                    مجموع قیمت کارشناس(ریال):
                                </td>
                                <td class="text-center font-weight-bold" colspan="2">
                                    {{ number_format($total_expert_price) }}
                                </td>
                                <td class="text-center font-weight-bold price-display" colspan="1">
                                    مجموع قیمت مدیر(ریال):
                                </td>
                                <td class="text-center font-weight-bold" colspan="2" id="manager_total_price">
                                    @php
                                        // مقدار اولیه (در صورت وجود مقدار نهایی در دیتابیس)
                                        $total_manager_price = 0;
                                        foreach(json_decode($sale_price_request->products) as $item2) {
                                            if(isset($item2->final_price) && $item2->final_price !== null) {
                                                $total_manager_price += $item2->final_price * $item2->count;
                                            }
                                        }
                                        $total_manager_price += $sale_price_request->shipping_cost;
                                    @endphp
                                    {{ $total_manager_price > $sale_price_request->shipping_cost ? number_format($total_manager_price) : 'ثبت نشده' }}
                                </td>
                            </tr>
                            </tfoot>
                            <input type="hidden" name="price" id="final_price_input" value="">
                        </table>
                    </div>
                    <div class="col-12 row mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description"
                                      class="description form-control"
                                      rows="10">{{ $sale_price_request->description }}</textarea>
                            @error('description')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                            <span class="text-info fst-italic">خط بعد Shift + Enter</span>
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
        $(document).on('input', '#shipping_cost', function () {
            let value = $(this).val().replace(/,/g, ''); // حذف کاماهای موجود
            if (!isNaN(value) && value.trim() !== '') {
                let formattedValue = new Intl.NumberFormat('fa-IR').format(value);
                $('#shipping_cost_formatted').text(formattedValue + ' ریال ');
            } else {
                $('#shipping_cost_formatted').text('');
            }
        });

        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);
                let dots = 0;

                // غیرفعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال');

                // ایجاد افکت چشمک‌زن و تغییر نقطه‌ها
                let interval = setInterval(() => {
                    dots = (dots + 1) % 4; // مقدار 0 تا 3
                    let text = 'در حال ارسال' + '.'.repeat(dots);
                    button.text(text).fadeOut(3000).fadeIn(3000); // افکت چشمک زدن
                }, 6000);

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();

                // متوقف کردن افکت بعد از ارسال (اختیاری، چون صفحه معمولاً رفرش می‌شود)
                setTimeout(() => clearInterval(interval), 10000);
            });
        });

        $(document).on('keyup', '.final_price_input', function () {
            const inputValue = $(this).val().replace(/,/g, ''); // حذف کاماها
            if (!isNaN(inputValue)) { // بررسی معتبر بودن مقدار ورودی
                const formattedValue = addCommas(inputValue); // فرمت سه‌رقم، سه‌رقم
                $(this).next('.price-display').text(formattedValue + ' ریال ');
            } else {
                $(this).next('.price-display').text(''); // پاک کردن مقدار در صورت نامعتبر بودن
            }
        });

        // تابع افزودن کاما
        function addCommas(nStr) {
            nStr += '';
            const x = nStr.split('.');
            let x1 = x[0];
            const x2 = x.length > 1 ? '.' + x[1] : '';
            const rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        $(document).on('input', '.final_price_input', function () {
            let value = $(this).val().replace(/,/g, '');
            if (!isNaN(value) && value.trim() !== '') {
                let formattedValue = new Intl.NumberFormat('fa-IR').format(value);
                // اصلاح کلاس از formatted-price به price-display
                $(this).next('.price-display').text(formattedValue + ' ریال ');
            } else {
                $(this).next('.price-display').text('');
            }
            calculateTotalPrice();
        });

        function calculateTotalPrice() {
            let total = 0;
            $('#products_table tbody tr').each(function () {
                let count = parseFloat($(this).find('input[name^="count"]').val()) || 0;
                let price = parseFloat($(this).find('input[name^="final_price"]').val().replace(/,/g, '')) || 0;
                total += count * price;
            });

            // اضافه کردن هزینه ارسال
            let shippingCost = parseFloat($('#shipping_cost').val().replace(/,/g, '')) || 0;
            total += shippingCost;

            // نمایش مجموع در سلول جدول و مقداردهی به اینپوت هیدن
            $('#manager_total_price').text(new Intl.NumberFormat('fa-IR').format(total));
            $('#final_price_input').val(total);
        }

        $(document).on('input', 'input[name^="final_price"], #shipping_cost', function () {
            calculateTotalPrice();
        });

        $(document).ready(function () {
            calculateTotalPrice();
        });
    </script>
@endsection
