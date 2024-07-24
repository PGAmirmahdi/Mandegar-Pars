<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/media/image/favicon.png">

    <!-- Theme Color -->
    <meta name="theme-color" content="#5867dd">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Plugin styles -->
    <link rel="stylesheet" href="/vendors/bundle.css" type="text/css">

    <!-- Datepicker -->
    <link rel="stylesheet" href="/vendors/datepicker/daterangepicker.css">

    <!-- Clockpicker -->
    <link rel="stylesheet" href="/vendors/clockpicker/bootstrap-clockpicker.min.css" type="text/css">

    <!-- Slick -->
    <link rel="stylesheet" href="/vendors/slick/slick.css">
    <link rel="stylesheet" href="/vendors/slick/slick-theme.css">

    <!-- Vmap -->
    <link rel="stylesheet" href="/vendors/vmap/jqvmap.min.css">

    <!-- App styles -->
    <link rel="stylesheet" href="/assets/css/app.css" type="text/css">

    <!-- sweet alert -->
    <script src="/assets/js/sweetalert.min.js"></script>

    <link rel="stylesheet" href="/vendors/select2/css/select2.min.css" type="text/css">

    <!-- Datepicker -->
    <link rel="stylesheet" href="/vendors/datepicker-jalali/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/vendors/datepicker/daterangepicker.css">

    <!-- DataTable -->
    <link rel="stylesheet" href="/vendors/dataTable/responsive.bootstrap.min.css" type="text/css">

    <style>
        .dataTable th{
            cursor: pointer !important;
        }

        .zoom-in-out {
            animation: zoom-in-zoom-out 1s ease infinite;
        }

        @keyframes zoom-in-zoom-out {
            0% {
                transform: scale(1, 1);
            }
            50% {
                transform: scale(1.5, 1.5);
            }
            100% {
                transform: scale(1, 1);
            }
        }
    </style>

    @yield('styles')

    <link rel="manifest" href="/manifest.json">
    <!-- Najva Push Notification -->
{{--    <script type="text/javascript">--}}
{{--        (function(){--}}
{{--            var now = new Date();--}}
{{--            var version = now.getFullYear().toString() + "0" + now.getMonth() + "0" + now.getDate() +--}}
{{--                "0" + now.getHours();--}}
{{--            var head = document.getElementsByTagName("head")[0];--}}
{{--            var link = document.createElement("link");--}}
{{--            link.rel = "stylesheet";--}}
{{--            link.href = "https://van.najva.com/static/cdn/css/local-messaging.css" + "?v=" + version;--}}
{{--            head.appendChild(link);--}}
{{--            var script = document.createElement("script");--}}
{{--            script.type = "text/javascript";--}}
{{--            script.async = true;--}}
{{--            script.src = "https://van.najva.com/static/js/scripts/new-website436970-website-54287-1faec3c1-6f27-4881-b219-5f5b5737f31b.js" + "?v=" + version;--}}
{{--            head.appendChild(script);--}}
{{--        })()--}}
{{--    </script>--}}
    <!-- END NAJVA PUSH NOTIFICATION -->

{{--    <script>--}}
{{--        window.najvaUserSubscribed = function(najva_user_token){--}}
{{--            $.ajax({--}}
{{--                url: '/panel/najva_token',--}}
{{--                type: 'post',--}}
{{--                data: {najva_user_token},--}}
{{--                success: function (res){--}}
{{--                    console.log(res.data)--}}
{{--                }--}}
{{--            })--}}
{{--        }--}}
{{--    </script>--}}

</head>
<body>

