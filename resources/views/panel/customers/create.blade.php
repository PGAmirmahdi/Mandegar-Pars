@extends('panel.layouts.master')
@section('title', 'ایجاد مشتری')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد مشتری</h6>
            </div>
            <form action="{{ route('customers.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام حقیقی/حقوقی <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="type">نوع <span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="type">
                            @foreach(\App\Models\Customer::TYPE as $key => $value)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="economical_number">شماره اقتصادی</label>
                        <input type="text" name="economical_number" class="form-control" id="economical_number" value="{{ old('economical_number') }}">
                        @error('economical_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="national_number">شماره ثبت/ملی<span class="text-danger">*</span></label>
                        <input type="text" name="national_number" class="form-control" id="national_number" value="{{ old('national_number') }}">
                        @error('national_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="postal_code">کد پستی<span class="text-danger">*</span></label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code" value="{{ old('postal_code') }}">
                        @error('postal_code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="province">استان <span class="text-danger">*</span></label>
                        <select name="province" id="province" class="js-example-basic-single select2-hidden-accessible" data-select2-id="4" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\Province::all() as $province)
                                <option value="{{ $province->name }}" {{ old('province') == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                        @error('province')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="city">شهر<span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" id="city" value="{{ old('city') }}">
                        @error('city')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone1">شماره تماس 1 <span class="text-danger">*</span></label>
                        <input type="text" name="phone1" class="form-control" id="phone1" value="{{ old('phone1') }}">
                        @error('phone1')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone2">شماره تماس 2</label>
                        <input type="text" name="phone2" class="form-control" id="phone2" value="{{ old('phone2') }}">
                        @error('phone2')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone3">شماره تماس 3</label>
                        <input type="text" name="phone3" class="form-control" id="phone3" value="{{ old('phone3') }}">
                        @error('phone3')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address1">آدرس 1 <span class="text-danger">*</span></label>
                        <textarea name="address1" id="address1" class="form-control">{{ old('address1') }}</textarea>
                        @error('address1')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address2">آدرس 2 </label>
                        <textarea name="address2" id="address2" class="form-control">{{ old('address2') }}</textarea>

                        @error('address2')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
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
