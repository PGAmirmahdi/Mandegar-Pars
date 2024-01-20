@extends('panel.layouts.master')
@section('title', 'ورود')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ورود</h6>
                @can('input-reports-create')
                    <a href="{{ route('inventory-reports.create', ['type' => 'input', 'warehouse_id' => request()->warehouse_id]) }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت ورودی
                    </a>
                @endcan
            </div>
            <form action="{{ route('inventory-reports.search') }}" method="get" id="search_form">
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <input type="hidden" name="type" value="{{ request()->type }}">
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="inventory_id" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">فیلتر بر اساس کالا (همه)</option>
                        @foreach(\App\Models\Inventory::where('warehouse_id',$warehouse_id)->pluck('title','id') as $id => $title)
                            <option value="{{ $id }}" {{ request()->inventory_id == $id ? 'selected' : '' }}>{{ $title }}</option>
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
                        <th>تحویل دهنده</th>
                        <th>تاریخ ورود</th>
                        <th>تاریخ ثبت</th>
                        <th>رسید انبار</th>
                        @can('input-reports-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('input-reports-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><strong>{{ $item->person }}</strong></td>
                            <td>{{ verta($item->date)->format('Y/m/d') }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('inventory-reports.show', $item) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @can('input-reports-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('inventory-reports.edit', ['inventory_report' => $item->id, 'type' => 'input', 'warehouse_id' => request()->warehouse_id]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('input-reports-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('inventory-reports.destroy',$item->id) }}" data-id="{{ $item->id }}">
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
            <div class="d-flex justify-content-center">{{ $reports->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


