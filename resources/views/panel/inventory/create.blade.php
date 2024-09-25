@extends('panel.layouts.master')
@section('title', 'افزودن کالا')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>افزودن کالا</h6>
            </div>
            <form action="{{ route('inventory.store') }}" method="post">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="title">عنوان کالا <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="code">کد کالا <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" id="code" value="{{ old('code') }}">
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="type">نوع <span class="text-danger">*</span></label>
                        <select class="form-control" name="type" id="type">
                            @foreach(\App\Models\Inventory::TYPE as $key => $value)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="count">موجودی <span class="text-danger">*</span></label>
                        <input type="number" name="count" class="form-control" id="count" value="{{ old('count') }}" min="0">
                        @error('count')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

