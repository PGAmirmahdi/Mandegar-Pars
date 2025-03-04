@extends('panel.layouts.master')
@section('title', 'گفتگو با هوش مصنوعی ماندگار')
@section('styles')
    <style>
        .chat-container {
            max-width: 100%;
            margin: 20px auto;
            background-color: #f4f7fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-header {
            background-color: rgb(85, 0, 255);
            color: #fff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .chat-body {
            background-image: url("{{ asset('assets/media/image/Ai.jpg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            padding: 20px;
            height: 625px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .message.user-message {
            justify-content: flex-end;
        }
        .message.user-message .message-content {
            background-color: rgba(85, 0, 255, 0.37);
            color: #fff;
            backdrop-filter: blur(6.9px);
        }
        .message-content {
            padding: 10px 15px;
            border-radius: 20px;
            background-color: rgba(0, 255, 255, 0.46);
            max-width: 70%;
            color: #fff;
            backdrop-filter: blur(6.9px);
        }
        .message-time {
            font-size: 12px;
            margin-top: 5px;
            color: #ffffff;
        }
        .chat-footer {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
            position: relative;
        }
        .chat-footer input {
            flex-grow: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            padding-right: 50px; /* فضای کافی برای دکمه */
        }
        .chat-footer button {
            position: absolute;
            left: 35px;
            top: 50%;
            transform: translateY(-50%);
            width: 65px;
            height: 65px;
            background-color: rgba(85, 0, 255, 0.37);
            color: #fff;
            border: none;
            border-radius: 20px;
        }

        /* استایل برای انیمیشن تایپینگ */
        .typing-indicator {
            display: inline-block;
        }
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 3px;
            background-color: #ccc;
            border-radius: 50%;
            animation: blink 1.4s infinite both;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes blink {
            0% { opacity: 0.2; }
            20% { opacity: 1; }
            100% { opacity: 0.2; }
        }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <div class="chat-header">
            @if($messages->isNotEmpty())
                <span> گفت و گوی {{ $messages->first()->user->fullName() }} با هوش مصنوعی</span>
            @else
                <span>گفتگو با هوش مصنوعی</span>
            @endif
        </div>
        <div class="chat-body" id="chat-body">
            @foreach ($messages as $message)
                <div class="message {{ $message->is_user_message ? 'user-message' : '' }}">
                    <div class="message-content">
                        {{ $message->message }}
                        <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="امکان ارسال پیام وجود ندارد" disabled>
            <button id="send-btn" class="btn btn-floating btn-primary row justify-content-center align-items-center" disabled>
                <i class="fa-brands fa-telegram font-size-30"></i>
            </button>
        </div>
    </div>
@endsection

@section('scripts')

@endsection
