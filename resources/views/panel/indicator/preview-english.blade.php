<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: dana;
            font-style: normal;
            font-weight: 400;
            font-display: auto;
            src: url({{asset('assets/fonts/farsi-fonts/sahel-300.eot')}}) format("embedded-opentype"),
            url({{asset('assets/fonts/farsi-fonts/sahel-300.woff2')}}) format("woff2"),
            url({{asset('assets/fonts/farsi-fonts/sahel-300.woff')}}) format("woff"),
            url({{asset('assets/fonts/farsi-fonts/sahel-300.ttf')}}) format("truetype")
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, h1, h2, h3, h4, h5, h6, p, ul, ol, li, table, div {
            margin: 0;
            padding: 0;
            border: 0;
            vertical-align: baseline;
        }


        body {
            font-family: dana, Sans-Serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }

        .a4-page {
            width: 21cm;
            height: 29.7cm;
            background-image: url('{{ asset('/assets/images/english-header.png') }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            /*box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);*/
            /*-webkit-print-color-adjust: exact;*/
            overflow-y: auto;
        }

        .div-indicator {
            position: absolute;
            top: 16rem;
            padding: 2.3rem !important;
            direction: rtl !important;
        }

        .date, .number, .attachment {
            font-size: 1.1rem;
            position: absolute;
            font-family: Sans-Serif, Arial;
        }

        .date {
            top: 2.2rem;
            right: 2.5rem;

        }

        .number {
            top: 5rem;
            right: 2.5rem;
            width: 8rem;
            text-align: center;

        }

        .attachment {
            top: 6.5rem;
            right: 1rem;
            width: 8rem;
            text-align: center;

        }

    </style>
    <title>پیش نمایش چاپ</title>
</head>

<body>
<div class="a4-page">
    <div class="date">
        {{ $date }}
    </div>
    <div class="number">
        {{ $number }}
    </div>
    <div class="attachment">
        {{ $attachment }}
    </div>

    <div class="div-indicator">
        <div>
            {!! $text !!}
        </div>
    </div>
</div>
</body>

</html>
