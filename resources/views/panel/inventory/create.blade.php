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
                        <label for="product_id">انتخاب کالا<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="product_id" id="product_id"  data-select2-id="1">
                            @foreach(\App\Models\Product::all() as $product)
                                <option
                                    value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{$product->title . ' - ' . $product->productModels->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
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

