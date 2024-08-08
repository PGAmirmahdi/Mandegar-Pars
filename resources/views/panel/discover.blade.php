{{--Button to get back into the app--}}
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>بازگشت به سایت</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body onload="load()">
    <p>در حال شناسایی دستگاه لطفا منتظر بمانید</p>
    <div class="loader"></div>
<style>
    @font-face {
        font-family: 'Estedad';
        src: url("{{asset('assets/fonts/Estedad-Black.woff2')}}") format("woff2"), url("{{asset('assets/fonts/Estedad-Black.ttf')}}") format("truetype"), url("{{asset('assets/fonts/Estedad-Black.woff')}}") format("woff");
    ;
        font-weight: normal;
        font-style: normal;
    }
    body{
        min-height: 100vh;
        width:100%;
        background-color: #f5f5f5;
        font-family: 'Estedad';
        display: flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
    }
    /* HTML: <div class="loader"></div> */
    .loader {
        width: 50px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 8px solid #514b82;
        animation:
            l20-1 0.8s infinite linear alternate,
            l20-2 1.6s infinite linear;
    }
    @keyframes l20-1{
        0%    {clip-path: polygon(50% 50%,0       0,  50%   0%,  50%    0%, 50%    0%, 50%    0%, 50%    0% )}
        12.5% {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100%   0%, 100%   0%, 100%   0% )}
        25%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 100% 100%, 100% 100% )}
        50%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
        62.5% {clip-path: polygon(50% 50%,100%    0, 100%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
        75%   {clip-path: polygon(50% 50%,100% 100%, 100% 100%,  100% 100%, 100% 100%, 50%  100%, 0%   100% )}
        100%  {clip-path: polygon(50% 50%,50%  100%,  50% 100%,   50% 100%,  50% 100%, 50%  100%, 0%   100% )}
    }
    @keyframes l20-2{
        0%    {transform:scaleY(1)  rotate(0deg)}
        49.99%{transform:scaleY(1)  rotate(135deg)}
        50%   {transform:scaleY(-1) rotate(0deg)}
        100%  {transform:scaleY(-1) rotate(-135deg)}
    }
</style>
{{--Link JS--}}
    <script>
        const getUA = () => {
            let device = "Unknown";
            const ua = {
                "Generic Linux": /Linux/i,
                "Android": /Android/i,
                "BlackBerry": /BlackBerry/i,
                "Bluebird": /EF500/i,
                "Chrome OS": /CrOS/i,
                "Datalogic": /DL-AXIS/i,
                "Honeywell": /CT50/i,
                "iPad": /iPad/i,
                "iPhone": /iPhone/i,
                "iPod": /iPod/i,
                "macOS": /Macintosh/i,
                "Windows": /IEMobile|Windows/i,
                "Zebra": /TC70|TC55/i,
            };
            Object.keys(ua).forEach(v => {
                if (navigator.userAgent.match(ua[v])) {
                    device = v;
                }
            });
            return device;
        };
        const redirectURL = (device) => {
            if (device === "iPhone" || device === "iPad" || device === "iPod" || device === "macOS") {
                setTimeout(() => {
                    window.location.href = "https://app.mpsystem.ir/pwa";
                }, 3000);
            } else if (device === "Android") {
                const intentUrl = "intent://artintoner.com#Intent;scheme=https;package=com.example.artintoner;S.browser_fallback_url=https%3A%2F%2Fcafebazaar.ir%2Fapp%2Fcom.example.artintoner;end";
                const fallbackUrl = "https://cafebazaar.ir/app/com.example.artintoner";

                // Try to open the intent URL
                setTimeout(() => {
                    window.location.replace(intentUrl);
                }, 1000);

                // Set a timeout to redirect to the fallback URL if the app does not open
                setTimeout(() => {
                    window.location.replace(fallbackUrl);
                }, 3000);
            } else {
                setTimeout(() => {
                    window.location.href = "https://app.mpsystem.ir/pwa";
                }, 3000);
            }
        };
        // Detect the device and redirect accordingly
        const userDevice = getUA();
        redirectURL(userDevice);
    </script>
</body>
</html>
