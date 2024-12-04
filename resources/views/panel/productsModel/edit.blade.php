@extends('panel.layouts.master')
@section('title', 'ویرایش برند')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش برند</h6>
            </div>
            <form action="{{ route('productsModel.update', $productsModel->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="name">نام برند<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $productsModel->name }}" placeholder="محصول 1">
                        @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="slug">اسلاگ<span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" id="slug" value="{{ $productsModel->slug }}" placeholder="product-1">
                        @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="category_id">دسته بندی<span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $productsModel->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
