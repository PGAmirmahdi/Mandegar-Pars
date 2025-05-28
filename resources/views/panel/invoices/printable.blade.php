@php use Illuminate\Support\Facades\DB; @endphp
@extends('panel.layouts.master')
@section('title', 'چاپ پیش فاکتور')
@php
    $sidebar = false;
    $header  = false;

    $sum_total_price = 0;
    $sum_discount_amount = 0;
    $sum_extra_amount = 0;
    $sum_total_price_with_off = 0;
    $sum_tax = 0;
    $sum_invoice_net = 0;
    $showTax = $invoice->created_in !== 'website';
    $i = 1;
@endphp
@section('styles')
    <style>
        #products_table input, #products_table select {
            width: auto !important;
        }

        .title-sec {
            background: #ececec !important;
        }

        .main-content {
            margin: 0 !important;
        }

        .me-100 {
            margin-right: 100px !important;
        }

        @page {
            size: A4 landscape !important;
        }

        @media print {
            body {
                transform: scale(0.9) !important;
            }
        }

        body {
            padding: 0 !important;
        }

        main {
            padding: 0 !important;
        }

        table th, td {
            padding: 4px !important;
            border: 2px solid #000 !important;
            font-size: 16px !important;
        }

        table th {
            font-weight: bold !important;
        }

        table tr {
            padding: 0 !important;
            border: 2px solid #000 !important;
        }

        #printable_sec {
            padding: 0 !important;
        }

        .card {
            margin: 0 !important;
        }

        .guide_box {
            text-align: center !important;
        }

        #seller_sign_sec {
            position: relative !important;
        }

        #seller_sign_sec small {
            position: absolute !important;
        }

        #seller_sign_sec .sign {
            position: absolute !important;
            top: -60px !important;
            left: 34% !important;
            width: 10rem !important;
        }

        #seller_sign_sec .stamp {
            position: absolute !important;
            top: -18px !important;
            left: 35% !important;
            width: 12rem !important;
        }

        html, body, main {
            height: 100% !important;
        }

        .card {
            min-height: 100% !important;
            max-height: 130% !important;
        }

        .content-page {
            margin-right: 0 !important;
            overflow: unset !important;
            padding: 0 !important;
            min-height: 0 !important;
        }

        * {
            color: #000 !important;
        }

        .btn, .fa {
            color: #fff !important
        }

        .table:not(.table-bordered) td {
            line-height: 1;
        }

        .content-page {
            height: 100% !important
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
                    <div class="col-3 text-end">
                        <h3>پیش فاکتور فروش کالا و خدمات</h3>
                    </div>
                    <div class="col-2"></div>
                    <div class="col-2 text-center">
                        <p class="m-0"> شماره سریال: {{ $invoice->order->code??$invoice->id }}</p>
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
                            <th class="text-center py-1 title-sec">مشخصات فروشنده</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-center">
                                <div class="mb-3">
                                    <span class="me-100">نام شخص حقیقی/حقوقی: صنایع ماشین های اداری ماندگار پارس</span>
                                    <span class="me-100">شماره اقتصادی: 14011383061</span>
                                    <span class="me-100">شماره ثبت/شماره ملی: 9931</span>
                                    <span class="me-100">شناسه ملی: 14011383061</span>
                                </div>
                                <div>
                                    <span class="me-100">نشانی: صفادشت،شهرک صنعتی صفادشت، بلوار خرداد، بین خیابان پنجم و ششم غربی، پلاک 228</span>
                                    <span class="me-100">کد پستی: 3164114855</span>
                                    <span class="me-100">شماره تلفن: 02165425052</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered mb-4">
                        <thead>
                        <tr>
                            <th class="text-center py-1 title-sec">مشخصات خریدار</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-center">
                                <div class="mb-3">
                                    <span class="me-100">نام شخص حقیقی/حقوقی: {{ $invoice->customer->name }}</span>
                                    <span class="me-100">شماره اقتصادی: {{ $invoice->economical_number }}</span>
                                    <span class="me-100">شماره ثبت/شماره ملی: {{ $invoice->national_number }}</span>
                                    <span class="me-100">استان: {{ $invoice->province }}</span>
                                </div>
                                <div>
                                    <span class="me-100">شهر: {{ $invoice->city }}</span>
                                    <span class="me-100">کد پستی: {{ $invoice->postal_code }}</span>
                                    <span class="me-100">نشانی: {{ $invoice->address }}</span>
                                    <span class="me-100">شماره تلفن: {{ $invoice->phone }}</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="col-12 mb-3">
                        <div>
                            <table class="table text-center" border="2">
                                <thead>
                                <tr>
                                    <th class="py-1 title-sec" colspan="12">مشخصات کالا یا خدمات مورد معامله</th>
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
                                    <th @if(!$showTax) colspan="2" @endif>مبلغ کل پس از تخفیف و اضافات</th>
                                    @if($showTax)
                                        <th>جمع مالیات و عوارض</th>
                                    @endif
                                    <th>خالص فاکتور</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- artin products --}}
                                @if(!$invoice->other_products)
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
                                            <td>{{ $item->pivot->color}} </td>
                                            <td>{{ $item->pivot->count }}</td>
                                            <td>{{ \App\Models\Product::UNITS[$item->pivot->unit] }}</td>
                                            <td>{{ number_format($item->pivot->price) }}</td>
                                            <td>{{ number_format($item->pivot->total_price) }}</td>
                                            <td>{{ number_format($discount_amount) }}</td>
                                            <td>{{ number_format($item->pivot->extra_amount) }}</td>
                                            <td @if(!$showTax) colspan="2" @endif>{{ number_format($item->pivot->total_price - ($item->pivot->extra_amount + $discount_amount)) }}</td>
                                            @if($showTax)
                                                <td>{{ number_format($item->pivot->tax) }}</td>
                                            @endif
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
                                @endif
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
                                        @if($showTax)
                                            <td>{{ number_format($item->tax) }}</td>
                                        @endif
                                        <td>{{ number_format($item->invoice_net) }}</td>
                                    </tr>

                                    @php
                                        $sum_total_price += $item->total_price;
                                        $sum_discount_amount += $item->discount_amount;
                                        $sum_extra_amount += $item->extra_amount;
                                        $sum_total_price_with_off += $item->total_price - ($item->extra_amount + $item->discount_amount);
                                        if($showTax){$sum_tax += $item->tax;}
                                        $sum_invoice_net += $item->invoice_net;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td colspan="6">جمع کل</td>
                                    <td>{{ number_format($sum_total_price) }}</td>
                                    <td>{{ number_format($sum_discount_amount) }}</td>
                                    <td>{{ number_format($sum_extra_amount) }}</td>
                                    <td @if(!$showTax) colspan="2" @endif>{{ number_format($sum_total_price_with_off) }}</td>
                                    @if($showTax)
                                        <td>{{ number_format($sum_tax) }}</td>
                                    @endif
                                    <td>{{ number_format($sum_invoice_net) }}</td>
                                </tr>
                                <tr>
                                    <th class="py-1 title-sec" colspan="6">تخفیف نهایی</th>
                                    @if(isset($invoice->shipping_cost))
                                        <th class="py-1 title-sec" colspan="2">هزینه حمل و نقل</th>
                                    @endif
                                    <th class="py-1 title-sec" @if(isset($invoice->shipping_cost)) colspan="4"
                                        @else colspan="6" @endif>مبلغ فاکتور پس از تخفیف نهایی
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="6">{{ number_format($invoice->discount) }}</td>
                                    @if(isset($invoice->shipping_cost))
                                        <th colspan="2">{{ number_format($invoice->shipping_cost) }}</th>
                                    @endif
                                    <td @if(isset($invoice->shipping_cost)) colspan="4"
                                        @else colspan="6" @endif>{{ number_format($sum_invoice_net - $invoice->discount +(isset($invoice->shipping_cost) ? $invoice->shipping_cost : 0)) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="d-flex">
                                            <span class="me-4">شرایط و نحوه فروش:</span>
                                            <div class="d-flex">
                                                @foreach(\App\Models\Order::Payment_Type as $key => $label)
                                                    @if($invoice->payment_type === $key)
                                                        <span>{{$label}}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="8" class="text-start">
                                        {{change_number_to_words($sum_invoice_net - $invoice->discount +(isset($invoice->shipping_cost) ? $invoice->shipping_cost : 0))}}
                                        ریال
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><small>توضیحات</small></td>
                                    <td colspan="10">{!! nl2br(e($invoice->description )) !!}</td>
                                </tr>
                                <tr>
                                    <td colspan="12">
                                        خواهشمند است مبلغ فاكتور را به شماره شبا IR550110000000103967138001 نزد بانك
                                        صنعت و معدن شعبه مرکزی واريز نماييد. با تشكر
                                        <br>
                                        <br>
                                        آدرس سایت https://artintoner.com
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="6" id="seller_sign_sec">
                                        <img src="{{ $invoice->user->sign_image ?? '' }}" class="sign">
                                        <img src="{{ asset('/assets/media/image/stamp.png') }}" class="stamp">
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
            <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fa fa-chevron-right me-2"></i>برگشت</a>
            {{--            <button class="btn btn-info" id="btn_print"><i class="fa fa-print me-2"></i>چاپ</button>--}}
            <form action="{{ route('invoices.download') }}" method="post">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <button class="btn btn-danger"><i class="fa fa-file-pdf me-2"></i>دانلود</button>
            </form>
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

