@extends('panel.layouts.master')
@section('title', 'یادداشت ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>یادداشت ها</h6>
                @can('notes-create')
                    <a href="{{ route('notes.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد یادداشت
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>تاریخ ایجاد</th>
                        @can('notes-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('notes-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($notes as $key => $note)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $note->title }}</td>
                            <td>{{ verta($note->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('notes-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('notes.edit', $note->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('notes-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('notes.destroy',$note->id) }}" data-id="{{ $note->id }}">
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
            <div class="d-flex justify-content-center">{{ $notes->links() }}</div>
        </div>
    </div>
@endsection


