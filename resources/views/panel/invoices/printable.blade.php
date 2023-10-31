@extends('panel.layouts.master')
@section('title', 'چاپ پیش فاکتور')
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

        .guide_box{
            text-align: center;
        }
    </style>

@endsection
@section('content')
    {{--  Screenshot Guide Modal  --}}
    <div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="screenshotModalLabel">راهنمای اسکرین شات</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="guide_box">
                        <strong>Google Chrome</strong>
                        <p>از کلید <code>Print Screen</code> در کیبورد خود استفاده کنید</p>
                    </div>
                    <div class="guide_box">
                        <strong>Mozilla Firefox</strong>
                        <p>از کلید های ترکیبی <code>Ctrl + Shift + S</code> استفاده کنید</p>
                    </div>
                    <div class="guide_box">
                        <strong>Safari</strong>
                        <p>از کلید های ترکیبی <code>Command + Shift + 4</code>  استفاده کنید </p>
                    </div>
                    <div class="guide_box">
                        <strong>Microsoft Edge</strong>
                        <p>از کلید <code>Print Screen</code> در کیبورد خود استفاده کنید</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    {{--  End Screenshot Guide Modal  --}}

    <div class="card">
        <div class="card-body" id="printable_sec">
            <div class="card-title">
                <div class="row">
                    <div class="col-7 text-right">
                        @if(request()->type == 'pishfactor')
                            <h3>پیش فاکتور</h3>
                        @else
                            <h3>صورت حساب فروش کالا و خدمات</h3>
                        @endif
                    </div>
                    <div class="col-3"></div>
                    <div class="col-2 text-center">
                        <p class="m-0">شماره سریال: {{ request()->type == 'pishfactor' ? $invoice->id : $factor->id }}</p>
                        <hr class="mt-0">
                        <p class="m-0">تاریخ: {{ request()->type == 'pishfactor' ? verta($invoice->created_at)->format('Y/m/d') : verta($factor->created_at)->format('Y/m/d') }}</p>
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
                        <div class="overflow-auto">
                            <table class="table table-bordered text-center">
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
                                            <td>{{ ++$key }}</td>
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
                                        <td colspan="10">لطفا مبلغ فاکتور را به شماره شبا IR55 0110 0000 0010 3967 1380 01 نزد بانک صنعت و معدن شعبه مرکزی واریز فرمایید.</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6"><small>مهر و امضای فروشنده</small></td>
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
        </div>
    </div>
    <div class="alert alert-info">
        <i class="fa fa-info-circle font-size-20"></i>
        برای اشتراک گذاری پیش فاکتور/فاکتور ابتدا با استفاده از دکمه <a href="javascript:void(0)">چاپ</a> آن را چاپ کرده یا از <a href="#screenshotModal" data-toggle="modal">اسکرین شات مرورگر</a> استفاده کنید
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

