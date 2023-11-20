@extends('panel.layouts.master')
@section('title', 'ورود')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ورود</h6>
                @can('input-reports-create')
                    <a href="{{ route('inventory-reports.create', ['type' => 'input']) }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت ورودی
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تحویل دهنده</th>
                        <th>تاریخ ثبت</th>
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
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('input-reports-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('inventory-reports.edit', ['inventory_report' => $item->id, 'type' => 'input']) }}">
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
            <div class="d-flex justify-content-center">{{ $reports->links() }}</div>
        </div>
    </div>
@endsection


