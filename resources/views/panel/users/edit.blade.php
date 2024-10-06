@extends('panel.layouts.master')
@section('title', 'ویرایش کاربر')
@section('content')
    <div id="app">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>ویرایش کاربر</h6>
                </div>

                <!-- فرم آپلود تصویر امضاء -->
                <form id="form-sign-image" enctype="multipart/form-data" class="dropzone" data-field="sign_image">
                    @csrf
                    @method('PATCH')
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sign_image">تصویر امضاء (PNG)</label>
                        <input type="file" name="sign_image" id="sign-image-dropzone2">
                        @if($user->sign_image)
                            <a href="{{ $user->sign_image }}" class="btn btn-link" target="_blank">مشاهده امضاء</a>
                        @endif
                        @error('sign_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </form>

                <!-- فرم آپلود تصویر پروفایل -->
                <form id="form-profile" enctype="multipart/form-data" class="dropzone" data-field="profile">
                    @csrf
                    @method('PATCH')
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="profile">عکس پروفایل</label>
                        <input type="file" name="profile" id="profile-dropzone2">
                        @if($user->profile)
                            <a href="{{ asset('storage/' . $user->profile) }}" class="btn btn-link" target="_blank">مشاهده پروفایل</a>
                        @endif
                        @error('profile')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </form>

                <!-- سایر فیلدها -->
                <form id="form-details">
                    @csrf
                    @method('PATCH')
                    <div class="form-row">
                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                            <label for="name">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}">
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                            <label for="family">نام خانوادگی <span class="text-danger">*</span></label>
                            <input type="text" name="family" class="form-control" id="family" value="{{ $user->family }}">
                            @error('family')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                            <label for="phone">شماره موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ $user->phone }}">
                            @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                            <label for="password">رمزعبور</label>
                            <input type="password" name="password" class="form-control" id="password">
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @can('admin')
                            @if(auth()->id() != $user->id)
                                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                    <label for="role">نقش <span class="text-danger">*</span></label>
                                    <select class="form-control" name="role" id="role">
                                        @foreach(\App\Models\Role::all() as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        @endcan
                    </div>
                </form>
                <button class="btn btn-primary" id="submit">ثبت فرم</button>
            </div>
        </div>
    </div>

    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(() => {
            const dropzones = []
            $('.dropzone').each(function(i, el) {
                const name = 'g_' + $(el).data('field')
                var myDropzone = new Dropzone(el, {
                    url: window.location.pathname,
                    autoProcessQueue: false,
                    uploadMultiple: true,
                    parallelUploads: 100,
                    maxFiles: 100,
                    paramName: name,
                    addRemoveLinks: true,
                })
                dropzones.push(myDropzone)
            })

            document.querySelector("#submit").addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                let form = new FormData($('#form-details')[0])

                dropzones.forEach(dropzone => {
                    let { paramName } = dropzone.options
                    dropzone.files.forEach((file, i) => {
                        form.append(paramName + '[' + i + ']', file)
                    })
                })
                $.ajax({
                    method: 'POST',
                    url: "{{ route('users.update', $user->id) }}",
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Redirect or notify user of success
                        window.location.replace(response.redirect || response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Handle error
                        console.error("Error:", errorThrown);
                        // Display error message to user
                    }
                });
            });
        });
    </script>
@endsection
