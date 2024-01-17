@extends('panel.layouts.master')
@section('title', 'گارانتی ها')
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
                <h6>گارانتی ها</h6>
                @can('guarantees-create')
                    <a href="{{ route('guarantees.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد گارانتی
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شماره سریال</th>
                        <th>مدت گارانتی</th>
                        <th>تاریخ فعالسازی</th>
                        <th>تاریخ انقضا</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('guarantees-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('guarantees-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($guarantees as $key => $guarantee)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $guarantee->serial }}</td>
                            <td>{{ \App\Models\Guarantee::PERIOD[$guarantee->period] }}</td>
                            <td>{{ $guarantee->activated_at ? verta($guarantee->activated_at)->format('Y/m/d') : '---' }}</td>
                            <td>{{ $guarantee->expired_at ? verta($guarantee->expired_at)->format('Y/m/d') : '---' }}</td>
                            <td>
                                @if($guarantee->status == 'active')
                                    <span class="badge badge-success">{{ \App\Models\Guarantee::STATUS[$guarantee->status] }}</span>
                                @elseif($guarantee->status == 'inactive')
                                    <span class="badge badge-warning">{{ \App\Models\Guarantee::STATUS[$guarantee->status] }}</span>
                                @elseif($guarantee->status == 'expired')
                                    <span class="badge badge-danger">{{ \App\Models\Guarantee::STATUS[$guarantee->status] }}</span>
                                @else
                                    <span class="badge badge-danger">{{ \App\Models\Guarantee::STATUS[$guarantee->status] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($guarantee->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('guarantees-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('guarantees.edit', $guarantee->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('guarantees-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('guarantees.destroy',$guarantee->id) }}" data-id="{{ $guarantee->id }}">
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
            <div class="d-flex justify-content-center">{{ $guarantees->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
