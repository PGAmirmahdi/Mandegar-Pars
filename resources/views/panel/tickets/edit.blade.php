@extends('panel.layouts.master')
@section('title', 'مشاهده تیکت')
@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">
    <style>
        .fa-check-double, .fa-check {
            color: green !important;
        }

        .chat-body-messages {
            background-image: url({{asset('assets/media/image/chat.jpg')}});
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .message-items {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 15px;
        }

        .message-item {
            max-width: 70%;
            position: relative;
            border-radius: 15px;
            padding: 10px 15px;
        }

        .message-item.outgoing {
            align-self: flex-end;
            background: #007bff;
            color: white;
            border-bottom-right-radius: 3px;
        }

        .message-item.incoming {
            align-self: flex-start;
            background: #e9ecef;
            color: black;
            border-bottom-left-radius: 3px;
        }

        .message-item.has-media {
            padding: 5px;
            max-width: 300px;
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
            font-size: 0.75rem;
            margin-top: 5px;
        }

        .message-item.outgoing .message-meta {
            color: rgba(255, 255, 255, 0.7);
        }

        .message-item.incoming .message-meta {
            color: rgba(0, 0, 0, 0.6);
        }

        .status-sent,
        .status-read {
            font-size: 0.65rem;
        }

        .status-read {
            color: #00ff9d;
        }

        .message-text {
            word-wrap: break-word;
            margin-bottom: 3px;
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
                                <div class="message-item outgoing {{ $message->file ? 'has-media' : '' }}">
                                    <div class="message-content">
                                        @if($message->text)
                                            <div class="message-text">{{ $message->text }}</div>
                                        @endif

                                        @includeWhen($message->file, 'panel.partials.file-message')

                                        <div class="message-meta">
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
                                <div class="message-item incoming {{ $message->file ? 'has-media' : '' }}">
                                    <div class="message-content">
                                        @if($message->text)
                                            <div class="message-text">{{ $message->text }}</div>
                                        @endif

                                        @includeWhen($message->file, 'panel.partials.file-message')

                                        <div class="message-meta">
                            <span class="message-time">
                                {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="chat-body-footer">
                    <!-- فرم ارسال پیام با AJAX -->
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
                                <button type="button" data-toggle="dropdown" class="ml-3 btn btn-success btn-floating">
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
                e.preventDefault(); // جلوگیری از رفرش صفحه
                var formData = new FormData(this);
                var url = $(this).attr('action');

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
                        // if(response.message_html) {
                        //     $('.message-items').append(response.message_html);
                        //     // اسکرول به پایین لیست پیام‌ها
                        //     $('.chat-body-messages').animate({ scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);
                        // }

                        // پاکسازی فرم پس از ارسال موفق
                        $('#chatForm')[0].reset();
                        $('#file_lbl').text('فایل');
                    },
                    error: function (xhr) {
                        alert('خطا در ارسال پیام.');
                    }
                });
            });
        });

        function fetchNewMessages() {
            $.ajax({
                url: "{{ route('tickets.getNewMessages', $ticket->id) }}",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.new_messages) {
                        $('.message-items').append(response.new_messages);
                        $('.chat-body-messages').animate({scrollTop: $('.chat-body-messages')[0].scrollHeight}, 500);
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
