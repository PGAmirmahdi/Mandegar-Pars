@extends('panel.layouts.master')
@section('title', 'مشاهده قیمت')
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
            width: fit-content !important;
        }
        .custom-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .custom-checkbox input[type="checkbox"] {
            display: none;
        }
        .custom-checkbox label {
            position: relative;
            cursor: pointer;
            padding-left: 25px;
            user-select: none;
        }
        .custom-checkbox label:before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid #007bff;
            border-radius: 3px;
            background-color: #fff;
        }
        .custom-checkbox input[type="checkbox"]:checked + label:before {
            background-color: #007bff;
            border-color: #007bff;
        }
        .custom-checkbox input[type="checkbox"]:checked + label:after {
            content: "";
            position: absolute;
            left: 5px;
            top: 10%;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
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
                            <th>شامل ارزش افزوده</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td>{{ $item['product_name'] }}</td> <!-- نام کالا -->
                                <td>{{ $item['product_model'] }}</td> <!-- مدل کالا -->
                                <td>{{ $item['category_name'] }}</td> <!-- دسته‌بندی کالا -->
                                <td>{{ isset($item['price']) ? number_format($item['price']) . " ریال " : '---' }}</td> <!-- قیمت کالا -->
                                <td class="custom-checkbox">
                                    <input type="checkbox" disabled {{ isset($item['vat_included']) && $item['vat_included'] ? 'checked' : '' }} id="vat-{{ $index }}">
                                    <label for="vat-{{ $index }}"></label>
                                </td>
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
