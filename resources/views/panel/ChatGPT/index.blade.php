@can('admin')
@extends('panel.layouts.master')
@section('title', 'چت کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>چت کاربران</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام کاربر</th>
                        <th>آخرین پیام</th>
                        <th>مشاهده چت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($conversations as $key => $conversation)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $conversation->user->name }}</td> <!-- نمایش نام کاربر -->
                            <td>{{ verta($conversation->updated_at)->format('H:i - Y/m/d') }}</td> <!-- زمان آخرین پیام -->
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('chat.show', $conversation->user_id) }}">
                                    <i class="fa fa-comments"></i> مشاهده چت
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>نام کاربر</th>
                        <th>آخرین پیام</th>
                        <th>مشاهده چت</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $conversations->links() }}</div>
        </div>
    </div>
@endsection
@endcan
