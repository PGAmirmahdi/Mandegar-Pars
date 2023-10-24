@extends('panel.layouts.master')
@section('title','لیست قیمت ها')

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت ها - (ریال)</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th>سامانه</th>
                        <th>همکار - تهران</th>
                        <th>همکار - شهرستان</th>
                        <th>تک فروشی</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Models\Product::all(['title','system_price','partner_price_tehran','partner_price_other','single_price']) as $key => $product)
                        <tr>
                            <th>{{ $product->title }}</th>
                            <td>{{ number_format($product->system_price) }}</td>
                            <td>{{ number_format($product->partner_price_tehran) }}</td>
                            <td>{{ number_format($product->partner_price_other) }}</td>
                            <td>{{ number_format($product->single_price) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
