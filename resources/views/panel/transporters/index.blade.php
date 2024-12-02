@extends('panel.layouts.master')
@section('title', 'حمل و نقل کننده‌ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>حمل و نقل کننده‌ها</h6>
                @can('transporters-create')
                    <a href="{{ route('transporters.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد حمل و نقل کننده
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>کد</th>
                        <th>آدرس</th>
                        <th>شماره تماس</th>
                        <th>تاریخ ایجاد</th>
                        @can('transporters-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('transporters-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transporters as $key => $transporter)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $transporter->name }}</td>
                            <td>{{ $transporter->code }}</td>
                            <td>{{ $transporter->address }}</td>
                            <td>{{ $transporter->phone }}</td>
                            <td>{{ verta($transporter->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('transporters-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('transporters.edit', $transporter->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('transporters-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('transporters.destroy', $transporter->id) }}" data-id="{{ $transporter->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $transporters->links() }}</div>
        </div>
    </div>
@endsection
