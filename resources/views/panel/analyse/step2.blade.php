@extends('panel.layouts.master')
@section("title",'انتخاب دسته بندی')
@section('content')
    <div class="container">
        <h2>مرحله 2: انتخاب دسته‌بندی</h2>
        <form method="POST" action="{{ route('analyse.step2.post') }}">
            @csrf
            <div class="form-group">
                <label for="category">دسته‌بندی</label>
                <select id="category" name="category_id" class="form-control" required>
                    <option value="">انتخاب کنید</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">مرحله بعد</button>
        </form>
    </div>
@endsection

