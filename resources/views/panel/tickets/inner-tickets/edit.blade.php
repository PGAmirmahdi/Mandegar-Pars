@extends('panel.layouts.master')
@section('title', 'مشاهده تیکت')
@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">
    <style>
        .fa-check-double, .fa-check {
            color: green !important;
        }
        body{
            overflow: hidden !important;
        }
        .chat-body-messages {
            background-image: url({{asset('assets/media/image/chat.jpg')}});
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .btn.btn-outline-light {
            background-color: transparent !important;
            border: none;
            color: #fff;
        }

        .message-text {
            font-size: 13px !important;
            color: #fff;
        }

        .fa-check {
            color: #bbb !important;
        }

        .message-item {
            background-color: rgba(93, 74, 156, 0.58) !important;
            backdrop-filter: blur(6.9px);
            border-radius: 5px !important;
        }
        .message-time {
            font-size: 0.65rem !important;
            color: #8a8a8a !important;
            margin-left: 30px;
        }
        .outgoing-message {
            background-color: rgba(151, 151, 152, 0.48) !important;
            backdrop-filter: blur(6.9px);
            .message-text{
                color: #fff !important;
            }
            .message-time{
                color: #461c70 !important;
            }
        }

        .fa-check-double {
            color: #34b7f1;
        }

        img{
            max-width: 200px !important;
        }
        .message-content{
            padding:0px 8px;
        }
        .fa-check, .fa-check-double {
            font-size: 0.65rem !important;
        }
        .chat-app{
            height: 85vh;
        }
        .chat-body-messages {
            height: 70vh !important;
            overflow-y: auto !important;
        }

        .message-items {
            min-height: min-content; /* اطمینان از رشد صحیح محتوا */
        }
    </style>
@endsection

@section('content')
    <div class="card chat-app-wrapper">
        <div class="row chat-app">
            <div class="col-xl-12 col-md-12 chat-body">
                <div class="chat-body-header">
                    <a href="#" class="btn btn-dark opacity-3 m-r-10 btn-chat-sidebar-open">
                        <i class="ti-menu"></i>
                    </a>
                    <div>
                        <figure class="avatar avatar-sm m-r-10">
                            <img src="/assets/media/image/avatar.png" class="rounded-circle" alt="image">
                        </figure>
                    </div>
                    <div>
                        <h6 class="mb-1 primary-font line-height-18">
                            @if(auth()->id() == $ticket->sender_id)
                                {{ $ticket->receiver->fullName() }}
                            @else
                                {{ $ticket->sender->fullName() }}
                            @endif
                        </h6>
                    </div>
                    <div class="ml-auto d-flex">
                        <div class="mr-4">
                            @if($ticket->status == 'closed')
                                <span class="badge badge-success">بسته شده</span>
                            @else
                                <span class="badge badge-warning">درحال بررسی</span>
                            @endif
                        </div>
                        <div class="dropdown ml-2">
                            <button type="button" data-toggle="dropdown" class="btn btn-sm btn-warning btn-floating">
                                <i class="fa fa-cog"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-menu-body">
                                    <ul>
                                        <li>
                                            @if($ticket->status == 'closed')
                                                <a class="dropdown-item"
                                                   href="{{ route('ticket.changeStatus', $ticket->id) }}">درحال
                                                    بررسی</a>
                                            @else
                                                <a class="dropdown-item"
                                                   href="{{ route('ticket.changeStatus', $ticket->id) }}">بسته شده</a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-body-messages">
                    <div class="message-items">
                        @foreach($ticket->messages as $message)
                            @if($message->user_id == auth()->id())
                                <div id="message-{{ $message->id }}" class="message-item {{ $message->file ? 'message-item-media' : '' }}">
                                    <div class="message-content">
                                        @if($message->text)
                                            <div class="message-text">{{ $message->text }}</div>
                                        @endif
                                        @includeWhen($message->file, 'panel.partials.file-message')
                                        <div class="message-meta row @if($message->file) justify-content-between m-2 @else justify-content-between @endif px-3">
                                        <span class="message-time">
                                            {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                                        </span>
                                            @if($message->read_at)
                                                <i class="status-read fa fa-check-double"></i>
                                            @else
                                                <i class="status-sent fa fa-check"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div id="message-{{ $message->id }}" class="message-item outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
                                    @if($message->text)
                                        <div class="message-text @if($message->file) p-2 @endif">{{ $message->text }}</div>
                                    @endif
                                    @includeWhen($message->file, 'panel.partials.file-message')
                                        <div class="message-meta row @if($message->file) justify-content-center m-2 @else justify-content-between @endif px-3">
                <span class="message-time">
                    {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="chat-body-footer">
                    <form id="chatForm" action="{{ route('tickets.update', $ticket->id) }}" method="post"
                          enctype="multipart/form-data" class="d-flex align-items-center">
                        @csrf
                        @method('PUT')
                        <input type="text" name="text" class="form-control" placeholder="پیام ..." required>
                        <div class="d-flex">
                            <button type="submit" class="ml-3 btn btn-primary btn-floating">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                            <div class="dropup">
                                <button type="button" data-toggle="dropdown"
                                        class="ml-3 btn btn-success btn-floating">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-menu-body">
                                        <ul>
                                            <li>
                                                <label class="dropdown-item" for="file">
                                                    <i class="icon fa fa-file"></i>
                                                    <span id="file_lbl">فایل</span>
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <input type="file" name="file" class="d-none" id="file">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- begin::lightbox -->
    <script src="/vendors/lightbox/jquery.magnific-popup.min.js"></script>
    <script src="/assets/js/examples/lightbox.js"></script>

    <script>
        $(document).ready(function () {
            // تغییر نام برچسب فایل پس از انتخاب فایل
            $('#file').on('change', function () {
                $('#file_lbl').text(this.files[0].name);
                $('input[name="text"]').removeAttr('required');
            });

            // ارسال فرم پیام با AJAX
            $('#chatForm').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                var url = $(this).attr('action');

                // افزودن پیام موقت با آیکون در حال ارسال
                var tempMessageId = 'temp-' + Date.now();
                var tempMessage = `<div class="message-item" id="${tempMessageId}">
        <div class="message-content">
            <div class="message-text">${$('input[name="text"]').val()}</div>
            <div class="message-meta row justify-content-between px-3">
                <span class="message-time">در حال ارسال...</span>
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
    </div>`;
                $('.message-items').append(tempMessage);
                $('.chat-body-messages').animate({ scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if(response.message_html) {
                            $(`#${tempMessageId}`).replaceWith(response.message_html);
                            setTimeout(() => {
                                const container = $('.chat-body-messages')[0];
                                // اسکرول به پایین با محاسبه دقیق
                                container.scrollTop = container.scrollHeight;
                            }, 50);
                        }
                        $('#chatForm')[0].reset();
                        $('#file_lbl').text('فایل');
                    },
                    error: function () {
                        $(`#${tempMessageId} .message-meta`).html('<span class="text-danger">ارسال ناموفق</span>');
                    }
                });
            });
        });
        function updateReadStatus() {
            $.ajax({
                url: "{{ route('tickets.getReadMessages', $ticket->id) }}",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if(response.read_messages && response.read_messages.length > 0) {
                        response.read_messages.forEach(function(id) {
                            // فرض کنید در ویو به هر پیام یک id یکتا مثل message-{{ $message->id }} داده شده
                            var messageDiv = $('#message-' + id);
                            // پیدا کردن آیکون وضعیت پیام که هنوز به صورت تک تیک (fa-check) هست
                            var icon = messageDiv.find('.status-sent');
                            if(icon.length) {
                                // تغییر آیکون به دو تیک (fa-check-double) و کلاس status-read
                                icon.removeClass('fa-check').addClass('fa-check-double status-read');
                            }
                        });
                    }
                },
                error: function () {
                    console.log('خطا در بروزرسانی وضعیت خوانده شدن پیام‌ها');
                }
            });
        }
        function fetchNewMessages() {
            // گرفتن آخرین پیام نمایش داده شده
            var lastMessage = $('.message-item').last();
            var lastId = lastMessage.attr('id') ? lastMessage.attr('id').replace('message-', '') : 0;
            $.ajax({
                url: "{{ route('tickets.getNewMessages', $ticket->id) }}",
                type: "GET",
                data: { last_id: lastId },
                dataType: "json",
                success: function (response) {
                    if (response.new_messages) {
                        var newMessages = $(response.new_messages);
                        newMessages.each(function() {
                            var messageId = $(this).attr('id');
                            if (!$('#' + messageId).length) { // اگر پیام با این id وجود نداشته باشد
                                $('.message-items').append($(this));
                            }
                        });
                        updateReadStatus();
                        $('.chat-body-messages').animate({ scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);
                    }
                },
                error: function () {
                    console.log("خطا در دریافت پیام‌های جدید");
                }
            });
        }

        // هر ۵ ثانیه یک بار اجرا شود
        setInterval(fetchNewMessages, 5000);
    </script>
@endsection
