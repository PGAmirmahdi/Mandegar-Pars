@extends('panel.layouts.master')
@section('title', 'ویرایش پرینتر')
@section('styles')
    <!-- Tagsinput -->
    <link rel="stylesheet" href="/vendors/tagsinput/bootstrap-tagsinput.css" type="text/css">
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش پرینتر</h6>
            </div>
            <form action="{{ route('printers.update', $printer->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام پرینتر<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $printer->name }}">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="brand">برند پرینتر <span class="text-danger">*</span></label>
                        <select type="text" name="brand" class="js-example-basic-single select2-hidden-accessible" id="brand">
                            @foreach(\App\Models\Printer::BRANDS as $brand)
                                <option value="{{ $brand }}" {{ $brand == $printer->brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                        @error('brand')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="cartridges">کارتریج های سازگار<span class="text-danger">*</span></label>
                        <input type="text" name="cartridges" class="form-control tagsinput" id="cartridges" value="{{ $printer->cartridges }}">
                        @error('cartridges')
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
