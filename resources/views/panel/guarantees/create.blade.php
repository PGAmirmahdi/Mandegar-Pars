@extends('panel.layouts.master')
@section('title', 'ایجاد گارانتی')
@section('styles')
    <style>
        .btn_remove{
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد گارانتی</h6>
            </div>
            <form action="{{ route('guarantees.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="serial_number">شماره سریال<span class="text-danger">*</span></label>
                        <input type="text" name="serial_number" class="form-control" id="serial_number" value="{{ $serial }}" readonly>
                        @error('serial_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="period">مدت گارانتی<span class="text-danger">*</span></label>
                        <select name="period" class="form-control" id="period">
                            @foreach(\App\Models\Guarantee::PERIOD as $key => $value)
                                <option value="{{ $key }}" {{ old('period') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('period')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="status">وضعیت<span class="text-danger">*</span></label>
                        <select name="status" class="form-control" id="status">
                            @foreach(\App\Models\Guarantee::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
