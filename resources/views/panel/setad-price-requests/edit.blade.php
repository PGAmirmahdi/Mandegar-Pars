@extends('panel.layouts.master')
@section('title', 'تایید درخواست ستاد')
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
            width: fit-content !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>تایید درخواست ستاد</h6>
            </div>
            <form action="{{ route('setad_price_requests.update', $setadpriceRequest->id) }}" method="post">
                @csrf
                @method('put')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <table>
                            <thead>
                            <tr>
                                <th>عنوان کالا</th>
                                <th>مدل</th>
                                <th>دسته‌بندی</th>
                                <th>تعداد</th>
                                <th>قیمت پیشنهادی سیستم</th>
                                <th>قیمت (تومان)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($setadpriceRequest->products) as $index => $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_model }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->count }}</td>
                                    <td>
                                        <input type="text" name="prices[{{ $index }}]" value="{{ isset($item->price) ? number_format($item->price) : 0 }}">
                                    </td>
                                    <td>
                                        <span>{{ number_format($item->system_price ?? 0) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // نمایش عدد فرمت‌شده زیر هر فیلد
        $(document).on('keyup', 'input.price-input', function () {
            const inputValue = $(this).val().replace(/,/g, ''); // حذف کاماها
            const formattedValue = addCommas(inputValue); // فرمت سه‌رقم، سه‌رقم
            $(this).val(formattedValue); // به‌روزرسانی مقدار فیلد
            $(this).next('.price-display').text(formattedValue); // نمایش مقدار فرمت‌شده
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
