@extends('panel.layouts.master')
@section('title', 'ثبت لیست قیمت')
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>ثبت لیست قیمت</h6>
            </div>
            <form action="{{ route('price-requests.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>قیمت قبلی</th>
                                <th>توضیحات</th>
                                <th>قیمت جدید</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <input type="hidden" name="products[]" value="{{ $product->id }}">
                                        {{ $product->title }}
                                    </td>
                                    <td><input type="text" class="form-control" name="old_prices[]" value="{{ $product->market_price }}" readonly></td>
                                    <td><input type="text" class="form-control" name="description[]" placeholder="توضیحات"></td>
                                    <td>
                                        <!-- اگر قیمت جدید وارد نشده، قیمت قبلی را به عنوان پیش‌فرض قرار دهیم -->
                                        <input type="number" class="form-control" name="new_prices[]" value="{{ $product->price }}" placeholder="قیمت جدید">
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
