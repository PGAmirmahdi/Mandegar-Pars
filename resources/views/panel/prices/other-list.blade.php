@extends('panel.layouts.master')
@section('title','لیست قیمت ها')
@php
    $sellers = \Illuminate\Support\Facades\DB::table('price_list_sellers')->get();
    $products = \Illuminate\Support\Facades\DB::table('products')->paginate(20); // محصولات را از جدول products دریافت می‌کنیم
@endphp
@section('styles')
    <style>
        #price_table td:hover{
            background-color: #e3daff !important;
        }

        #price_table .item{
            text-align: center;
            background: transparent;
            border: 0;
        }

        #price_table .item:focus{
            border-bottom: 2px solid #5d4a9c;
        }

        #btn_save{
            width: 100%;
            justify-content: center;
            border-radius: 0;
            padding: .8rem;
            font-size: larger;
        }

        #price_table{
            box-shadow: 0 5px 5px 0 lightgray;
        }

        #btn_model, #btn_seller, .btn_remove_seller, .btn_remove_model{
            vertical-align: middle;
            cursor: pointer;
        }

        /* table th sticky */
        .tableFixHead {
            overflow: auto !important;
            height: 800px !important;
        }

        .tableFixHead thead th{
            position: sticky !important;
            top: 0 !important;
            z-index: 1 !important;
        }

        /* Just common table stuff. Really. */
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        th, td {
            padding: 8px 16px !important;
        }

        .tableFixHead thead th {
            background: #fff !important;
            border: 1px solid #dee2e6 !important;
        }
        /* table th sticky */
    </style>
@endsection
@section('content')
    {{-- Add ProductModel Modal --}}
    <div class="modal fade" id="addModelModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModelModalLabel">افزودن مدل</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="model">عنوان مدل <span class="text-danger">*</span></label>
                        <input type="text" id="model" class="form-control">
                        <span class="invalid-feedback d-block" id="model_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-success" id="btn_add_model">افزودن</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end Add ProductModel Modal --}}
    {{-- Add Seller Modal --}}
    <div class="modal fade" id="addSellerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSellerModalLabel">افزودن تامین کننده</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="seller">نام تامین کننده <span class="text-danger">*</span></label>
                        <input type="text" id="seller" class="form-control">
                        <span class="invalid-feedback d-block" id="seller_error"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-success" id="btn_add_seller">افزودن</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end Add Seller Modal --}}
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت ها - (ریال)</h3>
            <div class="mb-3">
                <input type="text" id="productSearch" class="form-control" placeholder="جستجوی محصولات...">
            </div>
            <div style="overflow-x: auto" class="tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th colspan="{{$sellers->count()}}">
                            <i class="fa fa-plus text-success mr-2" data-toggle="modal" data-target="#addSellerModal" id="btn_seller"></i>
                            فروشنده
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <div style="display: block ruby">
                                <span>مدل</span>
                            </div>
                        </th>
                        @foreach($sellers as $seller)
                            <th class="seller">
                                <i class="fa fa-times text-danger btn_remove_seller mr-2" data-toggle="modal" data-target="#removeSellerModal" data-seller_id="{{ $seller->id }}"></i>
                                <span>{{ $seller->name }}</span>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product) {{-- استفاده از جدول محصولات --}}
                    <tr>
                        <th style="display: block ruby">
                            <span>{{ $product->title }}</span>
                        </th>
                        @for($i = 0; $i < $sellers->count(); $i++)
                            @php
                                $item = \Illuminate\Support\Facades\DB::table('price_list')
                                    ->where(['product_id' => $product->id, 'seller_id' => $sellers[$i]->id])
                                    ->first();
                            @endphp
                            <td>
                                <input type="text" class="item" data-product_id="{{ $product->id }}" data-seller_id="{{ $sellers[$i]->id }}" value="{{ $item ? number_format($item->price) : '-' }}">
                            </td>
                        @endfor
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
            <button class="btn btn-primary my-3 mx-1" id="btn_save">
                <i class="fa fa-check mr-2"></i>
                <span>ذخیره</span>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // btn save
            $('#btn_save').on('click', function () {
                $(this).attr('disabled','disabled');
                $('#btn_save span').text('درحال ذخیره سازی...')
                let items = [];

                $.each($('#price_table .item'), function (i, item) {
                    items.push({
                        'seller_id': $(item).data('seller_id'),
                        'product_id': $(item).data('product_id'), // استفاده از product_id به جای model_id
                        'price': $(item).val(),
                    })
                })

                $.ajax({
                    url: "{{ route('updatePrice') }}",
                    type: 'post',
                    data: {
                        items: JSON.stringify(items)
                    },
                    success: function (res) {
                        $('#btn_save').removeAttr('disabled');
                        $('#btn_save span').text('ذخیره')

                        Swal.fire({
                            title: 'با موفقیت ذخیره شد',
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

            // item changed
            $(document).on('keyup','.item', function () {
                $(this).val(addCommas($(this).val()))
            })

            function funcReverseString(str) {
                return str.split('').reverse().join('');
            }

            // for thousands grouping
            function addCommas(nStr) {
                let thisElementValue = nStr
                thisElementValue = thisElementValue.replace(/,/g, "");

                let seperatedNumber = thisElementValue.toString();
                seperatedNumber = funcReverseString(seperatedNumber);
                seperatedNumber = seperatedNumber.split("");

                let tmpSeperatedNumber = "";
                j = 0;
                for (let i = 0; i < seperatedNumber.length; i++) {
                    tmpSeperatedNumber += seperatedNumber[i];
                    j++;
                    if (j == 3) {
                        tmpSeperatedNumber += ",";
                        j = 0;
                    }
                }

                seperatedNumber = funcReverseString(tmpSeperatedNumber);
                if(seperatedNumber[0] === ",") seperatedNumber = seperatedNumber.replace("," , "");
                return seperatedNumber;
            }
        })
        // add seller
        // هنگام افزودن فروشنده جدید
        $(document).on('click', '#btn_add_seller', function () {
            let seller_name = $('#seller').val();

            if (seller_name === '') {
                $('#seller_error').text('وارد کردن نام تامین کننده الزامی است');
            } else {
                $('#seller_error').text('');

                $.ajax({
                    url: '/panel/add-seller',
                    type: 'POST',
                    data: {
                        name: seller_name,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.data) {
                            // پس از موفقیت در افزودن فروشنده جدید، دکمه حذف آن را به جدول اضافه می‌کنیم
                            let newSeller = `
                        <th class="seller">
                            <i class="fa fa-times text-danger btn_remove_seller mr-2" data-toggle="modal" data-target="#removeSellerModal" data-seller_id="${res.data.seller_id}"></i>
                            <span>${seller_name}</span>
                        </th>
                    `;
                            // اضافه کردن به جدول
                            $('#price_table thead tr:eq(1)').append(newSeller);
                            $('#addSellerModal').modal('hide');
                            Swal.fire({
                                title: 'با موفقیت اضافه شد',
                                icon: 'success',
                                toast: true,
                                timer: 2000,
                                position: 'top-start'
                            });
                        } else {
                            $('#seller_error').text(res.message);  // نمایش خطا در صورت بروز مشکل
                        }
                    },
                    error: function () {
                        alert('خطا در افزودن فروشنده');
                    }
                });
            }
        });
        // اصلاحات مربوط به دکمه حذف فروشنده
        $(document).on('click', '.btn_remove_seller', function () {
            // چاپ داده‌ها برای بررسی
            var sellerId = $(this).data('seller_id');  // استخراج seller_id از data-seller_id
            console.log("Seller ID: ", sellerId);  // چاپ sellerId برای بررسی

            // چک کنید که sellerId دریافت شده است
            if (!sellerId) {
                console.log("شناسه فروشنده یافت نشد");
                return;
            }

            // حذف فروشنده
            Swal.fire({
                title: 'آیا از حذف این فروشنده مطمئن هستید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'حذف',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/panel/remove-seller',
                        type: 'POST',
                        data: {
                            seller_id: sellerId,
                            _token: "{{ csrf_token() }}"  // ارسال CSRF Token برای درخواست امن
                        },
                        success: function (res) {
                            // در صورت موفقیت، جدول به‌روزرسانی می‌شود
                            console.log("Response: ", res);  // چاپ پاسخ سرور
                            if (res.status === 'success') {
                                $('#price_table').html($(res).find('#price_table').html());  // به‌روزرسانی جدول
                                Swal.fire({
                                    title: 'با موفقیت حذف شد',
                                    icon: 'success',
                                    toast: true,
                                    timer: 2000,
                                    position: 'top-start'
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطا در حذف فروشنده',
                                    icon: 'error',
                                    toast: true,
                                    timer: 2000,
                                    position: 'top-start'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                title: 'خطا در ارسال درخواست',
                                icon: 'error',
                                toast: true,
                                timer: 2000,
                                position: 'top-start'
                            });
                        }
                    });
                }
            });
        });
        // جستجوی محصولات
        $('#productSearch').on('keyup', function () {
            let query = $(this).val();

            $.ajax({
                url: '/panel/search-products',
                type: 'GET',
                data: { query: query },
                success: function (res) {
                    $('#productTableContainer').html(res.view);
                },
                error: function () {
                    alert('خطا در بارگذاری محصولات');
                }
            });
        });
    </script>
@endsection
