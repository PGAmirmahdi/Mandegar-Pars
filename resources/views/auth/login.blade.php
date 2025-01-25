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
    <script type="text/javascript">
        (function () {
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
    <script type="module" src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@0.1.8/site.min.js" async
            defer></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@0.1.8/site.compat.min.js" async
            defer></script>
</head>

<style>
    #captcha_sec img {
        cursor: pointer;
    }
    #captcha_sec{
        z-index: 9999;
    }
    .frc-captcha{
        z-index: 9999 !important;
    }
    #captcha_sec input {
        text-align: center !important;
        letter-spacing: 1rem;
    }
    iframe {
        width: 100%; /* عرض کامل */
        height: 80px; /* ارتفاع مورد نظر */
        margin:0px;
        position:relative !important;
    }
    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
        .form-wrapper{
            background-color: transparent !important;
        }
    }

    .video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .logo {
        margin-left: 20px;
        flex-shrink: 0;
    }

    .logo img {
        width: 220px;
    }

    form {
        background-color: transparent; /* حذف رنگ پس‌زمینه */
        width: 100%;
        height: 100%;
    }

    .form-container {
        margin-right: 20px;
        width: 500px;
    }

    .form-wrapper {
        margin-top: 100px;
        padding: 0px !important;
        opacity: 0;
        transition: opacity 1s;
        display: flex;
        flex-direction: row; /* جهت افقی برای فرم و تصویر */
        justify-content: space-between; /* فاصله مناسب بین فرم و تصویر */
        align-items: center; /* ترازبندی عمودی */
        background-color: rgba(76, 26, 122, 0.2); /* رنگ با شفافیت */
        backdrop-filter: blur(15px); /* افکت blur */
        -webkit-backdrop-filter: blur(15px); /* پشتیبانی از مرورگرهای وب‌کیت */
        border-radius: 15px; /* گوشه‌های گرد */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* سایه */
        width: 700px !important;
    }

    .form-container {
        padding: 50px !important;
        display: flex;
        flex-direction: column;
        align-items: center; /* تنظیم فرم در مرکز عمودی */
        justify-content: center;
        width: 50%; /* عرض فرم */
    }

    .form-wrapper.slide-down {
        animation: slideDown 1.5s ease forwards;
    }

    @keyframes slideDown {
        0% {
            transform: translateY(20%);
            opacity: 0;
        }
        100% {
            transform: translateY(0%);
            opacity: 1;
        }
    }

    .form-group {
        position: relative;
        margin-bottom: 20px;
    }

    .form-group label {
        text-align: center;
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        color: #999;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .right{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 500px !important;
    }

    .form-group input:focus {
        border-color: #5d4a9c;
    }

    .form-group input:focus + label,
    .form-group input:not(:placeholder-shown) + label {
        top: -10px;
        font-size: 12px;
        color: #5d4a9c;
    }

    .form-group input:focus + label {
        font-weight: bold;
    }

    /* تغییرات برای صفحات موبایل */
    @media (max-width: 768px) {
        .form-wrapper {
            flex-direction: column; /* چیدمان عمودی فرم و تصویر */
            align-items: center; /* وسط چین کردن */
            width: 100% !important;
        }

        .right {
            display: none; /* مخفی کردن تصویر سمت راست در صفحه موبایل */
        }

        .form-container {
            width: 100%; /* عرض فرم به 100% برای صفحه موبایل */
            padding: 20px; /* کاهش فاصله در موبایل */
        }

        .form-group label {
            color: #fff !important; /* تغییر رنگ لیبل‌ها به سفید */
        }

        .video-background {
            object-fit: cover; /* کشیدن ویدیو به اندازه صفحه */
        }

        .form-wrapper {
            margin-top: 20px; /* کاهش فاصله بالای فرم در موبایل */
            width: 90% !important; /* عرض فرم در موبایل */
        }

        .form-group input {
            font-size: 16px; /* افزایش اندازه فونت ورودی در موبایل */
        }
    }
</style>

<body class="form-membership">

<video class="video-background" autoplay muted playsinline>
    <source src="{{ asset('assets/media/login.mp4') }}" type="video/mp4">
    <img src="assets/media/image/fallback.jpg" alt="ویدیو پشتیبانی نمی‌شود.">
</video>

<div class="form-wrapper">
    <div class="bg-white right">
        <img src="assets/media/image/login.png" alt="عکس سمت راست"  width="300px">
    </div>
    <div class="form-container">
        <form action="{{ route('login') }}" method="post">
            @csrf
            <div>
                <img src="assets/media/image/header-logo.png" width="250px" alt="لوگو">
            </div>
            <h5 class="text-primary">ورود</h5>
            <div class="form-group">
                <input type="text" name="phone" id="phone" class="form-control text-left" placeholder="" dir="ltr" required autofocus>
                <label for="phone">شماره موبایل</label>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control text-left" placeholder="" dir="ltr" required autocomplete="off">
                <label for="password">رمز عبور</label>
            </div>
            <div class="frc-captcha" data-sitekey="{!! env('FRIENDLY_CAPTCHA_SITEKEY') !!}" style="width:100vw;"></div>
            <div class="form-group" id="captcha_sec">
                @error('frc-captcha-response')
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
    </div>
</div>

<!-- Plugin scripts -->
<script src="vendors/bundle.js"></script>

<!-- App scripts -->
<script src="assets/js/app.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@0.1.8/site.compat.min.js" async
        defer></script>
<script>
    $(document).ready(function () {
        $(document).on('click', '#captcha_sec img', function () {
            $.ajax({
                type: 'get',
                url: '/captcha/api',
                success: function (res) {
                    $('#captcha_sec img').attr('src', res.img)
                    // console.log($(this))
                }
            })
        })
    })
    document.querySelector('form').addEventListener('submit', function (event) {
        const captchaResponse = document.querySelector('input[name="frc-captcha-response"]');
        if (captchaResponse.value === '.ACTIVATED') {
            captchaResponse.value = ''; // مقدار را خالی کنید
        } else if (captchaResponse.value === '.UNACTIVATED') {
            captchaResponse.value = ''; // مقدار را خالی کنید
        }
    });
    setTimeout(() => {
        const formWrapper = document.querySelector('.form-wrapper');
        formWrapper.style.opacity = '1';
        formWrapper.classList.add('slide-down');
    }, 4000);

    const video = document.querySelector('.video-background');
    video.addEventListener('ended', () => {
        video.pause();
    });
</script>

</body>
</html>
