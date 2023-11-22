@extends('panel.layouts.master')
@section('title', 'ثبت مشتری')
@section('styles')
    <!-- Tagsinput -->
    <link rel="stylesheet" href="/vendors/tagsinput/bootstrap-tagsinput.css" type="text/css">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت مشتری</h6>
            </div>
            <form action="{{ route('foreign-customers.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="website">وبسایت</label>
                        <input type="url" name="website" class="form-control" id="website" value="{{ old('website') }}">
                        @error('website')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="phone">شماره واتساپ</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="email">ایمیل</label>
                        <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="country">کشور</label>
                        <select name="country" class="js-example-basic-single select2-hidden-accessible">
                            <option value="null">بدون انتخاب...</option>
                            @foreach(\App\Models\Country::pluck('fa_name') as $country)
                                <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="status">وضعیت <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="status">
                            @foreach(\App\Models\ForeignCustomer::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="products">
                            <i class="fa fa-question-circle text-info" data-toggle="tooltip" data-placement="bottom" data-original-title="پس از تایپ نام محصول Enter را بزنید" style="font-size: large"></i>
                            محصولات
                        </label>
                        <input type="text" name="products" class="form-control tagsinput">
                        @error('products')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="docs">فایل های پیوست</label>
                        <input type="file" name="docs[]" class="form-control" id="docs" multiple>
                        @error('docs')
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

    <script type="text/javascript">
        var domains = {!! json_encode(\App\Models\Country::pluck('fa_name','domain')) !!}
        $(document).ready(function () {
            $('#website').on('keyup', function () {
                let str_split = this.value.split('.');
                let domain = '.'+str_split[str_split.length - 1];
                let country = domains[domain];
                $("select[name='country']").val(country).trigger('change');
            })
        })
    </script>
@endsection
