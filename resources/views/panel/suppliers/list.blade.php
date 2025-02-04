@extends('panel.layouts.master')
@section('title', 'تامین کنندگان')
@php
    $sidebar = false;
    $header = false;
@endphp
@section('styles')
    <style>
        main{
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <h2 class="text-center mt-2">لیست تامین کنندگان</h2>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ایجاد کننده</th>
                        <th>نام سازمان/فروشگاه</th>
                        <th>نوع</th>
                        <th>شماره اقتصادی</th>
                        <th>شماره ثبت/ملی</th>
                        <th>کد پستی</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>آدرس</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($suppliers as $key => $supplier)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $supplier->user->name . ' '  . $supplier->user->family}}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->supplier_type }}</td>
                            <td>{{ $supplier->economical_number == 0 || $supplier->economical_number == null ? '---' : $supplier->economical_number }}</td>
                            <td>{{ $supplier->national_number == 0 || $supplier->national_number == null ? '---' : $supplier->national_number }}</td>
                            <td>{{ $supplier->postal_code == 0 || $supplier->postal_code == null ? '---' : $supplier->postal_code }}</td>
                            <td>{{ $supplier->province }}</td>
                            <td>{{ $supplier->city }}</td>
                            <td>{{ $supplier->phone1 }}</td>
                            <td>{{ $supplier->address1 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $suppliers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


