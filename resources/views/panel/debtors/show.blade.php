@extends('panel.layouts.master')
@section('title', 'جزئیات بدهکاری')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>جزئیات بدهکاری</h6>
            </div>
            <form>
                @csrf
                @method('GET')
                <div class="form-row">
                    <!-- فیلد مشتری غیرقابل تغییر -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer_id">مشتری</label>
                        <input type="text" class="form-control" value="{{ $debtor->customer->name }}" readonly>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer_id">شماره تلفن مشتری</label>
                        <input type="text" class="form-control" value="{{ $debtor->customer->phone1 }}" readonly>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="price">مبلغ بدهکاری</label>
                        <input type="text" name="price" class="form-control" id="price" value="{{ number_format($debtor->price) }}" readonly>
                        <div id="formatted-price" class="mt-2 text-muted"></div> <!-- نمایش قیمت فرمت شده -->
                    </div>

                    <!-- فیلد وضعیت -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="status">وضعیت</label>
                        <input type="text" class="form-control" value="{{ \App\Models\Debtor::STATUS[$debtor->status] }}" readonly>
                    </div>

                    <!-- فیلد توضیحات -->
                    <div class="col-12 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" class="form-control" readonly>{{ $debtor->description }}</textarea>
                    </div>

                    <!-- فیلد تصویر رسید -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receipt_image">تصویر رسید</label>
                        @if($debtor->recipe)
                            <div class="mt-2">
                                <strong>تصویر رسید:</strong><br>
                                <img src="{{ asset('storage/' . $debtor->recipe) }}" alt="Receipt Image" width="100">
                            </div>
                        @else
                            <p>تصویری برای رسید بارگذاری نشده است.</p>
                        @endif
                    </div>
                </div>
                <!-- دکمه بازگشت به لیست بدهکاران -->
                <a href="{{ route('debtors.index') }}" class="btn btn-secondary">بازگشت</a>
            </form>
        </div>
    </div>
@endsection
