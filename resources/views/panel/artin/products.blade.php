@extends('panel.layouts.master')
@section('title', 'محصولات وبسایت Artin')
@section('content')
    {{--  edit Price Modal  --}}
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
    {{--  end edit Price Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>محصولات وبسایت Artin</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
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
        var product_id;

        $(document).ready(function () {
            $('.btn_edit').on('click', function () {
                product_id = $(this).data('id');
                let price = $(this).data('price');
                price = parseInt(price);

                $('#price').val(price)
            })

            $('#btn_update').on('click', function (){
                $(this).attr('disabled','disabled').text('درحال بروزرسانی...')

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
                        $('tbody').html($(res).find('tbody').html());

                        $('#btn_update').removeAttr('disabled').text('اعمال')

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
                        })
                    }
                })
            })
        })
    </script>
@endsection
