@extends('panel.layouts.master')
@section("title",'وارد کردن آنالیز')
@section('content')
    <div class="card">
        <div class="card-body">
        <h2>مرحله 4: وارد کردن تعداد محصولات</h2>
        <form method="POST" action="{{ route('analyse.submit') }}">
            @csrf
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>نام محصول</th>
                    <th>تعداد</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->title }}</td>
                        <td>
                            <input type="number" name="products[{{ $product->id }}]" min="0" class="form-control">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">ثبت آنالیز</button>
        </form>
        </div>
    </div>
@endsection
