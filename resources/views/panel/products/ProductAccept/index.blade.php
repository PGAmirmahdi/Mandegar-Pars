@extends('panel.layouts.master')
@if(auth()->user()->isAdmin())
    @section('title', 'لیست درخواست ها')
@else
    @section('title', 'لیست درخواست های من')
@endif
@section('content')
    <style>
        .table-warning {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>
                    @if(auth()->user()->isAdmin())
                        لیست درخواست ها
                    @else
                        لیست درخواست های من
                    @endif
                </h6>
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
                            @if(auth()->user()->isAdmin())
                                ثبت کالا
                            @else
                                درخواست ثبت کالا
                            @endif
                        </a>
                    @endcan
                </div>

            </div>
            <form action="{{ route('products.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="code" class="form-control" placeholder="کد کالا"
                           value="{{ request()->code ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="category" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">شرح کالا (همه)</option>
                        @foreach(\App\Models\Category::all(['id','name']) as $category)
                            <option
                                value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="model" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="2">
                        <option value="all">برند (همه)</option>
                        @foreach(\App\Models\ProductModel::all(['id','slug']) as $model)
                            <option value="{{ $model->id }}" {{ request()->model ==  $model->id ? 'selected' : '' }}>
                                {{ $model->slug }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="3">
                        <option value="all">مدل کالا (همه)</option>
                        @foreach(\App\Models\Product::all(['id','title']) as $product)
                            <option
                                value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <label for="status">وضعیت<span class="text-danger">*</span></label>
                    <select name="status" class="js-example-basic-single select2-hidden-accessible" id="status">
                        @foreach(\App\Models\Product::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ old('status', $status) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کد کالا</th>
                        <th>ثبت کننده</th>
                        <th>شرح کالا</th>
                        <th>برند</th>
                        <th>مدل</th>
                        <th>وضعیت</th>
                        @canany(['admin','accountant'])
                            <th>موجودی</th>
                        @endcanany
                        @can('admin')
                            <th>تاریخ ایجاد</th>
                        @endcan
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
                        <tr @if($product->latestInventory() < 10 ) @canany(['admin','accountant']) class="table-warning" @endcanany @endif>
                            <td>{{ ++$key }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $product->code }}</td>
                            <td>{{ $product->creator->family }}</td>
                            <td>{{ $product->category->name ?? 'شرح نامشخص' }}</td>
                            <td>{{ $product->productModels->slug ?? 'برند نامشخص' }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $product->title }}</td>
                                <td>
                                    @if($product->status == 'approved')
                                        <span class="badge badge-success">تایید شده</span>
                                    @elseif($product->status == 'pending')
                                        <span class="badge badge-warning">منتظر تایید</span>
                                    @elseif($product->status == 'rejected')
                                        <span class="badge badge-danger">رد شده</span>
                                    @else
                                        <span class="badge badge-info">نامشخص</span>
                                    @endif
                                </td>
                            @canany(['admin','accountant'])
                                <td>{{ $product->latestInventory() }}</td>
                            @endcanany
                            @can('admin')
                                <td>{{ verta($product->created_at)->format('H:i - Y/m/d') }}</td>
                            @endcan
                            @can('products-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating"
                                       href="{{ route('products.edit', $product->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('products-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('products.destroy',$product->id) }}"
                                            data-id="{{ $product->id }}">
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
