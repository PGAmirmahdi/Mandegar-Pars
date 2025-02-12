@extends('panel.layouts.master')
@section('title', 'محصولات وبسایت Artin')
@section('content')
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
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center" id="products_table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>کد محصول</th>
                        <th>کد حسابداری</th> <!-- اضافه شده -->
                        <th>قیمت</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('artin-products-edit')
                            <th>ویرایش قیمت</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $product->post_title }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->code_accounting ?? 'نامشخص' }}</td>
                            <td id="{{ $product->ID }}">{{ number_format($product->min_price) . ' تومان' }}</td>
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
                                    <button class="btn btn-warning btn-floating btn_edit" data-toggle="modal" data-target="#editPriceModal" data-id="{{ $product->ID }}" data-price="{{ $product->min_price }}">
                                        <i class="fa fa-edit"></i>
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

                        $('#btn_update').removeAttr('disabled').text('اعمال');

                        $(`#${res.product_id}`).text(res.price_text)
                        $(`button[data-id="${res.product_id}"]`).data('price',res.price)

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
        });
    </script>
@endsection
