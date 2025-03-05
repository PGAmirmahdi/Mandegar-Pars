@extends('panel.layouts.master')
@section('title', 'چت کاربران با هوش مصنوعی ماندگار')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <a class="btn btn-facebook" href="{{ route('Ai.create') }}">
                    <i class="fa fa-comments"></i> شروع گفت و گو
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام کاربر</th>
                        <th>آخرین پیام</th>
                        <th>وضعیت</th>
                        <th>مشاهده چت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($messages as $conversation)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $conversation->user->fullName() }}</td>
                            <td>{{ $conversation->updated_at ? verta($conversation->updated_at)->format('H:i - Y/m/d') : '' }}</td>
                            <td>
                                <span class="badge badge-primary">بسته شده</span>
                            </td>
                            <td>
                                <a class="btn btn-info btn-floating"
                                   href="{{ route('Ai.show', $conversation->user->id) }}">
                                    <i class="fa fa-comments"></i>
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
                        <th>وضعیت</th>
                        <th>مشاهده چت</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
@endsection

