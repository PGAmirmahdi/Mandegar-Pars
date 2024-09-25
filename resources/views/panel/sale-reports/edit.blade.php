@extends('panel.layouts.master')
@section('title', 'ویرایش گزارش فروش')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش گزارش فروش</h6>
            </div>
            <form action="{{ route('sale-reports.update', $saleReport->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="person_name">نام شخص<span class="text-danger">*</span></label>
                        <input type="text" name="person_name" class="form-control" id="person_name" value="{{ $saleReport->person_name }}">
                        @error('person_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="organ_name">نام سازمان</label>
                        <input type="text" name="organ_name" class="form-control" id="organ_name" value="{{ $saleReport->organ_name }}">
                        @error('organ_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="national_code">کد/شناسه ملی</label>
                        <input type="text" name="national_code" class="form-control" id="national_code" value="{{ $saleReport->national_code }}">
                        @error('national_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice">سفارش</label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="invoice" id="invoice">
                            @foreach($invoices as $invoiceId => $customerName)
                                <option value="{{ $invoiceId }}" {{ $saleReport->invoice_id == $invoiceId ? 'selected' : '' }}> {{ $invoiceId }} - {{ $customerName }}</option>
                            @endforeach
                        </select>
                        @error('invoice')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="payment_type">نوع پرداخت</label>
                        <input type="text" name="payment_type" class="form-control" id="payment_type" value="{{ $saleReport->payment_type }}">
                        @error('payment_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

