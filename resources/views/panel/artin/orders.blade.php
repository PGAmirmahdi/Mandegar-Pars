@extends('panel.layouts.master')
@section('title', 'سفارشات سایت')
@php
    use Hekmatinasser\Verta\Verta;
@endphp
@section('styles')

@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">سفارشات سایت</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead>
                                <tr class="text-muted small">
                                    <th>شماره سفارش</th>
                                    <th>استان</th>
                                    <th>نام مشتری</th>
                                    <th>مبلغ سفارش</th>
                                    <th>وضعیت سفارش</th>
                                    <th class="text-right">تاریخ سفارش</th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                @foreach ($data['orders']['orders_details'] as $order)
                                    <tr>
                                        <td>{{ $order['order_id'] }}</td>
                                        <td>{{ $order['order_province'] }}</td>
                                        <td>{{ $order['customer_name'] }}</td>
                                        <td>{{ number_format($order['order_total']) }} تومان</td>
                                        <td>
                                            @if($order['order_status'] == 'completed')
                                                <span class="badge badge-success">تکمیل شده</span>
                                            @elseif($order['order_status'] == 'pws-post')
                                                <span class="badge badge-info">تحویل پست شده</span>
                                            @elseif($order['order_status'] == 'processing')
                                                <span class="badge badge-facebook">درحال انجام</span>
                                            @elseif($order['order_status'] == 'pending')
                                                <span class="badge badge-warning">منتظر پرداخت</span>
                                            @elseif($order['order_status'] == 'cancelled')
                                                <span class="badge badge-danger">لغو شده</span>
                                            @else
                                                <span class="badge badge-secondary">نامشخص</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ verta($order['order_date'])->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
