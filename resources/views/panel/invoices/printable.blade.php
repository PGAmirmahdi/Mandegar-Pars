@extends('panel.layouts.master')
@section('title', 'چاپ سفارش')
@php
    $sidebar = false;
    $header = false;

    $sum_total_price = 0;
    $sum_discount_amount = 0;
    $sum_extra_amount = 0;
    $sum_total_price_with_off = 0;
    $sum_tax = 0;
    $sum_invoice_net = 0;

    $i = 1;
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
        }

        #seller_sign_sec small{
            position: absolute;
        }

        #seller_sign_sec .sign{
            position: absolute;
            top: -60px;
            left: 34%;
            width: 10rem;
        }

        #seller_sign_sec .stamp{
            position: absolute;
            top: -41px;
            left: 31%;
            width: 13rem;
        }

        html, body, main {
            height: 100% !important;
        }

        .card {
            min-height: 100% !important;
            max-height: 130% !important;
        }

    </style>

@endsection
@section('content')
    <div class="card">
        <div class="card-body" id="printable_sec">
            <div class="card-title">
                <div class="row">
                    <div class="col-4">
                        @if($invoice->type == 'official')
                            <img src="/assets/media/image/header-logo.png" style="width: 15rem;">
                        @endif
                    </div>
                    <div class="col-3 text-right">
                        <h3>سفارش مشتری</h3>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-2 text-center">
                        <p class="m-0"> شماره سریال: {{ $invoice->id }}</p>
                        <hr class="mt-0">
                        <p class="m-0">تاریخ: {{ verta($invoice->created_at)->format('Y/m/d') }}</p>
                        <hr class="mt-0">
                    </div>
                </div>
            </div>
            <form action="" method="post">
                <div class="form-row">
                    <table class="table table-bordered mb-0">
                        <thead>
                        <tr>
                            <th class="text-center p-0 title-sec">مشخصات فروشنده</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($invoice->type == 'official')
                            <tr>
                                <td class="text-center">
                                    <div class="mb-3">
                                        <span class="mr-100">نام شخص حقیقی/حقوقی: شرکت صنایع ماشین های اداری ماندگار پارس</span>
                                        <span class="mr-100">شماره اقتصادی: 14011383061</span>
                                        <span class="mr-100">شماره ثبت/شماره ملی: 9931</span>
                                        <span class="mr-100">شناسه ملی: 14011383061</span>
                                    </div>
                                    <div>
                                        <span class="mr-100">نشانی: تهران، شهرستان ملارد، شهرک صنعتی صفادشت، بلوار خرداد، بین خیابان پنجم و ششم غربی، پلاک 228</span>
                                        <span class="mr-100">کد پستی: 3164114855</span>
                                        <span class="mr-100">شماره تلفن: 02165425053</span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="text-center">
                                    <div class="mb-3">
                                        <span class="mr-100">نام شخص حقیقی/حقوقی: {{ $invoice->seller->name }}</span>
                                        <span class="mr-100">استان: {{ $invoice->seller->province }}</span>
                                        <span class="mr-100">شهر: {{ $invoice->seller->city }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="mr-100">نشانی: {{ $invoice->seller->address }}</span>
                                        <span class="mr-100">شماره تماس: {{ $invoice->seller->phone }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="mr-100">شماره کارت: {{ $invoice->seller->card_number }}</span>
                                        <span class="mr-100">شماره شبا: {{ $invoice->seller->sheba_number }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    <table class="table table-bordered mb-5">
                        <thead>
                            <tr>
                                <th class="text-center p-0 title-sec">مشخصات خریدار</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <div class="mb-3">
                                        <span class="mr-100">نام شخص حقیقی/حقوقی: {{ $invoice->customer->name }}</span>
                                        <span class="mr-100">شماره اقتصادی: {{ $invoice->economical_number }}</span>
                                        <span class="mr-100">شماره ثبت/شماره ملی: {{ $invoice->national_number }}</span>
                                        <span class="mr-100">استان: {{ $invoice->province }}</span>
                                    </div>
                                    <div>
                                        <span class="mr-100">شهر: {{ $invoice->city }}</span>
                                        <span class="mr-100">کد پستی: {{ $invoice->postal_code }}</span>
                                        <span class="mr-100">نشانی: {{ $invoice->address }}</span>
                                        <span class="mr-100">شماره تلفن: {{ $invoice->phone }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-12 mb-3">
                        <div class="overflow-x-auto">
                            <table class="table text-center" border="2">
                                <thead>
                                <tr>
                                   <th class="p-0 title-sec" colspan="12">مشخصات کالا یا خدمات مورد معامله</th>
                                </tr>
                                <tr>
                                    <th>ردیف</th>
                                    <th>کالا</th>
                                    <th>رنگ</th>
                                    <th>تعداد</th>
                                    <th>واحد اندازه گیری</th>
                                    <th>مبلغ واحد</th>
                                    <th>مبلغ کل</th>
                                    <th>مبلغ تخفیف</th>
                                    <th>مبلغ اضافات</th>
                                    <th>مبلغ کل پس از تخفیف و اضافات</th>
                                    <th>جمع مالیات و عوارض</th>
                                    <th>خالص فاکتور</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {{-- artin products --}}
                                    @foreach($invoice->products as $key => $item)
                                        @php
                                            $usedCoupon = DB::table('coupon_invoice')->where([
                                                'product_id' => $item->pivot->product_id,
                                                'invoice_id' => $invoice->id,
                                            ])->first();

                                            if ($usedCoupon){
                                                $coupon = \App\Models\Coupon::find($usedCoupon->coupon_id);
                                                $discount_amount = $item->pivot->total_price * ($coupon->amount_pc / 100);
                                            }else{
                                                $discount_amount = 0;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ \App\Models\Product::find($item->pivot->product_id)->title }}</td>
                                            <td>{{ \App\Models\Product::COLORS[$item->pivot->color] }}</td>
                                            <td>{{ $item->pivot->count }}</td>
                                            <td>{{ \App\Models\Product::UNITS[$item->pivot->unit] }}</td>
                                            <td>{{ number_format($item->pivot->price) }}</td>
                                            <td>{{ number_format($item->pivot->total_price) }}</td>
                                            <td>{{ number_format($discount_amount) }}</td>
                                            <td>{{ number_format($item->pivot->extra_amount) }}</td>
                                            <td>{{ number_format($item->pivot->total_price - ($item->pivot->extra_amount + $discount_amount)) }}</td>
                                            <td>{{ number_format($item->pivot->tax) }}</td>
                                            <td>{{ number_format($item->pivot->invoice_net) }}</td>
                                        </tr>

                                        @php
                                            $sum_total_price += $item->pivot->total_price;
                                            $sum_discount_amount += $discount_amount;
                                            $sum_extra_amount += $item->pivot->extra_amount;
                                            $sum_total_price_with_off += $item->pivot->total_price - ($item->pivot->extra_amount + $discount_amount);
                                            $sum_tax += $item->pivot->tax;
                                            $sum_invoice_net += $item->pivot->invoice_net;
                                        @endphp
                                    @endforeach

                                    {{-- other products --}}
                                    @foreach($invoice->other_products as $key => $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item->title }}</td>
                                            <td>{{ $item->color }}</td>
                                            <td>{{ $item->count }}</td>
                                            <td>{{ \App\Models\Product::UNITS[$item->unit] }}</td>
                                            <td>{{ number_format($item->price) }}</td>
                                            <td>{{ number_format($item->total_price) }}</td>
                                            <td>{{ number_format($item->discount_amount) }}</td>
                                            <td>{{ number_format($item->extra_amount) }}</td>
                                            <td>{{ number_format($item->total_price - ($item->extra_amount + $item->discount_amount)) }}</td>
                                            <td>{{ number_format($item->tax) }}</td>
                                            <td>{{ number_format($item->invoice_net) }}</td>
                                        </tr>

                                        @php
                                            $sum_total_price += $item->total_price;
                                            $sum_discount_amount += $item->discount_amount;
                                            $sum_extra_amount += $item->extra_amount;
                                            $sum_total_price_with_off += $item->total_price - ($item->extra_amount + $item->discount_amount);
                                            $sum_tax += $item->tax;
                                            $sum_invoice_net += $item->invoice_net;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="6">جمع کل</td>
                                        <td>{{ number_format($sum_total_price) }}</td>
                                        <td>{{ number_format($sum_discount_amount) }}</td>
                                        <td>{{ number_format($sum_extra_amount) }}</td>
                                        <td>{{ number_format($sum_total_price_with_off) }}</td>
                                        <td>{{ number_format($sum_tax) }}</td>
                                        <td>{{ number_format($sum_invoice_net) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="p-0 title-sec" colspan="6">تخفیف نهایی</th>
                                        <th class="p-0 title-sec" colspan="6">مبلغ فاکتور پس از تخفیف نهایی</th>
                                    </tr>
                                    <tr>
                                        <td colspan="6">{{ number_format($invoice->discount) }}</td>
                                        <td colspan="6">{{ number_format($sum_invoice_net - $invoice->discount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <div class="d-flex">
                                                <span class="mr-4">شرایط و نحوه فروش</span>
                                                <div class="d-flex">
                                                    نقدی<input type="checkbox">
                                                </div>
                                                <div class="d-flex ml-5">
                                                    غیر نقدی<input type="checkbox">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><small>توضیحات</small></td>
                                        <td colspan="10">{{ $invoice->description }}</td>
{{--                                        <td colspan="10">لطفا مبلغ فاکتور را به شماره شبا IR55 0110 0000 0010 3967 1380 01 نزد بانک صنعت و معدن شعبه مرکزی واریز فرمایید.</td>--}}
                                    </tr>
                                    @if($invoice->type == 'official')
                                        <tr>
                                            <td colspan="12"><strong>تمام اجناس ارائه شده دارای 18 ماه گارانتی از سوی شرکت صنایع ماشین های اداری ماندگار پارس می باشد</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="6" id="seller_sign_sec">
                                            @if($invoice->status == "invoiced")
                                            <img src="{{ $invoice->user->sign_image ?? '' }}" class="sign">
                                            @if($invoice->type == 'official')
                                            <img src="{{ asset('/assets/media/image/stamp.png') }}" class="stamp">
                                            @endif
                                            @endif
                                            <small>مهر و امضای فروشنده</small>
                                        </td>
                                        <td colspan="6"><small>مهر و امضای خریدار</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="pb-2 d-flex justify-content-between px-3" id="print_sec">
            <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fa fa-chevron-right mr-2"></i>برگشت</a>
            <button class="btn btn-info" id="btn_print"><i class="fa fa-print mr-2"></i>چاپ</button>
            <form action="{{ route('invoices.download') }}" method="post">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <button class="btn btn-danger"><i class="fa fa-file-pdf mr-2"></i>دانلود</button>
            </form>
        </div>
    </div>
{{--    <div class="alert alert-info">--}}
{{--        <i class="fa fa-info-circle font-size-20"></i>--}}
{{--        برای اشتراک گذاری پیش فاکتور/فاکتور ابتدا با استفاده از دکمه <a href="javascript:void(0)">چاپ</a> آن را چاپ کرده یا از <a href="#screenshotModal" data-toggle="modal">اسکرین شات مرورگر</a> استفاده کنید--}}
{{--    </div>--}}
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

