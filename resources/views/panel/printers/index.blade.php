@extends('panel.layouts.master')
@section('title', 'پرینتر ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پرینتر ها</h6>
                @can('printers-create')
                    <a href="{{ route('printers.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد پرینتر
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام پرینتر</th>
                        <th>برند</th>
                        <th>تاریخ ایجاد</th>
                        @can('printers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('printers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($printers as $key => $printer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $printer->name }}</td>
                            <td>{{ $printer->brand }}</td>
                            <td>{{ verta($printer->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('printers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('printers.edit', $printer->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('printers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('printers.destroy',$printer->id) }}" data-id="{{ $printer->id }}">
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
            <div class="d-flex justify-content-center">{{ $printers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


