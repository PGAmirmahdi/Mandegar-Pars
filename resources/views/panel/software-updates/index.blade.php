@extends('panel.layouts.master')
@section('title', 'تغییرات نرم افزار')
@section('styles')
    <style>
        .modal-body ol{
            line-height: 2rem !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>تغییرات نرم افزار</h6>
                @can('software-updates-create')
                    <a href="{{ route('software-updates.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت تغییرات
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شماره نسخه</th>
                        <th>تاریخ انتشار</th>
                        <th>تاریخ ثبت</th>
                        @can('software-updates-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('software-updates-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($software_updates as $key => $update)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $update->version }}</td>
                            <td>{{ verta($update->date)->format('Y/m/d') }}</td>
                            <td>{{ verta($update->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('software-updates-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('software-updates.edit', $update->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('software-updates-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('software-updates.destroy',$update->id) }}" data-id="{{ $update->id }}">
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
            <div class="d-flex justify-content-center">{{ $software_updates->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
