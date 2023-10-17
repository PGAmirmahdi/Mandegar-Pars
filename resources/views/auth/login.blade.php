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
</head>

<body class="form-membership">

<!-- begin::page loader-->
<div class="page-loader">
    <div class="spinner-border"></div>
</div>
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
</body>

</html>
