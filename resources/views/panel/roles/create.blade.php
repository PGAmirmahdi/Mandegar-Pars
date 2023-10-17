@extends('panel.layouts.master')
@section('title', 'ایجاد نقش')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد نقش</h6>
            </div>
            <form action="{{ route('roles.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="label">نام فارسی<span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" id="label" value="{{ old('label') }}" placeholder="نویسنده">
                        @error('label')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام انگلیسی <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" placeholder="writer">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="permissions">دسترسی ها <span class="text-danger">*</span></label>
                        <select name="permissions[]" id="permissions" class="js-example-basic-single select2-hidden-accessible" multiple="" data-select2-id="4" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\Permission::all() as $permission)
                                <option value="{{ $permission->id }}" {{ old('permissions') ? (in_array($permission->id, old('permissions')) ? 'selected' : '') : '' }}>{{ $permission->label }}</option>
                            @endforeach
                        </select>
                        @error('permissions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

