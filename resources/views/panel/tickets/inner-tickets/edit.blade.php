@extends('panel.layouts.master')
@section('title', 'مشاهده تیکت')
@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">
    <style>
        .fa-check-double, .fa-check {
            color: #00ff00 !important;
        }

        body {
            overflow: hidden !important;
        }
        .main-content{
            padding: 10px !important;
        }
        .chat-app-wrapper{
         margin: 0px !important;
        }
        .chat-body-messages {
            background-image: url({{asset('assets/media/image/chat.jpg')}});
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: 100% !important;
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
            background: linear-gradient(70deg, rgba(217,104,197,0.4) 0%, rgba(69,0,108,0.6) 100%) !important;
            box-shadow: 1px 1px 4px -1px #494949;
            backdrop-filter: blur(4px);
            border-radius: 5px !important;
            padding: 0px !important;
        }

        .message-time {
            font-size: 0.65rem !important;
            color: #8a8a8a !important;
            margin-left: 30px;
        }

        .No2 {
            box-shadow: -1px 1px 4px -1px #494949 !important;
        }

        .outgoing-message {
            background: linear-gradient(90deg, rgba(238,174,202,0.6) 0%, rgba(148,187,233,0.4) 100%) !important;
            backdrop-filter: blur(4px);
            min-width: 200px !important;
            padding: 2px 8px !important;

            .message-text {
                color: #fff !important;
            }

            .message-time {
                margin: 0px;
                color: #461c70 !important;
            }
        }

        .fa-check-double {
            color: #34b7f1;
        }

        img {
            max-width: 200px !important;
        }

        .message-content {
            padding: 2px 8px;
            min-width: 200px;
        }

        .fa-check, .fa-check-double {
            font-size: 0.65rem !important;
        }

        .chat-app {
            height: 85vh;
        }

        .chat-body-messages {
            height: 70vh !important;
            overflow-y: auto !important;
        }

        .message-items {
            min-height: min-content; /* اطمینان از رشد صحیح محتوا */
        }

        .message-meta {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-right: 15px !important;
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
                        <div id="typing-indicator" style="display:none; margin: 10px; color: #fff;font-size: 10px"
                             class="text-dark">
                            <em>در حال تایپ...</em>
                        </div>
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
                                <div id="message-{{ $message->id }}"
                                     class="message-item {{ $message->file ? 'message-item-media' : '' }}">
                                    <div class="message-content">
                                        @if($message->text)
                                            <div class="message-text">{{ $message->text }}</div>
                                        @endif
                                        @includeWhen($message->file, 'panel.partials.file-message')
                                        <div
                                            class="message-meta row @if($message->file) justify-content-between m-2 @else justify-content-between @endif px-3">
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
                                <div id="message-{{ $message->id }}"
                                     class="message-item No2 outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
                                    @if($message->text)
                                        <div
                                            class="message-text @if($message->file) p-2 @endif">{{ $message->text }}</div>
                                    @endif
                                    @includeWhen($message->file, 'panel.partials.file-message')
                                    <div
                                        class="message-meta row @if($message->file) justify-content-center m-2 @else justify-content-between @endif px-3">
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
        {{--function updateReadStatus() {--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ route('tickets.getReadMessages', $ticket->id) }}",--}}
        {{--        type: "GET",--}}
        {{--        dataType: "json",--}}
        {{--        success: function (response) {--}}
        {{--            if (response.read_messages && response.read_messages.length > 0) {--}}
        {{--                response.read_messages.forEach(function (id) {--}}
        {{--                    // فرض کنید در ویو به هر پیام یک id یکتا مثل message-{{ $message->id }} داده شده--}}
        {{--                    var messageDiv = $('#message-' + id);--}}
        {{--                    // پیدا کردن آیکون وضعیت پیام که هنوز به صورت تک تیک (fa-check) هست--}}
        {{--                    var icon = messageDiv.find('.status-sent');--}}
        {{--                    if (icon.length) {--}}
        {{--                        // تغییر آیکون به دو تیک (fa-check-double) و کلاس status-read--}}
        {{--                        icon.removeClass('fa-check').addClass('fa-check-double status-read');--}}
        {{--                    }--}}
        {{--                });--}}
        {{--            }--}}
        {{--        },--}}
        {{--        error: function () {--}}
        {{--            console.log('خطا در بروزرسانی وضعیت خوانده شدن پیام‌ها');--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        var currentUserId = {{ auth()->id() }};
        var ticketId = {{ $ticket->id }};
        let typingTimer;
        const typingDelay = 1000; // ۱ ثانیه تا تایپ قطع شود
        let isTyping = false;

        $('input[name="text"]').on('input', function () {
            // اگر کاربر تازه شروع به تایپ کرده و هنوز isTyping false است
            if (!isTyping) {
                isTyping = true;
                // ارسال رویداد تایپ به سرور (broadcast) فقط یکبار در ابتدای تایپ
                $.ajax({
                    url: "{{ route('chat.typing') }}",
                    type: "POST",
                    data: {ticket_id: ticketId},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            }
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                // پس از ۱ ثانیه بدون تایپ، وضعیت تایپ را به false برگردانیم
                isTyping = false;
            }, typingDelay);
        });
        window.Echo.channel('ticket.' + ticketId)
            .listen('.MessageReadEvent', (event) => {
                if (event.read_messages && event.read_messages.length > 0) {
                    event.read_messages.forEach(function (id) {
                        // انتخاب المان پیام با آیدی مشخص
                        var messageDiv = $('#message-' + id);
                        // تغییر آیکون از تک تیک به دو تیک
                        var icon = messageDiv.find('.status-sent');
                        if (icon.length) {
                            icon.removeClass('fa-check').addClass('fa-check-double status-read');
                        }
                    });
                }
            });

        window.Echo.channel('ticket.' + ticketId)
            .listen('.TypingEvent', (event) => {
                if (event.user_id != currentUserId) {
                    // نمایش indicator برای تایپ
                    $('#typing-indicator').fadeIn();
                    // استفاده از تایمر برای مخفی کردن indicator بعد از چند ثانیه
                    clearTimeout(window.typingTimeout);
                    window.typingTimeout = setTimeout(() => {
                        $('#typing-indicator').fadeOut();
                    }, 3000);
                }
            });

        // تابع کمکی برای قالب‌بندی سایز فایل
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        window.Echo.channel('ticket.' + ticketId)
            .listen('.NewMessageEvent', (event) => {
                var message = event.message;
                var messageHtml = '';
                var formattedDate = event.formatted_date;
                var fileHtml = '';

                // در صورت وجود فایل، بررسی نوع و ساخت HTML مناسب
                if (message.file) {
                    // اگر فایل به صورت رشته (JSON) ارسال شده، ابتدا آن را پارس می‌کنیم
                    let file = (typeof message.file === "string") ? JSON.parse(message.file) : message.file;
                    if (['jpg','jpeg','png','webp','svg','gif'].includes(file.type)) {
                        fileHtml += `<ul class="w-100">
                                <li class="flex-column justify-content-center align-items-center w-100">
                                    <a href="${file.path}">
                                        <img src="${file.path}" alt="image" class="w-100 p-2">
                                        <span>${file.name}</span>
                                    </a>
                                </li>
                             </ul>`;
                    } else {
                        fileHtml += `<div class="m-b-0 text-muted text-left media-file">
                                <a href="${file.path}" class="btn btn-outline-light row text-left align-items-center justify-content-center h-50 w-100" download="${file.path}">
                                    <i class="fa fa-download font-size-18 m-r-10"></i>
                                    <div class="small">
                                        <div class="mb-2">${file.name}</div>
                                        <div class="font-size-13" dir="ltr">${formatBytes(file.size)}</div>
                                    </div>
                                </a>
                             </div>`;
                    }
                }

                // رندر پیام بر اساس اینکه کاربر فرستنده است یا دریافت‌کننده
                if (message.user_id == currentUserId) {
                    messageHtml += `<div id="message-${message.id}" class="message-item ${message.file ? 'message-item-media' : ''}">
                                <div class="message-content">`;
                    if (message.text) {
                        messageHtml += `<div class="message-text">${message.text}</div>`;
                    }
                    // اضافه کردن قسمت فایل در صورت وجود
                    if(fileHtml !== '') {
                        messageHtml += fileHtml;
                    }
                    messageHtml += `<div class="message-meta">
                                <span class="message-time">${formattedDate}</span>`;
                    if (message.read_at) {
                        messageHtml += `<i class="status-read fa fa-check-double"></i>`;
                    } else {
                        messageHtml += `<i class="status-sent fa fa-check"></i>`;
                    }
                    messageHtml += `   </div>
                            </div>
                        </div>`;
                } else {
                    messageHtml += `<div id="message-${message.id}" class="message-item outgoing-message ${message.file ? 'message-item-media' : ''}">`;
                    if (message.text) {
                        messageHtml += `<div class="message-text ${message.file ? 'p-2' : ''}">${message.text}</div>`;
                    }
                    // اضافه کردن قسمت فایل در صورت وجود
                    if(fileHtml !== '') {
                        messageHtml += fileHtml;
                    }
                    messageHtml += `<div class="message-meta row ${message.file ? 'justify-content-center m-2' : 'justify-content-between'} px-2">
                                <span class="message-time">${formattedDate}</span>
                            </div>
                        </div>`;
                }
                // اضافه کردن پیام جدید به صفحه چت
                $('.message-items').append(messageHtml);
                $('.chat-body-messages').animate({scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);
            });
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
                $('.chat-body-messages').animate({scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);

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
                        if (response.message_html) {
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
        {{--function fetchNewMessages() {--}}
        {{--    // گرفتن آخرین پیام نمایش داده شده--}}
        {{--    var lastMessage = $('.message-item').last();--}}
        {{--    var lastId = lastMessage.attr('id') ? lastMessage.attr('id').replace('message-', '') : 0;--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ route('tickets.getNewMessages', $ticket->id) }}",--}}
        {{--        type: "GET",--}}
        {{--        data: { last_id: lastId },--}}
        {{--        dataType: "json",--}}
        {{--        success: function (response) {--}}
        {{--            if (response.new_messages) {--}}
        {{--                var newMessages = $(response.new_messages);--}}
        {{--                newMessages.each(function() {--}}
        {{--                    var messageId = $(this).attr('id');--}}
        {{--                    if (!$('#' + messageId).length) { // اگر پیام با این id وجود نداشته باشد--}}
        {{--                        $('.message-items').append($(this));--}}
        {{--                    }--}}
        {{--                });--}}
        {{--                updateReadStatus();--}}
        {{--                $('.chat-body-messages').animate({ scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);--}}
        {{--            }--}}
        {{--        },--}}
        {{--        error: function () {--}}
        {{--            console.log("خطا در دریافت پیام‌های جدید");--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        {{--// هر ۵ ثانیه یک بار اجرا شود--}}
        {{--setInterval(fetchNewMessages, 5000);--}}
    </script>
@endsection
