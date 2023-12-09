@extends('panel.layouts.master')
@section('title', 'پروفایل ربات')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پروفایل ربات</h6>
            </div>
            <form action="{{ route('bot.profile') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="name">نام ربات<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $name }}">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="short_description">توضیحات کوتاه<span class="text-danger">*</span></label>
                        <textarea name="short_description" class="form-control" id="short_description" rows="5">{{ $shortDescription }}</textarea>
                        @error('short_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="description">توضیحات<span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" id="description" rows="5">{{ $description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">بروزرسانی</button>
            </form>
        </div>
    </div>
@endsection

