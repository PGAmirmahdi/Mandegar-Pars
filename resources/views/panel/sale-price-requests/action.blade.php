@extends('panel.layouts.master')
@section('title', 'تایید درخواست')
@section('styles')
    <style>
        /*table tbody tr td input {*/
        /*    text-align: center;*/
        /*    width: fit-content !important;*/
        /*}*/
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
                            @can('Organ')
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
                            @endcan
                        </div>
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                            <tr>
                                <th>عنوان کالا</th>
                                <th>مدل</th>
                                <th>دسته‌بندی</th>
                                <th>تعداد</th>
                                {{--                                <th>قیمت پیشنهادی سیستم</th>--}}
                                <th>قیمت پیشنهادی کارشناس فروش(ریال)</th>
                                <th>قیمت نهایی(ریال)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($sale_price_request->products) as $index => $item)
                                {{--                                @dd(isset($item->price))--}}
                                <tr>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_name[{{ $index }}]" value="{{ $item->product_name }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_model[{{ $index }}]" value="{{ $item->product_model }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="category_name[{{ $index }}]" value="{{ $item->category_name }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="number" name="count[{{ $index }}]"
                                               value="{{ $item->count }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="price[{{ $index }}]"
                                               value="{{ isset($item->price) ? number_format($item->price) : "بدون قیمت" }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control price-input" type="number"
                                               name="final_price[{{ $index }}]"
                                               value="{{ isset($item->final_price) ? number_format($item->final_price) : 0 }}">
                                        <span class="price-display">{{ isset($item->final_price) ? number_format($item->final_price) : '0' }} ریال </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
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
                <button class="btn btn-primary mt-5" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('keyup', '.price-input', function () {
            const inputValue = $(this).val().replace(/,/g, ''); // حذف کاماها
            if (!isNaN(inputValue)) { // بررسی معتبر بودن مقدار ورودی
                const formattedValue = addCommas(inputValue); // فرمت سه‌رقم، سه‌رقم
                $(this).next('.price-display').text(formattedValue); // نمایش مقدار فرمت‌شده
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
    </script>
@endsection
