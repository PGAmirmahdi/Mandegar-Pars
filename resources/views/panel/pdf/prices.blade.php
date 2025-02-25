<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست قیمت محصولات ماندگار پارس</title>
    <!-- Font Awesome 4 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* بارگزاری فونت B Estedad */
        @font-face {
            font-family: 'B Estedad';
            src: url('{{ asset("assets/fonts/Estedad-Black.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'B Estedad', sans-serif;
            font-size: 16px;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 200px;
            margin: 0 auto;
        }
        .header .logo img {
            width: 100%;
            height: auto;
        }
        .header .brand-info {
            margin-top: 10px;
        }
        .header .brand-info h1 {
            margin: 5px 0;
            font-size: 28px;
            color: #333;
        }
        .header .brand-info p {
            margin: 2px 0;
            font-size: 16px;
            color: #555;
        }
        .header .brand-info a {
            color: #555;
            text-decoration: none;
        }
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #2c3e50;
            color: #fff;
        }
        thead th {
            padding: 12px;
            font-size: 18px;
        }
        tbody td {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        tbody tr:nth-child(even) {
            background-color: #fff;
        }
        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        .footer .sign {
            width: 200px;
        }
        .footer .sign img {
            width: 100%;
            height: auto;
        }
        .footer .contact-info {
            flex: 1;
            margin-right: 20px;
            font-size: 16px;
            color: #333;
        }
        .footer .contact-info p {
            margin: 5px 0;
        }
        .footer .contact-info .icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            border-radius: 50%;
            color: #fff;
            margin-left: 5px;
        }
        .tel-icon {
            background-color: #3498db;
        }
        .mail-icon {
            background-color: #e74c3c;
        }
        .address-icon {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header with logo and brand info -->
    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/media/image/logo.png') }}" alt="لوگو">
        </div>
        <div class="brand-info">
            <h1>لیست قیمت صنایع ماشین های اداری ماندگار پارس</h1>
            <p>website: <a href="http://www.mandegarpars.com">www.mandegarpars.com</a></p>
            <p>ecommerce: <a href="http://www.artintoner.com">www.artintoner.com</a></p>
            <p>email: <a href="mailto:mandegarparsco@gmail.com">mandegarparsco@gmail.com</a></p>
        </div>
    </div>

    <!-- Table -->
    <table>
        <thead>
        <tr>
            <th>ردیف</th>
            <th>دسته بندی</th>
            <th>برند</th>
            <th>مدل</th>
            <th>قیمت (ریال)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->product->category->name }}</td>
                <td>{{ $item->product->productModels->name }}</td>
                <td>{{ $item->product->title }}</td>
                <td>{{ number_format($item->price) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Footer with signature and contact info -->
    <div class="footer">
        <div class="sign">
            <img src="{{ auth()->user()->sign_image }}" alt="امضا">
        </div>
        <div class="contact-info">
            <p>
                <span class="icon tel-icon"><i class="fa fa-phone"></i></span>
                021-65425052, 021-65425053, 021-65425054
            </p>
            <p>
                <span class="icon mail-icon"><i class="fa fa-envelope"></i></span>
                artintoner@gmail.com, mandegarparsco@gmail.com
            </p>
            <p>
                <span class="icon address-icon"><i class="fa fa-map-marker"></i></span>
                آدرس ما: ملارد، صفادشت، شهرک صنعتی صفادشت، بلوار خرداد، بین پنجم و ششم غربی، شرکت صنایع ماشین های اداری ماندگار پارس
            </p>
        </div>
    </div>
</div>
</body>
</html>
