@extends('panel.layouts.master')
@section('title', 'همکاران')
@section('content')
    <style>
        .profile {
            box-shadow: 0px 3px 3px 0px gainsboro;
            border-radius: 100%;
        }

        .profile2 {
            filter: drop-shadow(0px 4px 2px rgba(0, 0, 0, 0.5));
        }
    </style>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>همکاران</h6>
                @can('users-create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد همکار
                    </a>
                @endcan
            </div>
            @if(in_array(auth()->user()->role->name , ['admin','ceo','office-manager']))
                <form action="{{route('User.search')}}" id="user_search">
                    <div class="row mb-3">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                            <select name="user" class="js-example-basic-single select2-hidden-accessible"
                                    form="user_search">
                                <option value="all">نام همکار (همه)</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option
                                        value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                        {{ $user->name . ' ' . $user->family }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button class="btn btn-primary mx-2" type="submit">
                                جست و جو
                            </button>
                        </div>
                    </div>
                </form>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center" style="width: 100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عکس پروفایل</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>جنسیت</th>
                        <th>شماره موبایل</th>
                        <th>نقش</th>
                        <th>عکس امضا</th>
                        <th>تاریخ ایجاد</th>
                        @can('users-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('users-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key => $user)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                @if($user->profile)
                                    <a href="{{ $user->profile ?? '' }}">
                                        <img src="{{ $user->profile ?? '' }}" class="profile" alt="profile" width="60px"
                                             height="60px">
                                    </a>
                                @elseif(!$user->profile && $user->gender == 'female')
                                    <img src="{{asset('assets/media/image/Female.png')}}" class="profile"
                                         alt="female-profile" width="60px"
                                         height="60px">
                                @elseif(!$user->profile && $user->gender == 'male')
                                    <img src="{{asset('assets/media/image/Male.png')}}" class="profile"
                                         alt="female-profile" width="60px"
                                         height="60px">
                                @else
                                    <img src="{{asset('assets/media/image/inquery.png')}}" class="profile2"
                                         alt="female-profile" width="60px"
                                         height="60px">
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->family }}</td>
                            <td>
                                @if($user->gender == 'male')
                                    آقا
                                @elseif($user->gender == 'female')
                                    خانم
                                @else
                                    تعیین نشده
                                @endif</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->role->label }}</td>
                            <td>
                                @if($user->sign_image)
                                    <a href="{{ $user->sign_image ?? '' }}">
                                        <img src="{{ $user->sign_image ?? '' }}" class="sign" alt="sign" width="75px"
                                             height="75px">
                                    </a>
                                @else
                                    عکس امضا ندارد
                                @endif
                            </td>
                            <td>{{ verta($user->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('users-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('users.edit', $user->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('users-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('users.destroy',$user->id) }}" data-id="{{ $user->id }}">
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
            <div class="d-flex justify-content-center">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
