@extends('panel.layouts.master')
@section('title', 'کاربران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>کاربران</h6>
                @can('users-create')
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد کاربر
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center" style="width: 100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>شماره موبایل</th>
                        <th>نقش</th>
                        <th>عکس امضا</th>
                        <th>عکس پروفایل</th>
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
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->family }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->role->label }}</td>
                            <td>
                                @if($user->sign_image)
                                    <a href="{{ $user->sign_image ?? '' }}">
                                        <img src="{{ $user->sign_image ?? '' }}" class="sign" alt="sign" width="75px" height="75px">
                                    </a>
                                @else
                                    عکس امضا ندارد
                                @endif
                                <button class="btn btn-info btn-sm mt-1" data-toggle="modal" data-target="#editSignImageModal{{ $user->id }}">ویرایش عکس امضا</button>
                            </td>
                            <td>
                                @if($user->profile)
                                    <a href="{{ $user->profile ?? '' }}">
                                        <img src="{{ $user->profile ?? '' }}" class="profile" alt="profile" width="75px" height="75px">
                                    </a>
                                @else
                                    عکس پروفایل ندارد
                                @endif
                                <button class="btn btn-info btn-sm mt-1" data-toggle="modal" data-target="#editProfileImageModal{{ $user->id }}">ویرایش عکس پروفایل</button>
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
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('users.destroy',$user->id) }}" data-id="{{ $user->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>

                        <!-- Modal برای ویرایش عکس امضا -->
                        <div class="modal fade" id="editSignImageModal{{ $user->id }}" tabindex="-1" aria-labelledby="editSignImageModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editSignImageModalLabel">ویرایش عکس امضا</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('users.updateSignImage', $user->id) }}" method="post" enctype="multipart/form-data" class="dropzone" id="sign-dropzone{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="dz-message">عکس امضا خود را اینجا رها کنید</div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                                        <button type="button" class="btn btn-primary" onclick="processSignUpload('{{ $user->id }}')">بارگذاری</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal برای ویرایش عکس پروفایل -->
                        <div class="modal fade" id="editProfileImageModal{{ $user->id }}" tabindex="-1" aria-labelledby="editProfileImageModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editProfileImageModalLabel">ویرایش عکس پروفایل</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('users.updateProfileImage', $user->id) }}" method="post" enctype="multipart/form-data" class="dropzone" id="profile-dropzone{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="dz-message">عکس پروفایل خود را اینجا رها کنید</div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                                        <button type="button" class="btn btn-primary" onclick="processProfileUpload('{{ $user->id }}')">بارگذاری</button>
                                    </div>
                                </div>
                            </div>
                        </div>

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

    <!-- اسکریپت Dropzone -->
    <script>
        Dropzone.autoDiscover = false;

        function processSignUpload(userId) {
            var dropzone = new Dropzone("#sign-dropzone" + userId);
            dropzone.processQueue();
        }

        function processProfileUpload(userId) {
            var dropzone = new Dropzone("#profile-dropzone" + userId);
            dropzone.processQueue();
        }
    </script>
@endsection
