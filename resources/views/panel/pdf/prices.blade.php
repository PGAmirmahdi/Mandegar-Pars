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
    tbody tr:nth-child(odd) {
        background-color: #fff;
    }

    tbody tr:nth-child(even) {
        background-color: #eee;
    }

    td{
        padding: 10px 0 !important;
    }
</style>
<body>
    <table style="text-align: center; width: 100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th style="border-bottom: 2px solid #000; padding-bottom: 10px">ردیف</th>
            <th style="border-bottom: 2px solid #000; padding-bottom: 10px">مدل</th>
            <th style="border-bottom: 2px solid #000; padding-bottom: 10px">قیمت (ریال)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $key => $item)
            <tr style="border-spacing: 1em">
                <td>{{ ++$key }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ number_format($item->{$type}) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>


