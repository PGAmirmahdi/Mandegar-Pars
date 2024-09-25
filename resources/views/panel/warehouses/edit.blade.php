@extends('panel.layouts.master')
@section('title', 'ویرایش انبار')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش انبار</h6>
            </div>
            <form action="{{ route('warehouses.update', $warehouse->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">ویرایش<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $warehouse->name }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

