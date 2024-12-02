@php
    use App\Models\Customer;
    use App\Models\Transporter;
    use App\Models\Invoice;
    use App\Models\Transport;
@endphp
@extends('panel.layouts.master')

@section('title', 'ثبت حمل و نقل')

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
    <div class="card">
        <div class="card-body">
            <form action="{{ route('transports.store') }}" method="POST">
                @csrf {{-- توکن CSRF برای امنیت --}}
            <div class="col-12 mb-4 text-center">
                <h4>مشخصات خریدار</h4>
            </div>
            <div class="col-12 row justify-content-around">
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="invoice_id">نام شخص حقیقی/حقوقی <span class="text-danger">*</span></label>
                    <select name="invoice_id" id="invoice_id"
                            class="js-example-basic-single select2-hidden-accessible" data-select2-id="5"
                            tabindex="-2" aria-hidden="true">
                        <option value="" disabled selected>انتخاب کنید</option>
                        @foreach(Invoice::with('customer:id,name')->get(['id', 'customer_id']) as $invoice)
                            <option value="{{ $invoice->id }}">
                                {{ $invoice->customer->name }}
                            </option>
                        @endforeach

                    </select>
                    @error('invoice_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="customer">نام مشتری<span class="text-danger">*</span></label>
                    <input name="customer" id="customer" class="form-control col-12" readonly>
                    @error('customer')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="address">نشانی<span class="text-danger">*</span></label>
                    <input name="address" id="address" class="form-control col-12" readonly>
                    @error('address')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-12 mt-2 text-center">
                <h5>حمل و نقل کننده ها</h5>
            </div>
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-outline-success" type="button" id="btn_add"><i
                            class="fa fa-plus mr-2"></i> افزودن حمل و نقل کننده
                    </button>
                </div>
                <div class="overflow-auto">
                    <table class="table table-bordered table-striped text-center" id="products_table">
                        <thead>
                        <tr>
                            <th>حمل و نقل کننده</th>
                            <th>مبلغ</th>
                            <th>نوع پرداختی</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(old('transporters'))
                            @foreach(old('transporters') as $i => $transporterId)
                                <tr>
                                    <td>
                                        <select class="js-example-basic-single" name="transporters[]" required>
                                            <option value="" disabled selected>انتخاب کنید
                                            </option>
                                            @foreach(Transporter::all(['id','name']) as $item)
                                                <option
                                                    value="{{ $item->id }}" {{ $item->id == $transporterId ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="prices[]" class="form-control" min="0"
                                               value="{{ old('prices')[$i] }}" readonly>
                                        <div id="formatted-price-{{ $i }}" class="formatted-price"></div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="payment_type[]">
                                            @foreach(App\Models\Transport::Payment_Type as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
                <button class="btn btn-primary" type="submit" id="btn_form">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.js-example-basic-single').select2(); // تنظیم select2 برای انتخاب حمل و نقل‌کنندگان

        // رویداد تغییر برای انتخاب خریدار
        $(document).on('change', 'select[name="invoice_id"]', function () {
            let invoice_id = this.value;
            console.log(invoice_id); // برای اطمینان از مقدار انتخابی
            $.ajax({
                url: '/panel/get-invoice-info/' + invoice_id, // آدرس API برای دریافت اطلاعات
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function (res) {
                    if (res.success) {
                        $('#customer').val(res.data.name); // پر کردن نام مشتری
                        $('#address').val(res.data.address); // پر کردن آدرس مشتری
                    } else {
                        $('#customer').val('');
                        $('#address').val('');
                        alert(res.message);
                    }
                },
                error: function (err) {
                    console.log(err);
                    alert('خطا در دریافت اطلاعات');
                }
            });
        });

        // افزودن حمل و نقل کننده جدید
        $('#btn_add').click(function () {
            let rowCount = $('#products_table tbody tr').length;
            let newRow = `<tr>
                <td>
                    <select class="js-example-basic-single" name="transporters[]" required>
                        <option value="" disabled selected>انتخاب کنید</option>
                        @foreach(Transporter::all(['id','name']) as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="prices[]" class="form-control w-100" min="0" required>
        </td>
        <td>
            <select class="form-control w-100" name="payment_type[]">
@foreach(App\Models\Transport::Payment_Type as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
            </select>
        </td>
        <td>
            <button type="button" class="btn-floating btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button>
        </td>
    </tr>`;

            $('#products_table tbody').append(newRow);
            $('.js-example-basic-single').select2();
        });

        // حذف ردیف حمل و نقل
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });
    </script>
@endsection
