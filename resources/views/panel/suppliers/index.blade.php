@extends('panel.layouts.master')
@section('title', 'لیست تمامی تامین کنندگان')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست تمامی تامین کنندگان</h6>
                <div>
                    <form action="{{ route('suppliers.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('suppliers-create')
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد تامین کننده
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('suppliers.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <input type="text" name="code" form="search_form" class="form-control" placeholder="کد تامین کننده"
                           value="{{ request()->code ?? null }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="user" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="5">
                        <option value="all" selected>همکار (همه)</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                {{ $user->name . " " . $user->family }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="supplier" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="0">
                        <option value="all">نام سازمان/فروشگاه (همه)</option>
                        @foreach(\App\Models\Supplier::all() as $supplier)
                            <option
                                value="{{ $supplier->name }}" {{ request()->supplier == $supplier->name ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="province" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">استان (همه)</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option
                                value="{{ $province->name }}" {{ request()->province == $province->name ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="supplier_type" form="search_form"
                            class="js-example-basic-single select2-hidden-accessible" data-select2-id="2">
                        <option value="all">مشتری (همه)</option>
                        @foreach(\App\Models\Supplier::TYPE as $key => $value)
                            <option value="{{ $key }}" {{ request()->supplier_type == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- فیلد جدید: دسته‌بندی -->
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="category" form="search_form"
                            class="js-example-basic-single select2-hidden-accessible">
                        <option value="all">دسته بندی (همه)</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}" {{ request()->category == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <td>ایجاد کننده</td>
                        <th>کد مشتری</th>
                        <th>نام سازمان/فروشگاه</th>
                        <th>نوع</th>
                        <th>استان</th>
                        <th>شماره تماس 1</th>
                        <th>زمینه فعالیت</th>
                        <th>تاریخ ایجاد</th>
                        <th>جزئیات</th>
                        @can('suppliers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('suppliers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($suppliers as $key => $supplier)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $supplier->user->name . ' '  . $supplier->user->family}}</td>
                            <td>{{ $supplier->code ?? '---' }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ \App\Models\Supplier::TYPE[$supplier->supplier_type] }}</td>
                            <td>{{ $supplier->province }}</td>
                            <td>{{ $supplier->phone1 }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @if(is_array($supplier->category) && count($supplier->category) > 0)
                                        @foreach($supplier->category as $categoryId)
                                            @php
                                                $category = \App\Models\Category::find($categoryId);
                                            @endphp
                                            @if($category)
                                                <li class="d-inline-block mr-1">
                                                    <span class="badge badge-primary p-1 font-size-14">+ {{ $category->name }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    @else
                                        <p>تعیین نشده</p>
                                    @endif
                                </ul>

                            </td>
                            <td>{{ verta($supplier->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating"
                                   href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'url' => request()->getRequestUri()]) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @can('suppliers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating"
                                       href="{{ route('suppliers.edit', ['supplier' => $supplier->id, 'url' => request()->getRequestUri()]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('suppliers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('suppliers.destroy',$supplier->id) }}"
                                            data-id="{{ $supplier->id }}">
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
            <div class="d-flex justify-content-center">{{ $suppliers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


