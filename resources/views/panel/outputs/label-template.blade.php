<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>لیبل سفارش</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .label {
            width: 600px;
            border: 1px solid #000;
            padding: 20px;
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
    </style>
</head>
<body>
<div class="label">
    <div class="row">
        <div class="d-flex flex-column" style="gap: 10px;">
            <img src="{{ asset('/assets/media/image/logo-192x192.png') }}" alt="Logo" width="100" height="100">
            <span class="font-weight-bolder text-center">صنایع ماشین های اداری</span>
            <h4 class="text-center">ماندگار پارس</h4>
        </div>
        <div>
            <h5>فرستنده: ماندگار پارس</h5>
            <h5>شماره تماس: 09029463357</h5>
            <span>آدرس: تهران، صفادشت، شهرک صنعتی صفادشت<br>خیابان خرداد، بین خیابان 5 و 6 غربی، پلاک 212</span>
            <h6 class="font-weight-bold mt-4">((با تشکر از خرید شما))</h6>
        </div>
    </div>
    <!-- اطلاعات مشتری -->
    <div class="flex-column">
        <p>گیرنده: {{ $invoice->customer->name }}</p>
        <p>شماره تماس: {{ $invoice->customer->phone1 }}</p>
        <p>کد پستی: {{ $invoice->customer->postal_code }}</p>
        <p>آدرس: {{ $invoice->customer->address1 }}</p>
    </div>
</div>
</body>
</html>
