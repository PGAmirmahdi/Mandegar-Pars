@extends('panel.layouts.master')
@section('title', 'ارسال پیامک')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ارسال پیامک</h6>
            </div>
            <form id="sms-form" action="{{ route('sms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_name">نام گیرنده<span class="text-danger">*</span></label>
                        <input type="text" name="receiver_name" class="form-control" id="receiver_name" value="{{ old('receiver_name') }}">
                        @error('receiver_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_phone">شماره گیرنده<span class="text-danger">*</span></label>
                        <input type="text" name="receiver_phone" class="form-control" id="receiver_phone" value="{{ old('receiver_phone') }}">
                        @error('receiver_phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="message">متن پیام<span class="text-danger">*</span></label>
                        <textarea type="text" id="message" name="message" class="form-control">{{ old('message') }}</textarea>
                        @error('message')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ارسال پیامک</button>
                <div class="modal" id="uploadModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">در حال ارسال...</h5>
                            </div>
                            <div class="modal-body text-center">
                                <p>لطفا منتظر بمانید...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        .modal-content {
            width: 300px;
        }
        .textarea {
            width: 100%;
            min-height: 200px;
            border-radius: 5px;
            border: 1px solid gainsboro;
            padding: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
            outline: 0;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    {{--Jquery--}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var form = $('#sms-form');
            var modal = $('#uploadModal');

            form.on('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this);
                var messageContent = $('#message').val();
                formData.set('message', messageContent);

                modal.css('display', 'flex');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        console.log("قبل از ارسال");
                    },
                    success: function (response) {
                        modal.hide();
                        alert(response.success);
                        window.location.href = "{{ route('sms.index') }}";
                    },
                    error: function (xhr) {
                        modal.hide();
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = "خطا در اعتبارسنجی:<br>";
                            for (var key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessage += "- " + errors[key][0] + "<br>";
                                }
                            }
                            alert(errorMessage);
                        } else {
                            alert("مشکلی در ارسال پیامک وجود دارد");
                        }
                    }
                });
            });
        });
    </script>
@endsection
