@extends('panel.layouts.master')
@section('title', 'مشاهده سفارش خرید')
@section('styles')
    <style>
        table tbody tr td {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title mb-4">
                <h6>مشاهده سفارش خرید</h6>
            </div>
            <div class="form-row">
                <div class="col-12 mb-3">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="bg-primary">
                        <tr>
                            <th>دسته بندی کالا</th>
                            <th>عنوان کالا</th>
                            <th>شرح کالا</th>
                            <th>تعداد</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    {{ $item['product']->category->name ?? 'بدون دسته‌بندی' }}
                                </td>
                                <td>
                                    {{ $item['product']->title ?? 'نامشخص' }}
                                </td>
                                <td>
                                    {{ $item['product']->productModels->slug ?? 'بدون مدل' }}
                                </td>
                                <td>{{ $item['count'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea id="description" class="form-control" rows="5" readonly>{{ $buyOrder->description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
