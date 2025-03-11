@extends('panel.layouts.master')
@section('title', 'افزودن کالا')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>افزودن کالا</h6>
            </div>
            <form action="{{ route('inventory.store') }}" method="post">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="product_id">انتخاب کالا<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="product_id" id="product_id"  data-select2-id="1">
                            @foreach(\App\Models\Product::where('status', 'approved')->get() as $product)
                                <option
                                    value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->title . ' - ' . ($product->productModels->name  . ' - ' . $product->category->name?? '') }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="count">موجودی <span class="text-danger">*</span></label>
                        <input type="number" name="count" class="form-control" id="count" value="{{ old('count', 0) }}" min="0">
                        @error('count')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
    <script>
        $('#submit_button').on('click', function () {
            let button = $(this);
            let dots = 0;

            // غیرفعال کردن دکمه
            button.prop('disabled', true).text('در حال ارسال');

            // ایجاد افکت چشمک‌زن و تغییر نقطه‌ها
            let interval = setInterval(() => {
                dots = (dots + 1) % 4; // مقدار 0 تا 3
                let text = 'در حال ارسال' + '.'.repeat(dots);
                button.text(text).fadeOut(3000).fadeIn(3000); // افکت چشمک زدن
            }, 6000);

            // ارسال فرم به صورت خودکار
            button.closest('form').submit();

            // متوقف کردن افکت بعد از ارسال (اختیاری، چون صفحه معمولاً رفرش می‌شود)
            setTimeout(() => clearInterval(interval), 10000);
        });
    </script>
@endsection

