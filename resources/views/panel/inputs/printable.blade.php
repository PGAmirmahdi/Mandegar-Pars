@extends('panel.layouts.master')
@section('title', 'چاپ رسید انبار')
@php
    $sidebar = false;
    $header = false;

    $sum_total_price = 0;
    $sum_discount_amount = 0;
    $sum_extra_amount = 0;
    $sum_total_price_with_off = 0;
    $sum_tax = 0;
    $sum_invoice_net = 0;
@endphp
@section('styles')
    <style>
        #products_table input, #products_table select{
            width: auto;
        }
        .title-sec{
            background: #ececec;
        }
        .main-content{
            margin: 0 !important;
        }

        .mr-100{
            margin-right: 100px !important;
        }

        @page {
            size: A4 landscape;
        }

        @media print {
            body {
                transform: scale(0.9);
            }
        }

        body{
            padding: 0;
        }

        main{
            padding: 0 !important;
        }

        table th, td{
            padding: 4px !important;
            border: 2px solid #000 !important;
            font-size: 16px !important;
        }

        table th{
            font-weight: bold !important;
        }

        table tr{
            padding: 0 !important;
            border: 2px solid #000 !important;
        }

        #printable_sec{
            padding: 0;
        }

        .card{
            margin: 0;
        }

        .guide_box{
            text-align: center;
        }

        #seller_sign_sec{
            position: relative;
            height: 75px;
        }

        #seller_sign_sec small{
            position: absolute;
            top: 15px;
            left: 45%;
        }

        #seller_sign_sec .sign{
            position: absolute;
            top: 25px;
            left: 44%;
            width: 10rem;
        }

        #seller_sign_sec .stamp{
            position: absolute;
            top: 25px;
            left: 43%;
            width: 13rem;
        }

        #person_sec small{
            font-weight: bold;
            font-size: 15px;
        }


    </style>

@endsection
@section('content')
    <div class="card">
        <div class="card-body" id="printable_sec">
            <div class="card-title">
                <div class="row">
                    <div class="col-4">
                        <img src="/assets/media/image/header-logo.png" style="width: 15rem;">
                    </div>
                    <div class="col-3 text-right text-center" id="person_sec">
                        <h3>شرکت صنایع ماشین های اداری ماندگار پارس</h3>
                        <small>رسید انبار</small>
                        <br>
                        <small>تحویل دهنده: {{ $inventoryReport->person }}</small>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-2 text-center">
                        <p class="m-0">شماره سریال: {{  $inventoryReport->id }}</p>
                        <hr class="mt-0">
                        <p class="m-0">تاریخ: {{ verta($inventoryReport->created_at)->format('Y/m/d') }}</p>
                        <hr class="mt-0">
                    </div>
                </div>
            </div>
            <form action="" method="post">
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="overflow-auto">
                            <table class="table text-center" border="2">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>کد کالا</th>
                                    <th>عنوان کالا</th>
                                    <th>مقدار اصلی</th>
                                    <th>واحد اصلی</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventoryReport->in_outs as $key => $item)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $item->inventory->code }}</td>
                                            <td>{{ $item->inventory->title }}</td>
                                            <td>{{ $item->count }}</td>
                                            <td>عدد</td>
                                        </tr>
                                    @endforeach
                                    @if($inventoryReport->description)
                                        <tr style="line-height: 1">
                                            <th>توضیحات</th>
                                            <td colspan="4">{{ $inventoryReport->description }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div id="seller_sign_sec">
                            <img src="{{ auth()->user()->sign_image ?? '' }}" class="sign">
                            <img src="{{ asset('/assets/media/image/stamp.png') }}" class="stamp">
                            <small>مهر و امضای انباردار</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="pb-2 d-flex justify-content-between px-3" id="print_sec">
            <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fa fa-chevron-right mr-2"></i>برگشت</a>
            <button class="btn btn-info" id="btn_print"><i class="fa fa-print mr-2"></i>چاپ</button>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#btn_print').click(function () {
                $('#print_sec').addClass('d-none').removeClass('d-flex');
                $('.alert-info').addClass('d-none').removeClass('d-flex');
                window.print();
                $('#print_sec').removeClass('d-none').addClass('d-flex');
                $('.alert-info').removeClass('d-none').addClass('d-flex');
            })
        })
    </script>
@endsection

