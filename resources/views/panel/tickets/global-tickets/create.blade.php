@extends('panel.layouts.master')
@section('title', 'ثبت تیکت بین شرکتی')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
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
                                <button class="btn btn-primary" id="submit_ticket" type="submit">ثبت فرم</button>
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
        var company_name = @json(env('COMPANY_NAME'));
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

            fetchUsers(company_name);

            $('#company_id').change(function () {
                var selectedValue = $(this).val();
                console.log(selectedValue);
                fetchUsers(selectedValue);
            });


            var initialCompanyId = $('#company_id').val();
            if (initialCompanyId) {
                fetchUsers(initialCompanyId);
            }


            $('#submit_ticket').on('click', function (e) {
                e.preventDefault();

                var sender_id = {{ auth()->id() }};
                var company = "{{ env('COMPANY_NAME') }}";
                var receiver_id = $('#user_select').val();
                var title = $('#title').val();
                var text = $('#text').val();
                var fileInput = $('#file')[0].files[0];

                var formData = new FormData();
                formData.append('sender_id', sender_id);
                formData.append('company', company);
                formData.append('receiver_id', receiver_id);
                formData.append('title', title);
                formData.append('text', text);

                if (fileInput) {
                    formData.append('file', fileInput);
                }

                $.ajax({
                    url: '{{ env("API_BASE_URL") . "tickets" }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'API_KEY': "{{ env('API_KEY_TOKEN_FOR_TICKET') }}"
                    },
                    beforeSend: function () {
                        $('.btn-primary').prop('disabled', true).text('در حال ارسال...');
                    },
                    success: function (response) {
                        if (response.id) {
                            var editUrl = "{{ route('global-tickets.edit', ':id') }}".replace(':id', response.id);
                            window.location.href = editUrl;
                        } else {
                            console.error('خطا: ID تیکت در پاسخ دریافت نشد.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr);
                        alert('مشکلی پیش آمد، لطفاً دوباره امتحان کنید.');
                    },
                    complete: function () {
                        $('.btn-primary').prop('disabled', false).text('ثبت فرم');
                    }
                });
            });



        });
    </script>
@endsection
