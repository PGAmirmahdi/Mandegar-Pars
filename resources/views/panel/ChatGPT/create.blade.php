@extends('panel.layouts.master')
@section('title', 'گفتگو با ChatGPT')
@section('styles')
    <style>
        .chat-container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #f4f7fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-header {
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .chat-body {
            padding: 20px;
            height: 500px;
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
            background-color: #007bff;
            color: #fff;
        }
        .message-content {
            padding: 10px 15px;
            border-radius: 20px;
            background-color: #f1f1f1;
            max-width: 70%;
        }
        .message-time {
            font-size: 12px;
            margin-top: 5px;
            color: #888;
        }
        .chat-footer {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
        }
        .chat-footer input {
            flex-grow: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }
        .chat-footer button {
            margin-left: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
        }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <div class="chat-header">
            <h4>گفتگو با ChatGPT</h4>
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
            <input type="text" id="chat-input" placeholder="پیام خود را وارد کنید...">
            <button id="send-btn">ارسال</button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // تابع برای اضافه کردن پیام به چت
            function appendMessage(content, isUser = true) {
                const messageClass = isUser ? 'user-message' : '';
                const currentTime = new Date().toLocaleTimeString();

                $('#chat-body').append(`
                    <div class="message ${messageClass}">
                        <div class="message-content">
                            ${content}
                            <div class="message-time">${currentTime}</div>
                        </div>
                    </div>
                `);

                // پیمایش به آخر صفحه چت
                $('#chat-body').scrollTop($('#chat-body')[0].scrollHeight);
            }

            // ارسال درخواست به سرور برای دریافت پاسخ ChatGPT
            $('#send-btn').on('click', function () {
                var prompt = $('#chat-input').val();

                if (prompt.trim() !== '') {
                    // اضافه کردن پیام کاربر به صفحه
                    appendMessage(prompt);

                    // پاک کردن ورودی
                    $('#chat-input').val('');

                    // درخواست AJAX به ChatGPT
                    $.ajax({
                        url: '{{ url('/ask-gpt') }}',
                        type: 'POST',
                        data: {
                            prompt: prompt,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            // نمایش پاسخ ChatGPT در چت
                            const gptResponse = response.choices[0].message.content;
                            appendMessage(gptResponse, false);
                        },
                        error: function () {
                            appendMessage('خطا در دریافت پاسخ.', false);
                        }
                    });
                }
            });

            // ارسال پیام با کلید Enter
            $('#chat-input').on('keypress', function (e) {
                if (e.which === 13) {
                    $('#send-btn').click();
                }
            });
        });
    </script>
@endsection
