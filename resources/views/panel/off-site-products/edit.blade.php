@extends('panel.layouts.master')
@section('title', 'ویرایش محصول')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش محصول</h6>
            </div>
            <form action="{{ route('off-site-products.update', $offSiteProduct->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="website" value="{{ $offSiteProduct->website }}">
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ $offSiteProduct->title }}">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    @if($offSiteProduct->website == 'torob' || $offSiteProduct->website == 'emalls')
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                            <label for="url">لینک صفحه (URL)<span class="text-danger">*</span></label>
                            <input type="url" name="url" class="form-control" id="url" value="{{ $offSiteProduct->url }}">
                            @error('url')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif($offSiteProduct->website == 'digikala')
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                            <label for="code">کد کالا<span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" id="code" value="{{ str_replace('/','',substr($offSiteProduct->url, 36)) }}">
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

