<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>

        .div-indicator {
            position: absolute;
            top: 18rem;
            right: 1px;
            padding: 2.3rem !important;
            direction: rtl !important;
            line-height: 3em;
        }
        .date{
            position: absolute;
            top: 8.7rem;
            left: 4rem;
            font-size: 1.4rem;

        }
        .number{
            position: absolute;
            top: 11.5rem;
            left: 3.5rem;
            font-size: 1.3rem;
            width: 8rem;
            text-align: center;
        }
        .attachment{
            position: absolute;
            top: 14.1rem;
            left: 3rem;
            font-size: 1.4rem;
            width: 8rem;
            text-align: center;
        }


    </style>
</head>

<body style="direction: rtl">

    <div class="date">
        {{englishToPersianNumbers($date)}}
    </div>
    <div class="number">
        {{englishToPersianNumbers($number)}}
    </div>
    <div class="attachment">
        {{englishToPersianNumbers($attachment)}}
    </div>



<div class="div-indicator">
    {!! $text !!}
</div>
</body>
</html>
