<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>

        .div-indicator {
            position: absolute;
            right: 1px;
            top: 16rem;
            padding: 2.3rem !important;
            direction: rtl !important;

        }

        .date {
            position: absolute;
            top: 8.9rem;
            left: 4rem;
            font-size: 1.1rem;

        }

        .number {
            position: absolute;
            top: 11.8rem;
            left: 3rem;
            font-size: 1.1rem;
            width: 8rem;
            text-align: center;
        }

        .attachment {
            position: absolute;
            top: 14.5rem;
            left: 3rem;
            font-size: 1.1rem;
            width: 8rem;
            text-align: center;
        }
    </style>
</head>

<body>

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
    <div>
        {!! $text !!}
    </div>
</div>
</body>
</html>
