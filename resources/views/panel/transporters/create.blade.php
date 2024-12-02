@extends('panel.layouts.master')
@section('title', 'ایجاد حمل و نقل کننده')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد حمل و نقل کننده</h6>
            </div>
            <form action="{{ route('transporters.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-12 mb-3">
                        <label for="name">نام<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-12 mb-3">
                        <label for="phone">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-12 mb-3">
                        <label for="address">آدرس<span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" id="address">{{ old('address') }}</textarea>
                        @error('address')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
