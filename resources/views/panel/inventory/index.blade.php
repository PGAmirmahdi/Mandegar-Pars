@extends('panel.layouts.master')
@section('title', 'انبار')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>انبار</h6>
                <div>
                    <form action="{{ route('inventory.excel') }}" method="post" id="excel_form">
                        <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>
                    @can('inventory-create')
                        <a href="{{ route('inventory.create', ['warehouse_id' => $warehouse_id]) }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            افزودن کالا
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('inventory.search') }}" method="post" id="search_form">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="code" class="form-control" placeholder="کد محصول" value="{{ request()->code ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                    <input type="text" name="title" class="form-control" placeholder="عنوان محصول" value="{{ request()->title ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="type" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">نوع (همه)</option>
                        @foreach(\App\Models\Inventory::TYPE as $key => $value)
                            <option value="{{ $key }}" {{ request()->type == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
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
                        <th>عنوان کالا</th>
                        <th>کد کالا</th>
                        <th>نوع</th>
                        <th>موجودی اولیه</th>
                        <th>موجودی فعلی</th>
                        <th>تعداد ورود</th>
                        <th>تعداد خروج</th>
                        <th>تاریخ ایجاد</th>
                        @can('inventory-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('inventory-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ \App\Models\Inventory::TYPE[$item->type] }}</td>
                            <td>{{ number_format($item->initial_count) }}</td>
                            <td>{{ number_format($item->current_count) }}</td>
                            <td>{{ number_format($item->getInputCount()) }}</td>
                            <td>{{ number_format($item->getOutputCount()) }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('inventory-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('inventory.edit', $item->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('inventory-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('inventory.destroy',$item->id) }}" data-id="{{ $item->id }}">
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
            <div class="d-flex justify-content-center">{{ $data->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


