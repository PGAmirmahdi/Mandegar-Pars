<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>لیبل سفارش</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        /* تعریف فونت با استفاده از @font-face */
        @font-face {
            font-family: 'MyFont';
            src: url("{{ asset('assets/fonts/farsi-fonts/vazir-400.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'MyFontBold';
            src: url("{{ asset('assets/fonts/farsi-fonts/vazir-700.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        /* استفاده از فونت تعریف‌شده برای کل صفحه */
        body {
            font-family: 'MyFont', sans-serif;
        }

        /* تنظیم عنصر لیبل به صورت مربع 600px x 600px */
        .label {
            width: 600px;
            height: 600px;
            margin: 20px auto;
            box-sizing: border-box;
            padding: 10px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .flex-column p {
            margin: 5px 0;
        }

        .text-center {
            text-align: center;
        }

        .row {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-around;
        }

        .No2 {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .khat {
            width: 90%;
            height: 90%;
            padding: 25px;
            /*border: 2px dashed #333;*/
        }

        #downloadBtn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            font-family: sans-serif;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .out {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
<!-- لیبل سفارش -->
<div class="label p-2" id="label">
    <div class="out">
        <div class="khat">
            <div class="row justify-content-center align-items-center w-100">
                <div class="d-flex flex-column justify-content-center align-items-center No2" style="gap: 0px;">
                    <img src="{{ asset('/assets/media/image/logo-lg.png') }}" alt="Logo" width="130" height="125">
                    <p class="font-weight-bolder text-center">صنایع ماشین های اداری</p>
                    <h1 class="text-center" style="margin: 0px;font-family:'MyFontBold' ">ماندگار پارس</h1>
                </div>
                <div style="display: flex;flex-direction: column;justify-content: start;align-items: start;gap: 5px">
                    <p style="font-size: 28px;margin: 0px">فرستنده: ماندگار پارس</p>
                    <p style="font-size: 23px;margin: 0px">شماره تماس: 09029463357</p>
                    <span>
                    آدرس: تهران، صفادشت، شهرک صنعتی صفادشت<br>
                    خیابان خرداد، بین خیابان 5 و 6 غربی، پلاک 212
                </span>
                    <h5 style="font-family: 'MyFontBold';margin: 5px;width: 100%;display: flex;flex-direction: row;justify-content:center;align-items: center;">((با تشکر از خرید شما))</h5>
                </div>
            </div>
            <!-- اطلاعات مشتری -->
            <div class="flex-column" style="padding-right: 20px">
                <p style="font-size: 18px">گیرنده: {{ $invoice->customer->name }}</p>
                <p style="font-size: 18px">شماره تماس: {{ $invoice->customer->phone1 }}</p>
                <p style="font-size: 18px">کد پستی: {{ $invoice->customer->postal_code }}</p>
                <p style="font-size: 18px">آدرس: {{ $invoice->customer->address1 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- دکمه دانلود -->
<button id="downloadBtn" style="padding-top: 10px !important;">دانلود لیبل به صورت تصویر</button>

<!-- اضافه کردن html2canvas از CDN -->
<script src="{{asset('assets/js/html2canvas.min.js')}}"></script>
<script>
    document.getElementById('downloadBtn').addEventListener('click', function () {
        html2canvas(document.getElementById('label')).then(function (canvas) {
            var imgData = canvas.toDataURL("image/png");
            var downloadLink = document.createElement('a');
            downloadLink.href = imgData;
            downloadLink.download = "label.png";
            // اضافه کردن لینک به بدنه، کلیک خودکار و سپس حذف آن
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        });
    });
</script>
</body>
</html>
