<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ماندگار پارس | ورود</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/media/image/favicon.png">

    <!-- Theme Color -->
    <meta name="theme-color" content="#5867dd">

    <!-- Plugin styles -->
    <link rel="stylesheet" href="vendors/bundle.css" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="assets/css/app.css" type="text/css">

    <link rel="manifest" href="/manifest.json">
    <!-- Najva Push Notification -->
    <script type="text/javascript">
        (function(){
            var now = new Date();
            var version = now.getFullYear().toString() + "0" + now.getMonth() + "0" + now.getDate() +
                "0" + now.getHours();
            var head = document.getElementsByTagName("head")[0];
            var link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = "https://van.najva.com/static/cdn/css/local-messaging.css" + "?v=" + version;
            head.appendChild(link);
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.async = true;
            script.src = "https://van.najva.com/static/js/scripts/new-website436970-website-54287-1faec3c1-6f27-4881-b219-5f5b5737f31b.js" + "?v=" + version;
            head.appendChild(script);
        })()
    </script>
    <!-- END NAJVA PUSH NOTIFICATION -->
</head>
<style>
    #captcha_sec img{
        cursor: pointer;
    }
    #captcha_sec input{
        text-align: center !important;
        letter-spacing: 1rem;
    }
</style>
<body class="form-membership">

<!-- begin::page loader-->
@include('panel.layouts.loader')
<!-- end::page loader -->

<div class="form-wrapper">

    <!-- logo -->
    <div class="logo">
        <img src="assets/media/image/logo-sm.png" alt="image">
    </div>
    <!-- ./ logo -->

    <h5>ورود</h5>

    <!-- form -->
    <form action="{{ route('login') }}" method="post">
        @csrf
        <div class="form-group">
            <input type="text" name="phone" class="form-control text-left" placeholder="شماره موبایل" dir="ltr" required autofocus>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control text-left" placeholder="رمز عبور" dir="ltr" required>
        </div>
        <div class="form-group" id="captcha_sec">
            {!! captcha_img() !!}
            <input type="text" name="captcha_code" class="form-control text-left mt-2 mb-0" placeholder="کد امنیتی" dir="ltr" required autofocus>
            @error('captcha_code')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button class="btn btn-primary btn-block">ورود</button>
        @error('phone')
        <span class="invalid-feedback d-block" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </form>
    <!-- ./ form -->

</div>

<!-- Plugin scripts -->
<script src="vendors/bundle.js"></script>

<!-- App scripts -->
<script src="assets/js/app.js"></script>

<script>
    $(document).ready(function () {
        $(document).on('click', '#captcha_sec img', function (){
            $.ajax({
                type: 'get',
                url: '/captcha/api',
                success: function (res){
                    $('#captcha_sec img').attr('src',res.img)
                    // console.log($(this))
                }
            })
        })
    })
</script>
</body>

</html>
