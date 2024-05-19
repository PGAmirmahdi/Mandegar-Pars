@extends('panel.layouts.master')
@section('title', 'ایجاد محصول')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد محصول</h6>
            </div>
            <form action="{{ route('off-site-products.store') }}" method="post">
                @csrf
                <input type="hidden" name="website" value="{{ request()->website }}">
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    @if(request()->website == 'torob' || request()->website == 'emalls')
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                            <label for="url">لینک صفحه (URL)<span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control" id="url" value="{{ old('url') }}">
                            @error('url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('error')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif(request()->website == 'digikala')
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                            <label for="code">کد کالا<span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" id="code" value="{{ old('code') }}">
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div>
                                <img src="{{ asset('/assets/media/image/digikala-code.png') }}" class="w-100 mt-2" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="مثال: کد کالا">
                            </div>
                        </div>
                    @endif
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

