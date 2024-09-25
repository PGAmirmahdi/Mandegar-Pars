@extends('panel.layouts.master')
@section('title', 'محصولات وبسایت Artin')
@section('content')
    {{--  Create Product Modal  --}}
    @can('artin-products-create')
        <div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    @can('artin-products-create')
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProductModalLabel">ایجاد محصول جدید</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    @endcan
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_title">عنوان</label>
                            <input type="text" name="title" id="create_title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="create_sku">کد محصول</label>
                            <input type="text" name="sku" id="create_sku" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="create_price">قیمت</label>
                            <input type="text" name="price" id="create_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="create_status">وضعیت</label>
                            <select name="status" id="create_status" class="form-control">
                                <option value="publish">منتشر شده</option>
                                <option value="draft">پیش نویس</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">لغو</button>
                        <button type="button" class="btn btn-primary" id="btn_create">ایجاد</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    {{--  end Create Product Modal  --}}
    {{--  edit Price Modal  --}}
    @can('artin-products-edit')
        <div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPriceModalLabel">ویرایش قیمت</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="price">قیمت</label>
                            <input type="text" name="price" id="price" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">لغو</button>
                        <button type="button" class="btn btn-primary" id="btn_update">اعمال</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    {{--  end edit Price Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>محصولات وبسایت Artin</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createProductModal">ایجاد محصول جدید</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center" id="products_table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>کد محصول</th>
                        <th>قیمت</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('artin-products-edit')
                            <th>ویرایش قیمت</th>
                        @endcan
                        @can('artin-products-delete')
                            <th>حذف محصول</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $product->post_title }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ number_format($product->min_price).' تومان ' }}</td>
                            <td>
                                @if($product->post_status == 'publish')
                                    <span class="badge badge-success">منتشر شده</span>
                                @elseif($product->post_status == 'draft')
                                    <span class="badge badge-warning">پیش نویس</span>
                                @else
                                    <span class="badge badge-warning">نامشخص</span>
                                @endif
                            </td>
                            <td>{{ verta($product->post_date)->format('H:i - Y/m/d') }}</td>
                            @can('artin-products-edit')
                                <td>
                                    <button class="btn btn-warning btn-floating btn_edit" data-toggle="modal" data-target="#editPriceModal" data-id="{{ $product->id }}" data-price="{{ $product->min_price }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                            @endcan
                            @can('artin-products-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating btn_delete" data-id="{{ $product->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var product_id;

            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Edit price functionality
            $(document).on('click', '.btn_edit', function () {
                product_id = $(this).data('id');
                let price = $(this).data('price');
                price = parseInt(price);

                $('#price').val(price);
            });

            $(document).on('click', '#btn_update', function () {
                $(this).attr('disabled', 'disabled').text('درحال بروزرسانی...');

                let price = $('#price').val();

                $.ajax({
                    url: '/panel/artin-products-update-price',
                    type: 'post',
                    data: {
                        product_id,
                        price
                    },
                    success: function (res) {
                        $('#editPriceModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('#products_table tbody').html($(res).find('#products_table tbody').html());

                        $('#btn_update').removeAttr('disabled').text('اعمال');

                        Swal.fire({
                            title: 'قیمت با موفقیت ویرایش شد',
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
                        });
                    }
                });
            });

            // Create product functionality
            $(document).on('click', '#btn_create', function () {
                $(this).attr('disabled', 'disabled').text('درحال ایجاد...');

                let title = $('#create_title').val();
                let sku = $('#create_sku').val();
                let price = $('#create_price').val();
                let status = $('#create_status').val();

                $.ajax({
                    url: '/panel/artin-products-store',
                    type: 'post',
                    data: {
                        title: title,
                        sku: sku,
                        price: price,
                        status: status
                    },
                    success: function (res) {
                        $('#createProductModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('#products_table tbody').html($(res).find('#products_table tbody').html());

                        $('#btn_create').removeAttr('disabled').text('ایجاد');

                        Swal.fire({
                            title: 'محصول با موفقیت ایجاد شد',
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
                        });
                    },
                    error: function (xhr) {
                        $('#btn_create').removeAttr('disabled').text('ایجاد');

                        Swal.fire({
                            title: 'خطا در ایجاد محصول',
                            text: xhr.responseJSON.error,
                            icon: 'error',
                            showConfirmButton: true,
                        });
                    }
                });
            });

            // Delete product functionality
            $(document).ready(function () {
                var product_id;

                // Set up CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Delete product functionality
                $(document).on('click', '.btn_delete', function () {
                    product_id = $(this).data('id');

                    Swal.fire({
                        title: 'آیا مطمئن هستید؟',
                        text: "این عملیات قابل بازگشت نیست!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'بله، حذف شود!',
                        cancelButtonText: 'لغو'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/panel/artin-products-destroy/' + product_id,
                                type: 'delete',
                                data: {product_id},
                                success: function (res) {
                                    $('#products_table tbody').html($(res).find('#products_table tbody').html());

                                    Swal.fire({
                                        title: 'محصول با موفقیت حذف شد',
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
                                        },
                                    });
                                    window.location.reload();
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: 'خطا در حذف محصول',
                                        text: xhr.responseJSON.error,
                                        icon: 'error',
                                        showConfirmButton: true,
                                    });
                                }
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
