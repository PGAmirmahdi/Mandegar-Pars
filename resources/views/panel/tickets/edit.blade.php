@extends('panel.layouts.master')
@section('title', 'مشاهده تیکت')
@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">

    <style>
        .fa-check-double, .fa-check{
            color: green !important;
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
{{--                        <span class="small text-success">در حال نوشتن ...</span>--}}
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
                            <button type="button" data-toggle="dropdown" class="btn btn-sm  btn-warning btn-floating">
                                <i class="fa fa-cog"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-menu-body">
                                    <ul>
                                        <li>
                                            @if($ticket->status == 'closed')
                                                <a class="dropdown-item" href="{{ route('ticket.changeStatus', $ticket->id) }}">درحال بررسی</a>
                                            @else
                                                <a class="dropdown-item" href="{{ route('ticket.changeStatus', $ticket->id) }}">بسته شده</a>
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
                        @foreach($ticket->messages->whereNotBetween('created_at', [now()->startOfDay()->toDateTimeString(),now()->endOfDay()->toDateTimeString()]) as $message)
                            @if($message->user_id == auth()->id())
                                <div class="message-item {{ $message->file ? 'message-item-media' : '' }}">
                                    {{ $message->text }}
                                    @includeWhen($message->file, 'panel.partials.file-message')
                                    <small class="message-item-date text-muted">
                                        {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                                        @if($message->read_at)
                                            <i class="fa fa-check-double"></i>
                                        @else
                                            <i class="fa fa-check"></i>
                                        @endif
                                    </small>
                                </div>
                            @else
                                <div class="message-item outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
                                    {{ $message->text }}
                                    @includeWhen($message->file, 'panel.partials.file-message')
                                    <small class="message-item-date text-muted">
                                        {{ verta($message->created_at)->format('H:i - Y/m/d') }}
                                    </small>
                                </div>
                            @endif
                        @endforeach
                        @if($ticket->messages->whereBetween('created_at', [now()->startOfDay()->toDateTimeString(),now()->endOfDay()->toDateTimeString()])->count())
                            <div class="message-item message-item-date-border">
                                <span class="badge">امروز</span>
                            </div>
                            @foreach($ticket->messages->whereBetween('created_at', [now()->startOfDay()->toDateTimeString(),now()->endOfDay()->toDateTimeString()]) as $message)
                                @if($message->user_id == auth()->id())
                                    <div class="message-item {{ $message->file ? 'message-item-media' : '' }}">
                                        {{ $message->text }}
                                        @includeWhen($message->file, 'panel.partials.file-message')
                                        <small class="message-item-date text-muted">
                                            {{ verta($message->created_at)->format('H:i') }}
                                            @if($message->read_at)
                                                <i class="fa fa-check-double"></i>
                                            @else
                                                <i class="fa fa-check"></i>
                                            @endif
                                        </small>
                                    </div>
                                @else
                                    <div class="message-item outgoing-message {{ $message->file ? 'message-item-media' : '' }}">
                                        {{ $message->text }}
                                        @includeWhen($message->file, 'panel.partials.file-message')
                                        <small class="message-item-date text-muted">
                                            {{ verta($message->created_at)->format('H:i') }}
                                        </small>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="chat-body-footer">
                    <form action="{{ route('tickets.update', $ticket->id) }}" method="post" class="d-flex align-items-center" enctype="multipart/form-data">
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
            $('#file').on('change', function () {
                $('#file_lbl').text(this.files[0].name)

                $('input[name="text"]').removeAttr('required')
            })
        })
    </script>
@endsection
