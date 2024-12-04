@extends('panel.layouts.master')
@section("title",'انتخاب برند')
@section('content')
    <div class="container">
        <h2>مرحله 3: انتخاب برند</h2>
        <form method="POST" action="{{ route('analyse.step3.post') }}">
            @csrf
            <div class="form-group">
                <label for="brand">برند</label>
                <select id="brand" name="brand_id" class="form-control" required>
                    <option value="">انتخاب کنید</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">مرحله بعد</button>
        </form>
    </div>
@endsection
