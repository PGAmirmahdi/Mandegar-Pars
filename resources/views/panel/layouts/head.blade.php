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


    @yield('styles')
</head>
<body>

