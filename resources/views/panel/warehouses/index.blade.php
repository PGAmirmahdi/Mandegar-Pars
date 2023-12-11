@extends('panel.layouts.master')
@section('title', 'انبار')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>انبار</h6>
                @can('warehouses-create')
                    <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد انبار
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام انبار</th>
                        <th>موجودی اولیه</th>
                        <th>موجودی فعلی</th>
                        <th>ورود</th>
                        <th>خروج</th>
                        <th>تاریخ ایجاد</th>
                        @can('inventory-list')
                            <th>مشاهده</th>
                        @endcan
                        @can('warehouses-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('warehouses-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($warehouses as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ number_format($item->getInitialCount()) }}</td>
                            <td>{{ number_format($item->getCurrentCount()) }}</td>
                            <td>{{ number_format($item->getInputCount()) }}</td>
                            <td>{{ number_format($item->getOutputCount()) }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('inventory-list')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('inventory.index', ['warehouse_id' => $item->id]) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('warehouses-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('warehouses.edit', $item->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('warehouses-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('warehouses.destroy',$item->id) }}" data-id="{{ $item->id }}">
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
            <div class="d-flex justify-content-center">{{ $warehouses->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


