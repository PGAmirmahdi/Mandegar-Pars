@extends('panel.layouts.master')
@section('title', 'ویرایش تأمین‌کننده')
@section('styles')
    <style>
        .social_sec{
            font-size: large !important;
        }
        .social_sec a{
            margin: 0 10px !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش تأمین‌کننده</h6>
            </div>
            <form action="{{ route('suppliers.update', $supplier->id) }}" method="post">
                @csrf
                @method('PATCH')
                <input type="hidden" name="url" value="{{ $url }}">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام سازمان/شرکت <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $supplier->name }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="economical_number">شماره اقتصادی</label>
                        <input type="text" name="economical_number" class="form-control" id="economical_number" value="{{ $supplier->economical_number }}">
                        @error('economical_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="national_number">شماره ثبت/ملی<span class="text-danger">*</span></label>
                        <input type="text" name="national_number" class="form-control" id="national_number" value="{{ $supplier->national_number }}">
                        @error('national_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="postal_code">کد پستی<span class="text-danger">*</span></label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code" value="{{ $supplier->postal_code }}">
                        @error('postal_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="province">استان <span class="text-danger">*</span></label>
                        <select name="province" id="province" class="js-example-basic-single select2-hidden-accessible" data-select2-id="4" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\Province::all() as $province)
                                <option value="{{ $province->name }}" {{ old('province', $supplier->province) == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="city">شهر<span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" id="city" value="{{ $supplier->city }}">
                        @error('city')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="supplier_type">نوع<span class="text-danger">*</span></label>
                        <select class="form-control" name="supplier_type" id="supplier_type">
                            @foreach(\App\Models\Supplier::TYPE as $key => $value)
                                <option value="{{ $key }}" {{ $supplier->supplier_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('supplier_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="categories">زمینه فعالیت<span class="text-danger">*</span></label>
                        <select name="category[]" id="category" class="js-example-basic-single select2-hidden-accessible" multiple="" data-select2-id="6" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}" {{ in_array($cat->id, old('category', $supplier->category ?: [])) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone1">شماره تماس 1<span class="text-danger">*</span></label>
                        <input type="text" name="phone1" class="form-control" id="phone1" value="{{ $supplier->phone1 }}">
                        <div class="social_sec">
                            <a href="https://t.me/+98{{ $supplier->phone1 }}" target="_blank">
                                <i class="fa-brands fa-telegram text-info"></i>
                            </a>
                            <a href="https://wa.me/+98{{ $supplier->phone1 }}" target="_blank">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                            </a>
                            <a href="tel:{{ $supplier->phone1 }}">
                                <i class="fa fa-phone-square text-success"></i>
                            </a>
                        </div>
                        @error('phone1')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone2">شماره تماس 2</label>
                        <input type="text" name="phone2" class="form-control" id="phone22" value="{{ $supplier->phone2 }}">
                        <div class="social_sec">
                            <a href="https://t.me/+98{{ $supplier->phone2 }}" target="_blank">
                                <i class="fa-brands fa-telegram text-info"></i>
                            </a>
                            <a href="https://wa.me/+98{{ $supplier->phone2 }}" target="_blank">
                                <i class="fa-brands fa-whatsapp text-success"></i>
                            </a>
                            <a href="tel:{{ $supplier->phone2 }}">
                                <i class="fa fa-phone-square text-success"></i>
                            </a>
                        </div>
                        @error('phone2')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address1">آدرس 1<span class="text-danger">*</span></label>
                        <textarea name="address1" id="address1" class="form-control">{{ $supplier->address1 }}</textarea>
                        @error('address1')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address2">آدرس 2</label>
                        <textarea name="address2" id="address2" class="form-control">{{ $supplier->address2 }}</textarea>
                        @error('address2')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ $supplier->description }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" id="submit_button">ثبت فرم</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);

                // تغییر متن و غیر فعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال...');

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();
            });
        });
    </script>
@endsection
