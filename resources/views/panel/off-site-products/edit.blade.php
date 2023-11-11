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
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="url">لینک ترب (URL)<span class="text-danger">*</span></label>
                        <input type="url" name="url" class="form-control" id="url" value="{{ $offSiteProduct->url }}">
                        @error('url')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

