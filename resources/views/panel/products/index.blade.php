@extends('panel.layouts.master')
@section('title', 'محصولات')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>محصولات</h6>
                @can('products-create')
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد محصول
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تصویر محصول</th>
                        <th>عنوان محصول</th>
                        <th>کد محصول</th>
                        <th>دسته بندی</th>
                        <th>موجودی</th>
                        <th>تاریخ ایجاد</th>
                        @can('products-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('products-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><a href="{{ $product->image }}" target="_blank"><img src="{{ $product->image }}" width="40px"></a></td>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->total_count }}</td>
                            <td>{{ verta($product->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('products-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('products.edit', $product->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('products-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('products.destroy',$product->id) }}" data-id="{{ $product->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $products->links() }}</div>
        </div>
    </div>
@endsection


