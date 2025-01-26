@extends('panel.layouts.master')

@section('title', 'صفحه انباردار')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/all.css">
    <style>
        #products_table input, #products_table select {
            width: auto;
        }
        .radio-custom {
            margin: 0;
            padding: 0;
        }
    </style>
@endsection

@php
    use App\Models\Transporter;
@endphp

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('transports.storeBijak', $transport->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-12 mb-4 text-center">
                    <h4>مشخصات فاکتور</h4>
                </div>

                <div class="col-12 row justify-content-around">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice_id">سفارش</label>
                        <input name="invoice_id" id="invoice_id" class="form-control" value="{{ $invoice->customer->name }} - {{ $invoice->order ? $invoice->order->code : '' }}" title="{{ $invoice->order ? $invoice->order->code : '' }}" readonly>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer">نام مشتری</label>
                        <input name="customer" id="customer" class="form-control" value="{{ $invoice->customer->name }}" readonly>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address">نشانی</label>
                        <input name="address" id="address" class="form-control" value="{{ $invoice->customer->address1 }}" readonly>
                    </div>
                </div>

                <div class="col-12 mt-2 text-center">
                    <h5>حمل و نقل‌کننده‌ها</h5>
                </div>

                <div class="col-12 mb-3">
                    <div class="overflow-auto">
                        <table class="table table-bordered table-striped text-center" id="products_table">
                            <thead>
                            <tr>
                                <th>حمل و نقل‌کننده</th>
                                <th>مبلغ</th>
                                <th>نوع پرداخت</th>
                                <th>انتخاب</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transportItems as $i => $item)
                                <tr>
                                    <td>
                                        <select class="form-control" name="transporters[]" required disabled>
                                            <option value="" disabled selected>انتخاب کنید</option>
                                            @foreach(Transporter::all(['id', 'name']) as $transporter)
                                                <option value="{{ $transporter->id }}" {{ $transporter->id == $item->transporter_id ? 'selected' : '' }}>
                                                    {{ $transporter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="prices[]" class="form-control" min="0" value="{{ $item->price }}" readonly>
                                        <div id="formatted-price-{{ $i }}" class="formatted-price">{{ number_format($item->price) }}</div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="payment_type[]" disabled>
                                            @foreach(App\Models\Transport::Payment_Type as $key => $value)
                                                <option value="{{ $key }}" {{ $key == $item->payment_type ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="radio" name="selected_item" value="{{ $item->id }}" class="radio-custom" disabled {{ $item->select == 'selected' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label for="bijak">بیجک انباردار</label>
                    <input type="file" name="bijak" id="bijak" class="form-control" required>
                </div>
                <div class="col-12 mt-3">
                    <label for="description">توضیحات</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <button class="btn btn-primary mt-3" type="submit">ثبت</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script>
        // Initialize iCheck for custom radio buttons (with circular style)
        $('input[type="radio"].radio-custom').iCheck({
            radioClass: 'iradio_square-blue',
        });
    </script>
@endsection
