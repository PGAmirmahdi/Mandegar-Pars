@extends('panel.layouts.master')

@section('title', 'عملیات حسابداری حمل و نقل')

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
            <form action="{{ route('transports.finalaccountantupdate.finalaccounting', $transport->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="col-12 mb-4 text-center">
                    <h4>مشخصات فاکتور</h4>
                </div>

                <div class="col-12 row justify-content-around">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice_id">نام شخص حقیقی/حقوقی</label>
                        <input name="invoice_id" id="invoice_id" class="form-control" value="{{ $invoice->customer->name }}" readonly>
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
                                    </td>
                                    <td>
                                        <select class="form-control" name="payment_type[]" disabled>
                                            @foreach(App\Models\Transport::Payment_Type as $key => $value)
                                                <option value="{{ $key }}" {{ $key == $item->payment_type ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <!-- رادیو باتن‌ها باید name یکسان داشته باشند تا فقط یکی از آن‌ها قابل انتخاب باشد -->
                                        <input type="radio" name="selected_item" value="{{ $item->id }}" class="radio-custom" disabled {{ $item->select == 'selected' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- نمایش لینک فایل در صورت وجود -->
                <div class="col-12 mt-4">
                    @if($widgetPath)
                        <a href="{{ $widgetPath->bijak_path ?? '' }}">
                            <img src="{{ $widgetPath->bijak_path ?? '' }}" class="sign" alt="sign" width="75px"
                                 height="75px">
                        </a>
                    @else
                        <p>فایلی برای نمایش وجود ندارد.</p>
                    @endif
                </div>

                <!-- دکمه‌های تایید و عدم تایید -->
                <form action="{{ route('transports.finalaccountantupdate.finalaccounting', $transport->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="status" value="level4">
                    <button class="btn btn-success" type="submit">تایید</button>
                </form>

                <form action="{{ route('transports.finalaccountantupdate.finalaccounting', $transport->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="status" value="level2">
                    <button class="btn btn-danger" type="submit">عدم تایید</button>
                </form>

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
