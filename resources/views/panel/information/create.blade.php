@extends('panel.layouts.master')
@section('title', 'ایجاد اطلاعات')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد اطلاعات</h6>
            </div>
            <form action="{{ route('baseinfo.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="type">دسته بندی<span class="text-danger">*</span></label>
                        @php use App\Models\Baseinfo; @endphp
                        <select class="form-control" name="type">
                            @foreach(Baseinfo::TYPE as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="info">اطلاعات<span class="text-danger">*</span></label>
                        <input type="text" name="info" class="form-control" id="info" value="{{ old('info') }}">
                        @error('info')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="access">دسترسی<span class="text-danger">*</span></label>
                        <select class="form-control" name="access">
                            @foreach(Baseinfo::ACCESS as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('access')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
{{--                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">--}}
{{--                        <label for="file">آپلود فایل</label>--}}
{{--                        <input type="file" name="file" id="file-input">--}}
{{--                        @error('file')--}}
{{--                        <div class="invalid-feedback d-block">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
@endsection

