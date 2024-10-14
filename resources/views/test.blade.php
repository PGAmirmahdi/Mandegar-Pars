<!DOCTYPE html>
<html>
<head>
    <title>Test Event</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<h1>{{ $message }}</h1>
</body>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

</script>
<script>
    // افزودن توکن CSRF به درخواست‌های Ajax

    var userId = {{ Auth::user()->id }}; // شناسه کاربر لاگین شده

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        encrypted: true,
        authEndpoint: '/pusher/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // ارسال توکن CSRF
            }
        }
    });

    var channel = pusher.subscribe('private-notification.' + userId);
    channel.bind('App\\Events\\SendMessage', function(data) {
        console.log('Notification received: ', data.message);
    });

</script>
</html>
