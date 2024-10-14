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
                        <th>ویرایش عکس امضا</th>
                        <th>ویرایش عکس پروفایل</th>
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
                            </td>
                            <td>
                                @if($user->profile)
                                    <a href="{{ $user->profile ?? '' }}">
                                        <img src="{{ $user->profile ?? '' }}" class="profile" alt="profile" width="75px" height="75px">
                                    </a>
                                @else
                                    عکس پروفایل ندارد
                                @endif
                            </td>
                            <td>{{ verta($user->created_at)->format('H:i - Y/m/d') }}</td>
                            <td><button class="btn btn-info btn-floating" data-toggle="modal" data-target="#editSignImageModal{{ $user->id }}"><i class="fa fa-edit"></i></button></td>
                            <td><button class="btn btn-info btn-floating" data-toggle="modal" data-target="#editProfileImageModal{{ $user->id }}"><i class="fa fa-edit"></i></td>
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
                                        <form action="{{ route('users.uploadSignImage', $user->id) }}" method="post" enctype="multipart/form-data" class="dropzone" id="sign-dropzone{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="sign_image">
                                            <div class="dz-message">عکس امضا خود را اینجا رها کنید</div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
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
                                        <form action="{{ route('users.uploadProfile', $user->id) }}" method="post" enctype="multipart/form-data" class="dropzone" id="profile-dropzone{{ $user->id }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="profile">
                                            <div class="dz-message">عکس پروفایل خود را اینجا رها کنید</div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
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
        Dropzone.options.signDropzone = {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            init: function() {
                this.on("success", function(file, response) {
                    // مدیریت پاسخ
                });
                this.on("error", function(file, response) {
                    // مدیریت خطا
                });
            }
        };
        // آپلود پروفایل
        document.getElementById('upload-profile-button').addEventListener('click', function() {
            var formData = new FormData();
            var fileInput = document.getElementById('profile-input'); // فرض بر این است که یک input برای آپلود پروفایل دارید

            if (fileInput.files.length > 0) {
                formData.append('profile', fileInput.files[0]);

                fetch("{{ route('users.uploadProfile', ':id') }}".replace(':id', userId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect; // هدایت به صفحه کاربران
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => console.error('Upload error:', error));
            } else {
                console.error('هیچ فایلی برای آپلود انتخاب نشده است.');
            }
        });

        // آپلود امضا
        document.getElementById('upload-sign-button').addEventListener('click', function() {
            var formData = new FormData();
            var fileInput = document.getElementById('sign-input'); // فرض بر این است که یک input برای آپلود امضا دارید

            if (fileInput.files.length > 0) {
                formData.append('sign_image', fileInput.files[0]);

                fetch("{{ route('users.uploadSignImage', ':id') }}".replace(':id', userId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },ل
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect; // هدایت به صفحه کاربران
                        } else {
                            console.error(data.message);
                        }
                    })
                    .catch(error => console.error('Upload error:', error));
            } else {
                console.error('هیچ فایلی برای آپلود انتخاب نشده است.');
            }
        });

    </script>
@endsection
