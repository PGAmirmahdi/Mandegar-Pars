@extends('panel.layouts.master')
@section('title','لیست قیمت ها')
@php
    $sellers = \Illuminate\Support\Facades\DB::table('price_list_sellers')->get();
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
    {{-- Add Model Modal --}}
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
    {{-- end Add Model Modal --}}
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
    {{-- Remove Seller Modal --}}
    <div class="modal fade" id="removeSellerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeSellerModalLabel">حذف تامین کننده</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h5 class="text-center">می خواهید این تامین کننده را حذف کنید؟</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-danger" id="btn_remove_seller">حذف</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end Remove Seller Modal --}}
    {{-- Remove Model Modal --}}
    <div class="modal fade" id="removeModelModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModelModalLabel">حذف مدل</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h5 class="text-center">می خواهید این مدل را حذف کنید؟</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-danger" id="btn_remove_model">حذف</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end Remove Model Modal --}}
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت ها - (ریال)</h3>
            <div style="overflow-x: auto" class="tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th colspan="{{ \Illuminate\Support\Facades\DB::table('price_list_sellers')->count() }}">
                            <i class="fa fa-plus text-success mr-2" data-toggle="modal" data-target="#addSellerModal" id="btn_seller"></i>
                            تامین کننده
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <div style="display: block ruby">
                                <i class="fa fa-plus text-success mr-2" data-toggle="modal" data-target="#addModelModal" id="btn_model"></i>
                                <span>برند/مدل</span>
                            </div>
                        </th>
                        @foreach($sellers as $seller)
                            <th class="seller">
                                <i class="fa fa-times text-danger btn_remove_seller mr-2" data-toggle="modal" data-target="#removeSellerModal"></i>
                                <span>{{ $seller->name }}</span>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\Illuminate\Support\Facades\DB::table('price_list_models')->get() as $model)
                        <tr>
                            <th style="display: block ruby">
                                <i class="fa fa-times text-danger btn_remove_model mr-2" data-toggle="modal" data-target="#removeModelModal"></i>
                                <span>{{ $model->name }}</span>
                            </th>
                            @for($i = 0; $i < \Illuminate\Support\Facades\DB::table('price_list_sellers')->count(); $i++)
                                @php $item = \Illuminate\Support\Facades\DB::table('price_list')->where(['model_id' => $model->id, 'seller_id' => $sellers[$i]->id])->first() @endphp
                                <td>
                                    <input type="text" class="item" data-model_id="{{ $model->id }}" data-seller_id="{{ $sellers[$i]->id }}" value="{{ $item ? number_format($item->price) : '-' }}">
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
                        'model_id': $(item).data('model_id'),
                        'price': $(item).val(),
                    })
                })
                // console.log(items)

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
                        // console.log(res)
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
                // event handlers
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

            // add model
            $(document).on('click', '#btn_add_model', function () {
                let model_name = $('#model').val();

                if(model_name === ''){
                    $('#model_error').text('وارد کردن عنوان مدل الزامی است')
                }else{
                    $('#model_error').text('')

                    $.ajax({
                        url: '/panel/add-model',
                        type: 'post',
                        data: {
                            name: model_name
                        },
                        success: function (res) {
                            if(res.data === undefined){
                                $('tbody:not(.internal_tels)').html($(res).find('tbody:not(.internal_tels)').html());
                                $('#addModelModal').hide();
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');

                                Swal.fire({
                                    title: 'با موفقیت اضافه شد',
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
                            }else{
                                $('#model_error').text(res.data.message)
                            }
                        }
                    })
                }
            })

            // add seller
            $(document).on('click', '#btn_add_seller', function () {
                let seller_name = $('#seller').val();

                if(seller_name === ''){
                    $('#seller_error').text('وارد کردن نام تامین کننده الزامی است')
                }else{
                    $('#seller_error').text('')

                    $.ajax({
                        url: '/panel/add-seller',
                        type: 'post',
                        data: {
                            name: seller_name
                        },
                        success: function (res) {
                            if(res.data === undefined){
                                $('#price_table').html($(res).find('#price_table').html());
                                $('#addSellerModal').hide();
                                $('.modal-backdrop').remove();
                                $('body').removeClass('modal-open');

                                Swal.fire({
                                    title: 'با موفقیت اضافه شد',
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
                            }else{
                                $('#seller_error').text(res.data.message)
                            }
                        }
                    })
                }
            })

            // remove seller and model
            var seller;
            var model;

            $(document).on('click', '.btn_remove_seller', function () {
                seller = $(this).parent().find('span').text();
            })

            $(document).on('click', '.btn_remove_model', function () {
                model = $(this).parent().find('span').text();
            })

            $(document).on('click','#btn_remove_seller', function () {
                $.ajax({
                    url: '/panel/remove-seller',
                    type: 'post',
                    data: {
                        name: seller
                    },
                    success: function (res) {
                        $('#removeSellerModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');

                        $('#price_table').html($(res).find('#price_table').html());

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
                    }
                })
            })

            $(document).on('click','#btn_remove_model', function () {
                $.ajax({
                    url: '/panel/remove-model',
                    type: 'post',
                    data: {
                        name: model
                    },
                    success: function (res) {
                        $('#removeModelModal').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');

                        $('#price_table').html($(res).find('#price_table').html());

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
                    }
                })
            })
        })
    </script>
@endsection
