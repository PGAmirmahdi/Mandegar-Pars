<!-- Plugin scripts -->
<script src="/vendors/bundle.js"></script>

<!-- Chartjs -->
<script src="/vendors/charts/chartjs/chart.min.js"></script>

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
<script src="/vendors/vmap/maps/jquery.vmap.usa.js"></script>
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

@yield('scripts')

<script>
    {{-- ajax setup --}}
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    {{-- end ajax setup --}}

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
                        $('.table').html($(res).find('.table').html());
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
                    }
                })

            }
        })
    })
    {{-- end delete tables row --}}
</script>
