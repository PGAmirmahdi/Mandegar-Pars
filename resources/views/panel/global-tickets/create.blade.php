@extends('panel.layouts.master')
@section('title', 'ثبت تیکت بین شرکتی')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('tickets.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="company">شرکت<span class="text-danger">*</span></label>
                                        <select name="company" id="company_id" class="form-control"
                                                data-toggle="select2">
                                            @foreach(\App\Models\GlobalTicket::COMPANIES as $key => $value)
                                                <option value="{{ $key }}" {{ old('company') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('company')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="receiver">گیرنده<span
                                                class="text-danger">*</span></label>
                                        <select name="receiver" id="user_select" class="form-control" data-toggle="select2">
                                            <option value="">انتخاب کنید...</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user['id'] }}" {{ old('receiver') == $user['id'] ? 'selected' : '' }}>
                                                    {{ $user['name'] }} {{ $user['family'] }} - {{ $user['role_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('receiver')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="title">عنوان تیکت<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" id="title"
                                               value="{{ old('title') }}">
                                        @error('title')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="file">فایل</label>
                                        <input type="file" name="file" class="form-control" id="file">
                                        @error('file')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                        <a href="" target="_blank" class="btn btn-link d-none" id="file_preview">پیش
                                            نمایش</a>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                        <label class="form-label" for="text">متن تیکت<span class="text-danger">*</span></label>
                                        <textarea type="text" name="text" class="form-control" id="text"
                                                  rows="5">{{ old('text') }}</textarea>
                                        @error('text')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">ثبت فرم</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var loading = $('.loading');
        $(document).ready(function () {
            $('#file').on('change', function () {
                $('#file_preview').removeClass('d-none')

                let file = this.files[0];
                let url = URL.createObjectURL(file);

                $('#file_preview').attr('href', url)
            });

            function fetchUsers(companyId) {
                if (companyId) {
                    $.ajax({
                        url: '{{ env('API_BASE_URL').'get-users' }}',
                        type: 'POST',
                        headers: {
                            'API_KEY': "{{env('API_KEY_TOKEN_FOR_TICKET')}}"
                        },
                        data: {
                            company_name: companyId,
                            user_id: {{ auth()->id() }},
                            _token: '{{ csrf_token() }}'
                        },
                        beforeSend: function () {
                            $('#user_select').empty();
                            $('#user_select').append('<option value="">در حال بارگذاری...</option>');
                        },
                        success: function (response) {
                            $('#user_select').empty();
                            $('#user_select').append('<option value="">انتخاب کنید...</option>');
                            $.each(response, function (key, user) {
                                $('#user_select').append('<option value="' + user.id + '">' + user.name + ' ' + user.family + ' - ' + user.role_name + '</option>');
                            });
                        },
                        error: function (xhr) {
                            console.error('Error:', xhr);
                        }
                    });
                }
            }

            $('#company_id').change(function () {
                var selectedValue = $(this).val();
                console.log(selectedValue);
                fetchUsers(selectedValue);
            });


            var initialCompanyId = $('#company_id').val();
            if (initialCompanyId) {
                fetchUsers(initialCompanyId);
            }
        });
    </script>
@endsection
