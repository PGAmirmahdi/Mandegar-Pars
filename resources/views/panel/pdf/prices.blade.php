<!doctype html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
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
        .No1{
            direction:ltr;
            display:flex;
            flex-direction:row;
            justify-content:start;
            align-items:center;
            width:100% !important;
            p{
                font-size:24px;
            }
            span{
                font-size:16px;
            }
            .No2{
                height:75px !important;
                width:50%;
                display:flex;
                flex-direction:column;
                justify-content:space-between;
                align-items:start;
                margin-left:10px;
                p{
                    margin:0px;
                }
            }
            .b{
                height:75px !important;
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:start;
                margin-left:10px;
                p{
                    font-size:14px;
                    text-align:left !important;
                    direction:ltr;
                }
            }
        }
    </style>
</head>
<body>
<div class="No1">
    <img src="{{asset('assets/media/image/icon1.png')}}" width="fit-content" height="100px" alt="logo" style="margin-bottom:10px;">
</div>
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
<img src="{{asset('assets/media/image/icon2.png')}}" alt="logo2" width="fit-content" style="margin:auto;display:block !important;margin-left:50%;">
<img src="{{asset('assets/media/image/icon3.png')}}" alt="logo3" width="fit-content">
</body>

</html>


