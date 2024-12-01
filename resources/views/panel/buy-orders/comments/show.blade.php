@extends('panel.layouts.master')

@section('title', 'مشاهده سفارش خرید')

@section('styles')
    <!-- lightbox -->
    <link rel="stylesheet" href="/vendors/lightbox/magnific-popup.css" type="text/css">

    <style>
        .fa-check-double, .fa-check {
            color: green !important;
        }
        .avatar {
            width: 30px; /* اندازه کوچک عکس پروفایل */
            height: 30px;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    <div class="card chat-app-wrapper">
        <div class="row chat-app">
            <div class="col-xl-12 col-md-12 chat-body">
                <div class="chat-body-header">
                    <div>
                        <h6 class="mb-1 primary-font line-height-18">
                            سفارش خرید شماره {{ $order->id }} - {{ $order->customer->name }}
                        </h6>
                    </div>
                    <div class="ml-auto d-flex">
                        <div class="mr-4">
                            <span class="badge badge-warning">{{ $order->status }}</span>
                        </div>
                    </div>
                </div>
                <div class="chat-body-messages">
                    <div class="message-items">
                        @foreach ($comments as $comment)
                            <div class="message-item {{ $comment->user_id == auth()->id() ? '' : 'outgoing-message' }}">
                                <!-- نمایش تصویر پروفایل -->
                                <figure class="avatar avatar-sm m-r-10">
                                    <img src="{{ $comment->user->profile ? asset('storage/'.$comment->user->profile) : asset('assets/media/image/avatar.png') }}" class="rounded-circle" alt="profile">
                                </figure>
                                <strong>{{ $comment->user->name }}:</strong>
                                <p>{{ $comment->comment }}</p>
                                <small class="message-item-date text-muted">
                                    {{ verta($comment->created_at)->format('H:i - Y/m/d') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="chat-body-footer">
                    <form action="{{ route('buy-orders.comments.store', $order->id) }}" method="post" class="d-flex align-items-center">
                        @csrf
                        <input type="text" name="comment" class="form-control" placeholder="نظر خود را وارد کنید..." required>
                        <button type="submit" class="ml-3 btn btn-primary btn-floating">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/vendors/lightbox/jquery.magnific-popup.min.js"></script>
    <script src="/assets/js/examples/lightbox.js"></script>
@endsection
