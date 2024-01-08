@extends('panel.layouts.master')
@section('title', 'محصولات')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>محصولات</h6>
                <div>
                    <form action="{{ route('products.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('products-create')
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد محصول
                        </a>
                    @endcan
                </div>

            </div>
            <form action="{{ route('products.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="code" class="form-control" placeholder="کد محصول" value="{{ request()->code ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                    <input type="text" name="title" class="form-control" placeholder="عنوان محصول" value="{{ request()->title ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
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
                            <td>
                                <a href="{{ $product->image }}" target="_blank">
                                    <img data-src="{{ $product->image }}" class="lazyload" width="40px">
                                </a>
                            </td>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->category->name }}</td>
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
            <div class="d-flex justify-content-center">{{ $products->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
@endsection
