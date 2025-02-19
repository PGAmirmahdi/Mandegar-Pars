<!-- Plugin scripts -->
<script src="/vendors/bundle.js"></script>
<!-- Chartjs -->
<script src="/vendors/charts/chartjs/chart.min.js"></script>

{{--platform--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/platform/1.3.6/platform.min.js"></script>

<!-- Circle progress -->
<script src="/vendors/circle-progress/circle-progress.min.js"></script>

<!-- Peity -->
<script src="/vendors/charts/peity/jquery.peity.min.js"></script>
<script src="/assets/js/examples/charts/peity.js"></script>

<!-- Datepicker -->
<script src="/vendors/datepicker/daterangepicker.js"></script>


<!-- Slick -->
<script src="/vendors/slick/slick.min.js"></script>

<!-- Vamp -->
<script src="/vendors/vmap/jquery.vmap.min.js"></script>
<script src="/vendors/vmap/maps/jquery.vmap.iran.js"></script>
<script src="/assets/js/examples/vmap.js"></script>

<!-- CKEditor -->
<script src="/vendors/ckeditor/ckeditor.js"></script>
<script src="/assets/js/examples/ckeditor.js"></script>

<!-- Dashboard scripts -->
<script src="/assets/js/examples/dashboard.js"></script>
<div class="colors">
    <!-- To use theme colors with Javascript -->
    <div class="bg-primary"></div>
    <div class="bg-primary-bright"></div>
    <div class="bg-secondary"></div>
    <div class="bg-secondary-bright"></div>
    <div class="bg-info"></div>
    <div class="bg-info-bright"></div>
    <div class="bg-success"></div>
    <div class="bg-success-bright"></div>
    <div class="bg-danger"></div>
    <div class="bg-danger-bright"></div>
    <div class="bg-warning"></div>
    <div class="bg-warning-bright"></div>
</div>

<!-- App scripts -->
<script src="/assets/js/app.js"></script>
<script src="/assets/js/sweetalert2@11"></script>

<!-- Select2 -->
<script src="/vendors/select2/js/select2.min.js"></script>
<script src="/assets/js/examples/select2.js"></script>

<!-- Datepicker -->
<script src="/vendors/datepicker-jalali/bootstrap-datepicker.min.js"></script>
<script src="/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js"></script>
<script src="/vendors/datepicker/daterangepicker.js"></script>
<script src="/assets/js/examples/datepicker.js"></script>

<!-- Clockpicker -->
<script src="/vendors/clockpicker/bootstrap-clockpicker.min.js"></script>
<script src="/assets/js/examples/clockpicker.js"></script>

<!-- fontawesome -->
<script src="/assets/js/fontawesome.min.js"></script>

<!-- DataTable -->
<script src="/vendors/dataTable/jquery.dataTables.min.js"></script>
<script src="/vendors/dataTable/dataTables.bootstrap4.min.js"></script>
<script src="/vendors/dataTable/dataTables.responsive.min.js"></script>
<script src="/assets/js/examples/datatable.js"></script>

<!-- Drop Zone -->
<script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>

{{--Laravel Echo--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.iife.min.js"></script>--}}

<script src="{{ asset('/js/app.js') }}"></script>

@yield('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var body = document.getElementsByTagName('body')[0];
        var icon = document.getElementById('darkModeIcon');
        var text = document.getElementById('darkModeToggle');

        // بررسی حالت ذخیره‌شده در localStorage
        if (localStorage.getItem("darkMode") === "enabled") {
            body.classList.add("dark");
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
            text.setAttribute("data-original-title", "حالت روز");
        } else {
            body.classList.remove("dark");
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
            text.setAttribute("data-original-title", "حالت شب");
        }
    });

    function toggleDark() {
        var body = document.getElementsByTagName('body')[0];
        var icon = document.getElementById('darkModeIcon');
        var text = document.getElementById('darkModeToggle');

        body.classList.toggle('dark');

        if (body.classList.contains('dark')) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
            text.setAttribute("data-original-title", "حالت روز");

            // ذخیره حالت تاریک در localStorage
            localStorage.setItem("darkMode", "enabled");
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
            text.setAttribute("data-original-title", "حالت شب");

            // ذخیره حالت روشن در localStorage
            localStorage.setItem("darkMode", "disabled");
        }
    }

    var userId = {{ Auth::user()->id }};

    {{--Pusher.logToConsole = false;--}}

    {{--var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {--}}
    {{--    cluster: '{{ env("PUSHER_APP_CLUSTER") }}',--}}
    {{--    encrypted: true,--}}
    {{--    authEndpoint: '/pusher/auth',--}}
    {{--    auth: {--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // ارسال توکن CSRF--}}
    {{--        }--}}
    {{--    }--}}
    {{--});--}}

    {{--var channel = pusher.subscribe('private-notification.' + userId);--}}
    {{--channel.bind('App\\Events\\SendMessage', function(data) {--}}
    {{--    console.log('Notification received: ', data.message);--}}
    {{--});--}}

</script>
<script>
    {{-- ajax setup --}}
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    {{-- end ajax setup --}}

    {{-- delete tables row --}}
    $(document).on('click', '.trashRow', function () {
        let self = $(this)
        Swal.fire({
            title: 'حذف شود؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e04b4b',
            confirmButtonText: 'حذفش کن',
            cancelButtonText: 'لغو',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: self.data('url'),
                    type: 'post',
                    data: {
                        id: self.data('id'),
                        _method: 'delete'
                    },
                    success: function (res) {
                        $('tbody:not(.internal_tels)').html($(res).find('tbody:not(.internal_tels)').html());
                        Swal.fire({
                            title: 'با موفقیت حذف شد',
                            icon: 'success',
                            showConfirmButton: false,
                            toast: true,
                            timer: 2000,
                            timerProgressBar: true,
                            position: 'top-start',
                            customClass: {
                                popup: 'my-toast',
                                icon: 'icon-center',
                                title: 'left-gap',
                                content: 'left-gap',
                            }
                        })
                    },
                    error: function (jqXHR, exception) {
                        Swal.fire({
                            title: jqXHR.responseText,
                            icon: 'error',
                            showConfirmButton: false,
                            toast: true,
                            timer: 4000,
                            timerProgressBar: true,
                            position: 'top-start',
                            customClass: {
                                popup: 'my-toast',
                                icon: 'icon-center',
                                title: 'left-gap',
                                content: 'left-gap',
                            }
                        })
                    }
                })

            }
        })
    })
    {{-- end delete tables row --}}

    //  network status
    window.addEventListener("offline", (event) => {
        $('#network_sec').html(`
                <span data-toggle="tooltip" data-placement="bottom" data-original-title="connecting">
                    <i class="fa fa-wifi text-danger zoom-in-out"></i>
                </span>`)
        $('#network_sec span').tooltip();
    });

    window.addEventListener("online", (event) => {
        $('#network_sec').html(`
                <span data-toggle="tooltip" data-placement="bottom" data-original-title="connected">
                    <i class="fa fa-wifi text-success"></i>
                </span>`)
        $('#network_sec span').tooltip();
    });
    // end network status

    // realtime notification
    // تعریف متغیرهای سراسری
    var notificationsBlocked = false;
    var soundMuted = false;

    // ثبت رویداد بعد از لود شدن صفحه
    document.addEventListener('DOMContentLoaded', function () {
        var blockSwitch = document.getElementById('customSwitch11');
        var muteSwitch = document.getElementById('customSwitch12');

        if (blockSwitch) {
            blockSwitch.addEventListener('change', function () {
                notificationsBlocked = this.checked;
                console.log("Notifications blocked: " + notificationsBlocked);
            });
        }

        if (muteSwitch) {
            muteSwitch.addEventListener('change', function () {
                soundMuted = this.checked;
                console.log("Sound muted: " + soundMuted);
            });
        }
    });
    // فرض کنید اینجا آدرس فایل صوتی نوتیفیکیشن شماست.
    var audio = new Audio('/audio/notification.wav');

    // دریافت شناسه کاربر از سرور (همانطور که در کد شما وجود دارد)
    var userId = {{ Auth::user()->id }};

    // دریافت نوتیفیکیشن از کانال Echo
    Echo.channel('presence-notification.' + userId)
        .listen('SendMessage', (e) => {
            // به‌روزرسانی بخش اعلان‌ها
            $('#notification_sec a').addClass('nav-link-notify');
            $('#notif_count').html(parseInt($('#notif_count').html()) + 1);
            $(".timeline").prepend(`
                <div class="timeline-item">
                    <div>
                        <figure class="avatar avatar-state-danger avatar-sm m-r-15 bring-forward">
                            <span class="avatar-title bg-primary-bright text-primary rounded-circle">
                                <i class="fa fa-bell font-size-20"></i>
                            </span>
                        </figure>
                    </div>
                    <div>
                        <p class="m-b-5">
                            <a href="/panel/read-notifications/${e.data.id}">${e.data.message}</a>
                        </p>
                        <small class="text-muted">
                            <i class="fa fa-clock-o m-r-5"></i>الان
                        </small>
                    </div>
                </div>
            `);

            audio.play();
        });
    messaging.onMessage(function (payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };

        new Notification(noteTitle, noteOptions);

        audio.play();
    });
    // window.Echo.channel(`my-test`)
    //     .listen('.test.event', (e) => {
    //         console.log(e)
    //     })
    // console.log(window.Echo)
    // end realtime

    // firebase push notification
    var firebaseConfig = {
        apiKey: "AIzaSyCtEA3i9gCB6dFxg8EqaVM1D5IpwB-ylHU",
        authDomain: "mandagar569874586.firebaseapp.com",
        projectId: "mandagar569874586",
        storageBucket: "mandagar569874586.firebasestorage.app",
        messagingSenderId: "521491944",
        appId: "1:521491944:web:bc9dc1a872ee0c1c4dc05d",
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (token) {
                console.log(token);

                $.ajax({
                    url: '/panel/saveFcmToken',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        console.log('Token saved successfully.');
                    },
                    error: function (err) {
                        console.log('User Chat Token Error' + err);
                    },
                });

            }).catch(function (err) {
            console.log('User Chat Token Error' + err);
        });
    }

    initFirebaseMessagingRegistration();

    messaging.onMessage(function (payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });
</script>
{{--<script>--}}
{{--    if ('serviceWorker' in navigator) {--}}
{{--        navigator.serviceWorker.register('/serviceworker.js')--}}
{{--            .then(() => console.log('سرویس ورکر رجیستر شد'))--}}
{{--            .catch(error => console.error('سرویس ورکر رجیستر نشد مشکل:', error));--}}
{{--    }--}}
{{--</script>--}}
