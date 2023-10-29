<!doctype html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<style>
    body{
        font-size: larger;
    }
</style>
<body>
    <table style="text-align: center; width: 100%">
        <thead>
        <tr>
            <th style="border-bottom: 2px solid #000; padding-bottom: 10px">مدل</th>
            <th style="border-bottom: 2px solid #000; padding-bottom: 10px">قیمت (ریال)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>{{ number_format($item->system_price) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>


