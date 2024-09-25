@extends('panel.layouts.master')
@section('title', 'مشتریان')
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
        <h2 class="text-center mt-2">لیست مشتریان</h2>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام حقیقی/حقوقی</th>
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
                    @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->economical_number == 0 || $customer->economical_number == null ? '---' : $customer->economical_number }}</td>
                            <td>{{ $customer->national_number == 0 || $customer->national_number == null ? '---' : $customer->national_number }}</td>
                            <td>{{ $customer->postal_code == 0 || $customer->postal_code == null ? '---' : $customer->postal_code }}</td>
                            <td>{{ $customer->province }}</td>
                            <td>{{ $customer->city }}</td>
                            <td>{{ $customer->phone1 }}</td>
                            <td>{{ $customer->address1 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $customers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


