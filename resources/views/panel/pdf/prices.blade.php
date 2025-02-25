<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست قیمت محصولات ماندگار پارس</title>
    <style>
        body {
            font-family: 'B Nazanin', sans-serif;
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
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .logo {
            width: 120px;
            margin-left: 10px;
        }
        .header .logo img {
            width: 100%;
            height: auto;
        }
        .header .title {
            flex: 1;
            text-align: center;
        }
        .header .title h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
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
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        .footer .sign {
            width: 150px;
        }
        .footer .sign img {
            width: 100%;
            height: auto;
        }
        .footer .icon {
            width: 100px;
        }
        .footer .icon img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/media/image/icon1.png') }}" alt="لوگو">
        </div>
        <div class="title">
            <h2>لیست قیمت محصولات صنایع ماشین های اداری ماندگار پارس</h2>
        </div>
    </div>

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

    <div class="footer">
        <div class="sign">
            <img src="{{ auth()->user()->sign_image }}" alt="امضا">
        </div>
        <div class="icon">
            <img src="{{ asset('assets/media/image/icon3.png') }}" alt="آیکون">
        </div>
    </div>
</div>
</body>
</html>
