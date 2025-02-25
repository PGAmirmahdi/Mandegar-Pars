<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست قیمت محصولات ماندگار پارس</title>
    <style>
        body {
            font-size: larger;
            font-family: 'Tahoma', sans-serif;
        }
        tbody tr:nth-child(odd) {
            background-color: #fff;
        }
        tbody tr:nth-child(even) {
            background-color: #eee;
        }
        td, th {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 100px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ asset('assets/media/image/icon1.png') }}" alt="logo">
    <h2>لیست قیمت محصولات صنایع ماشین های اداری ماندگار پارس</h2>
</div>

<table style="width: 100%; border-collapse: collapse;" class="font-size-16">
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
    <img src="{{ asset('assets/media/image/icon2.png') }}" alt="icon2">
    <img src="{{auth()->user()->sign_image}}" alt="icon3">
</div>

</body>
</html>
