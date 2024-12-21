@extends('panel.layouts.master')
@section('title', 'ایجاد بدهکاری')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد بدهکاری</h6>
            </div>
            <form action="{{ route('debtors.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer_id">مشتری<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="customer_id" id="customer_id"  data-select2-id="1">
                            @foreach(\App\Models\Customer::all() as $customer)
                                <option
                                    value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{$customer->code . ' - ' .  $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="price">مبلغ بدهکاری<span class="text-danger">*</span></label>
                        <input type="text" name="price" class="form-control" id="price" value="{{ old('price') }}"
                               placeholder="45,000,000">
                        <div id="formatted-price" class="mt-2 text-muted"></div> <!-- نمایش قیمت فرمت شده -->
                        @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="status">وضعیت<span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                            <option disabled>انتخاب کنید</option>
                            @foreach(\App\Models\Debtor::STATUS as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-12 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea class="form-text" style="width: 100%;height: 100px"></textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.getElementById('price');
            const formattedPrice = document.getElementById('formatted-price');

            // زمانی که ورودی در فیلد وارد می‌شود
            priceInput.addEventListener('input', function(e) {
                // حذف هرگونه کاراکتر غیر عددی (مثل کاما و نقطه)
                let value = e.target.value.replace(/[^\d]/g, '');

                // نمایش قیمت فرمت شده زیر ورودی
                if (value.length > 0) {
                    const formattedValue = Number(value).toLocaleString('fa-IR'); // فرمت فارسی (ریال)
                    formattedPrice.textContent = `مبلغ فرمت شده: ${formattedValue} ریال`;
                } else {
                    formattedPrice.textContent = ''; // اگر مقدار ورودی خالی باشد، فرمت را پاک کنیم
                }
            });
        });
    </script>
@endsection

