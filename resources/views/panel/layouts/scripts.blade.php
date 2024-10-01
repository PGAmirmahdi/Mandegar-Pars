<!-- Plugin scripts -->
<script src="/vendors/bundle.js"></script>
<!-- Chartjs -->
<script src="/vendors/charts/chartjs/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2"></script>

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

<script src="{{ mix('/js/app.js') }}"></script>

@yield('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.2/echo.iife.js"></script>


<script src="https://www.gstatic.com/firebasejs/9.1.3/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.1.3/firebase-messaging-compat.js"></script>
<script>
    // Firebase push notification setup
    const firebaseConfig = {
        apiKey: "AIzaSyCUdU7PnQmzrkcJDFOJsIGcpe7CZV1GBrA",
        authDomain: "mandegarpars-5e075.firebaseapp.com",
        projectId: "mandegarpars-5e075",
        storageBucket: "mandegarpars-5e075.appspot.com",
        messagingSenderId: "11452789862",
        appId: "1:11452789862:web:8ee1465cf4e374fcbde9a7"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging.getToken({ vapidKey: '<YOUR_PUBLIC_VAPID_KEY>' }).then((currentToken) => {
            if (currentToken) {
                // ارسال توکن به سرور شما
                $.ajax({
                    url: '/panel/saveFcmToken',  // این URL سمت سرور شما است
                    type: 'POST',
                    data: {
                        token: currentToken
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        console.log('Token saved successfully on the server.');
                    },
                    error: function (err) {
                        console.log('Error saving token on the server:', err);
                    },
                });
            } else {
                console.log('No registration token available.');
            }
        }).catch((err) => {
            console.error('An error occurred while retrieving token. ', err);
        });
    }

    initFirebaseMessagingRegistration();

    // Handle incoming messages
    messaging.onMessage(function(payload) {
        console.log('Message received. ', payload);
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });

</script>

<script>

    // Handle incoming messages
    messaging.onMessage(function(payload) {
        console.log('Message received. ', payload);
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });

    // Pusher initialization and event handling
    var pusher = new Pusher('ac8ae105709d7299a673', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
        alert(JSON.stringify(data));
    });
</script>
<script>
    {{-- ajax setup --}}
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    {{-- end ajax setup --}}
    messaging.getToken().then((currentToken) => {
        if (currentToken) {
            console.log('FCM Token:', currentToken);
        } else {
            console.log('No registration token available.');
        }
    }).catch((err) => {
        console.error('An error occurred while retrieving token. ', err);
    });

    {{-- delete tables row --}}
    $(document).on('click','.trashRow', function() {
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
                    success: function(res) {
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
    console.log(window.Echo);
    var audio = new Audio('/audio/notification.wav');
    let userId = "{{ auth()->id() }}";
    Echo.join('presence-notification.' + userId)
        .listen('SendMessage', (e) => {
            $('#notification_sec a').addClass('nav-link-notify');
            $('#notif_count').html(parseInt($('#notif_count').html()) + 1);
            $(".timeline").prepend(`<div class="timeline-item">
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
            </div>`);
            audio.play();
        });
</script>
