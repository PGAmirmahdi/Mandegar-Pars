@extends('panel.layouts.master')
@section('title', 'نمایش درخواست ستاد')
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
                <h6>نمایش درخواست ستاد</h6>
                <a href="{{ route('setad_price_requests.index') }}" class="btn btn-secondary">
                    بازگشت
                </a>
            </div>
            <form>
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="col-12 row mb-4">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label class="form-label" for="customer">مشتری حقیقی/حقوقی</label>
                                <select name="customer" id="customer" class="form-control" disabled>
                                    <option value="" disabled selected>انتخاب کنید...</option>
                                    @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                        <option
                                            value="{{ $customer->id }}" {{ $setad_price_request->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->code.' - '.$customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="payment_type">نوع پرداختی</label>
                                <select class="form-control" name="payment_type_display" id="payment_type_display" disabled>
                                    @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                        <option value="{{ $key }}" {{ $setad_price_request->payment_type == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                <label for="date">تاریخ موعد</label>
                                <input type="text" name="date" class="form-control" id="date" value="{{ $setad_price_request->date . ' - ' . $setad_price_request->hour }}" disabled>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                <label for="need_no">شماره نیاز</label>
                                <input type="text" name="need_no" class="form-control" id="need_no" value="{{ $setad_price_request->need_no }}" disabled>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>مدل</th>
                                <th>دسته‌بندی</th>
                                <th>تعداد</th>
                                <th>قیمت پیشنهادی کارشناس فروش</th>
                                <th>قیمت نهایی مدیر</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($setad_price_request->products) as $index => $item)
                                <tr>
                                    <td>
                                        <input class="form-control readonly" type="text" name="product_name[{{ $index }}]" value="{{ $item->product_name }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="product_model[{{ $index }}]" value="{{ $item->product_model }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="category_name[{{ $index }}]" value="{{ $item->category_name }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="count[{{ $index }}]" value="{{ $item->count }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="price[{{ $index }}]" value="{{ isset($item->price) ? number_format($item->price) : "بدون قیمت" }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="final_price[{{ $index }}]" value="{{ isset($item->final_price) ? number_format($item->final_price) : "بدون قیمت" }}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 row mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description" class="form-control" rows="10" disabled>{{ $setad_price_request->description }}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
