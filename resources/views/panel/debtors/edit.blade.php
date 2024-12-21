@extends('panel.layouts.master')
@section('title', 'ویرایش بدهکاری')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش بدهکاری</h6>
            </div>
            <form action="{{ route('debtors.update', $debtor->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <!-- فیلد مشتری غیرقابل تغییر -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="customer_id">مشتری</label>
                        <input type="text" class="form-control disabled" value="{{ $debtor->customer->name }}" readonly>
                        <input type="hidden" name="customer_id" value="{{ $debtor->customer_id }}">
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="price">مبلغ بدهکاری<span class="text-danger">*</span></label>
                        <input type="text" name="price" class="form-control disabled" id="price" value="{{ old('price', $debtor->price) }}"
                               placeholder="45,000,000" readonly>
                        <div id="formatted-price" class="mt-2 text-muted"></div> <!-- نمایش قیمت فرمت شده -->
                        @error('price')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- فیلد وضعیت -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="status">وضعیت<span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                            <option disabled>انتخاب کنید</option>
                            @foreach(\App\Models\Debtor::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ old('status', $debtor->status) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- فیلد توضیحات -->
                    <div class="col-12 mb-3">
                        <label for="description">توضیحات </label>
                        <textarea name="description" class="form-control">{{ old('description', $debtor->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- فیلد آپلود تصویر رسید -->
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="recipe">تصویر رسید</label>
                        <input type="file" name="recipe" class="form-control" id="recipe">
                        @if($debtor->receipt_image)
                            <div class="mt-2">
                                <strong>تصویر فعلی:</strong><br>
                                <img src="{{ asset('storage/' . $debtor->receipt_image) }}" alt="Receipt Image" width="100">
                            </div>
                        @endif
                        @error('recipe')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ویرایش بدهکار</button>
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

                // فرمت کردن عدد به صورت سه رقمی
                if (value.length > 0) {
                    value = parseInt(value).toLocaleString('fa-IR'); // فرمت فارسی (ریال)
                }

                // اعمال مقدار جدید به فیلد ورودی
                e.target.value = value;

                // نمایش قیمت فرمت شده زیر ورودی
                formattedPrice.textContent = `مبلغ فرمت شده: ${value} ریال`;
            });
        });
    </script>
@endsection
