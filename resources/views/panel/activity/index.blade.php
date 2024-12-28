@extends('panel.layouts.master')
@section('title', 'فعالیت‌های کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>فعالیت‌های کاربران</h6>
                <form method="GET" action="{{ route('activity') }}" class="d-flex">
                    <input type="text" name="search" class="form-control" placeholder="نام کاربر را وارد کنید..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary ml-2">
                        <i class="fa fa-search mx-2"></i>
                        جستجو
                    </button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام کاربر</th>
                        <th>نوع فعالیت</th>
                        <th>توضیحات</th>
                        <th>تاریخ</th>
                        @can('activity-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($activities as $key => $activity)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ $activity->action }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ verta($activity->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('activity-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('activity.destroy',$activity->id) }}"
                                            data-id="{{ $activity->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">هیچ فعالیتی یافت نشد.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $activities->links() }}</div>
        </div>
    </div>
@endsection
