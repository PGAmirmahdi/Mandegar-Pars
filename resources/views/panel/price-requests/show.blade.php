@extends('panel.layouts.master')
@section('title', 'مشاهده قیمت')
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
                <h6>مشاهده قیمت</h6>
                <h4>تاریخ بارگذاری: {{ verta($priceRequest->created_at)->format('H:i - Y/m/d') }}</h4>
            </div>
            <div class="form-row">
                <div class="col-12 mb-3">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="bg-primary">
                        <tr>
                            <th>عنوان کالا</th>
                            <th>مدل</th> <!-- اضافه کردن ستون مدل -->
                            <th>دسته‌بندی</th> <!-- اضافه کردن ستون دسته‌بندی -->
                            <th>قیمت (تومان)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td>{{ $item['product_name'] }}</td> <!-- نام کالا -->
                                <td>{{ $item['product_model'] }}</td> <!-- مدل کالا -->
                                <td>{{ $item['category_name'] }}</td> <!-- دسته‌بندی کالا -->
                                <td>{{ isset($item['market_price']) ? number_format($item['market_price']) . " ریال " : '---' }}</td> <!-- قیمت کالا -->
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
