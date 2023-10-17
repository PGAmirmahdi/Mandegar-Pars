@extends('panel.layouts.master')
@section('title', 'ایجاد پرینتر')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد پرینتر</h6>
            </div>
            <form action="{{ route('printers.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="printer_name">نام پرینتر<span class="text-danger">*</span></label>
                        <input type="text" name="printer_name" class="form-control" id="printer_name" value="{{ old('printer_name') }}" placeholder="HP">
                        @error('printer_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="printer_model">مدل پرینتر <span class="text-danger">*</span></label>
                        <input type="text" name="printer_model" class="form-control" id="printer_model" value="{{ old('printer_model') }}" placeholder="laserJet 1005">
                        @error('printer_model')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

