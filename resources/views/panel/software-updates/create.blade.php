@extends('panel.layouts.master')
@section('title', 'ثبت تغییرات')
@section('styles')
    <style>
        .btn_remove{
            cursor: pointer;
        }
    </style>

    <!-- Tagsinput -->
    <link rel="stylesheet" href="/vendors/tagsinput/bootstrap-tagsinput.css" type="text/css">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت تغییرات</h6>
            </div>
            <form action="{{ route('software-updates.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="version_number">شماره نسخه<span class="text-danger">*</span></label>
                        <input type="text" name="version_number" class="form-control" id="version_number" value="{{ old('version_number') }}" placeholder="1.0.0">
                        @error('version_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="release_date">تاریخ انتشار<span class="text-danger">*</span></label>
                        <input type="text" name="release_date" class="form-control date-picker-shamsi-list" id="release_date" value="{{ old('release_date') }}">
                        @error('release_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">تغییرات<span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control tagsinput" id="description" value="{{ old('description') }}">
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
    <!-- Tagsinput -->
    <script src="/vendors/tagsinput/bootstrap-tagsinput.js"></script>
    <script src="/assets/js/examples/tagsinput.js"></script>
@endsection
