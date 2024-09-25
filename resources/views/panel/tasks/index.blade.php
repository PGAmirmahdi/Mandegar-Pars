@extends('panel.layouts.master')
@section('title', 'وظایف')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وظایف</h6>
                @can('tasks-create')
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد وظیفه
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>ایجاد کننده</th>
                        <th>تاریخ ایجاد</th>
                        <th>مشاهده</th>
                        @can('tasks-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('tasks-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $key => $task)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->creator_id == auth()->id() ? 'شما' : $task->creator->fullName() }}</td>
                            <td>{{ verta($task->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('tasks.show', $task->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @can('tasks-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $task->creator_id != auth()->id() ? 'disabled' : '' }}" href="{{ route('tasks.edit', $task->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('tasks-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('tasks.destroy',$task->id) }}" data-id="{{ $task->id }}" {{ $task->creator_id != auth()->id() ? 'disabled' : '' }}>
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
            <div class="d-flex justify-content-center">{{ $tasks->links() }}</div>
        </div>
    </div>
@endsection


