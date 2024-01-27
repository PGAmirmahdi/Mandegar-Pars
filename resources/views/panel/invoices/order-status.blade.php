@extends('panel.layouts.master')
@section('title', 'وضعیت سفارشات')
@section('styles')
    <style>
        .item img{
            width: 50px !important;
        }
        .item .row{
            margin: 2rem 0 2rem 0;
            text-align: center;
        }
        .inactive{
            filter: grayscale(100%) !important;
        }
        .flip-x{
            transform: scaleX(-1);
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وضعیت سفارشات</h6>
            </div>
            <div class="orders">
                @foreach($orders as $order)
                    @php
                        $processing = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->where('status','processing')->first();
                        $out = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->where('status','out')->first();
                        $exit_door = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->where('status','exit_door')->first();
                        $sending = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->where('status','sending')->first();
                        $delivered = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->where('status','delivered')->first();

                        $current_status = \App\Models\OrderStatus::where('invoice_id', $order->invoice_id)->orderByDesc('order')->first()->status;
                    @endphp
                    <div class="item rounded shadow p-4 mt-4">
                        <div class="d-flex justify-content-between">
                            <h5>{{ $order->invoice->customer->name }}</h5>
                            <div class="form-group">
                                <select class="form-control change_status" data-invoice_id="{{ $order->invoice_id }}">
                                    @foreach(\App\Models\OrderStatus::STATUS as $key => $status)
                                        <option value="{{ $key }}" {{ $key == $current_status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img src="{{ asset('assets/media/image/order/register.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ verta($order->created_at)->format('H:i - Y/m/d') }}">
                                <small class="d-block">ثبت سفارش</small>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img class="{{ $processing ? '' : 'inactive' }}" src="{{ asset('assets/media/image/order/processing.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $processing ? verta($processing->created_at)->format('H:i - Y/m/d') : '' }}">
                                <small class="d-block">آماده سازی سفارش</small>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img class="{{ $out ? '' : 'inactive' }}" src="{{ asset('assets/media/image/order/out.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $out ? verta($out->created_at)->format('H:i - Y/m/d') : '' }}">
                                <small class="d-block">خروج از انبار</small>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img class="{{ $exit_door ? '' : 'inactive' }}" src="{{ asset('assets/media/image/order/exit_door.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $exit_door ? verta($exit_door->created_at)->format('H:i - Y/m/d') : '' }}">
                                <small class="d-block">درب خروج</small>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img class="{{ $sending ? '' : 'inactive' }} flip-x" src="{{ asset('assets/media/image/order/sending.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $sending ? verta($sending->created_at)->format('H:i - Y/m/d') : '' }}">
                                <small class="d-block">درحال ارسال</small>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 my-2">
                                <img class="{{ $delivered ? '' : 'inactive' }} flip-x" src="{{ asset('assets/media/image/order/delivered.png') }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $delivered ? verta($delivered->created_at)->format('H:i - Y/m/d') : '' }}">
                                <small class="d-block">تحویل به مشتری</small>
                            </div>
                        </div>
                    </div>
                    <hr style="border: 1px dashed">
                @endforeach
            </div>
            <div class="d-flex justify-content-center">{{ $orders->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('.change_status').on('change', function () {
                let invoice_id = $(this).data('invoice_id');
                let status = $(this).val();

                $.ajax({
                    type: 'post',
                    url: '/panel/orders-status',
                    data: {
                        invoice_id,
                        status
                    },
                    success: function (res) {
                        console.log(res.data)
                    }
                })
            })
        })
    </script>
@endsection


