@extends('panel.layouts.master')
@section('title', 'فعالیت‌های کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <div class="card-header col-12 row justify-content-between">
                    <h5 class="font-weight-bolder">فعالیت های اخیر</h5>
                    <form method="get" action="{{ route('activity.search') }}" class="row col-6 justify-content-end">
                        <div class="col-3">
                            <select name="user" class="js-example-basic-single select2-hidden-accessible">
                                <option value="all">نام کاربر (همه)</option>
                                @foreach(\App\Models\User::all(['id','name','family']) as $user)
                                    <option value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                        {{ $user->name . ' ' . $user->family }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="text" name="start_date" class="form-control date-picker-shamsi-list" placeholder="از تاریخ" value="{{ request('start_date') }}" autocomplete="off">
                        </div>
                        <div class="col-3">
                            <input type="text" name="end_date" class="form-control date-picker-shamsi-list" placeholder="تا تاریخ" value="{{ request('end_date') }}" autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary ml-2">
                            <i class="fa fa-search mx-2"></i> جستجو
                        </button>
                    </form>
                </div>
            </div>
            @can('activity-list')
                <div class="card col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($activities as $key => $activity)
                                <div class="timeline-item">
                                    <div>
                                        <figure class="avatar avatar-sm m-r-15 bring-forward">
                                            <span class="avatar-title bg-primary-bright text-primary rounded-circle">
                                                @if(isset($activity->user->profile))
                                                    <img src="{{ $activity->user->profile }}" style="max-width: 76.79px" data-toggle="tooltip" data-placement="bottom" title="{{ $activity->user->fullName() }}" class="rounded-circle" alt="image" width="36.5px" height="36.5px">
                                                @else
                                                    <i class="fa-solid fa-clock"></i>
                                                @endif
                                            </span>
                                        </figure>
                                    </div>
                                    <div>
                                        <p class="font-size-12 m-0">
                                            {{ $activity->user->fullName() }} <span class="font-size-10 text-secondary m-0 p-0">({{ $activity->action }})</span>
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ \Illuminate\Support\Str::limit($activity->description) }}</strong>
                                        </p>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-clock m-r-5"></i>{{ verta($activity->created_at)->format('H:i - Y/m/d') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endcan
            <div class="d-flex justify-content-center">{{ $activities->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
