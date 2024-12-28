@extends('panel.layouts.master')
@section('title', 'فعالیت‌های کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>فعالیت‌های کاربران</h6>
                <form method="GET" action="{{ route('activity') }}" class="d-flex">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                                data-select2-id="3">
                            <option value="all">نام کاربر(همه)</option>
                            @foreach(\App\Models\User::all(['id','name','family']) as $user)
                                <option
                                    value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                    {{ $user->name . ' ' . $user->family }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
