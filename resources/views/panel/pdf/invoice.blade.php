<!doctype html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<style>
    #products_table input, #products_table select {
        width: auto;
    }

    .title-sec {
        background: #ececec;
    }

    .main-content {
        margin: 0 !important;
    }

    body {
        padding: 0;
        text-align: center !important;
    }

    main {
        padding: 0 !important;
    }

    table {
        width: 100% !important;
        /*border-collapse: separate !important;*/
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .table th, .table td {
        padding: 4px !important;
        border: 2px solid #000 !important;
        font-size: 16px !important;
        text-align: center !important;
    }

    .table tr {
        padding: 0 !important;
        border: 2px solid #000 !important;
        text-align: center !important;
    }

    #printable_sec {
        padding: 0;
    }

    .card {
        margin: 0;
    }

    .guide_box {
        text-align: center;
    }
</style>
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
<div style="font-size: 20px;width: 100%;">
    <table>
        <tr>
            <td style="width: 700px">
                @if($invoice->type == 'official')
                    <img src="{{ public_path('/assets/media/image/header-logo.png') }}" style="width: 15rem;">
                @endif
            </td>
            <td>
                <span style="font-size: 25px">سفارش مشتری</span>
            </td>
            <td style="width: 200px">
                <p style="font-size: 15px">شماره سریال: {{ $invoice->id }}</p>
                <br>
                <p style="font-size: 15px">تاریخ: {{ verta($invoice->created_at)->format('Y/m/d') }}</p>
            </td>
        </tr>
    </table>
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
                <td>
                    <div>
                        <span>نام شخص حقیقی/حقوقی: شرکت صنایع ماشین های اداری ماندگار پارس</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره اقتصادی: 14011383061</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره ثبت/شماره ملی: 9931</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شناسه ملی: 14011383061</span>
                    </div>
                    <div style="height: 2rem">&nbsp;</div>
                    <div>
                        <span>نشانی: تهران، شهرستان ملارد، شهرک صنعتی صفادشت، بلوار خرداد، بین خیابان پنجم و ششم غربی، پلاک 228</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>کد پستی: 3164114855</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره تلفن: 02165425053</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                    <div class="mb-3" style="width: 100%">
                        <span>نام شخص حقیقی/حقوقی: {{ $invoice->customer->name }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره اقتصادی: {{ $invoice->economical_number }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره ثبت/شماره ملی: {{ $invoice->national_number }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>استان: {{ $invoice->province }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div style="height: 2rem">&nbsp;</div>
                    <div>
                        <span>شهر: {{ $invoice->city }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>کد پستی: {{ $invoice->postal_code }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>نشانی: {{ $invoice->address }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span>شماره تلفن: {{ $invoice->phone }}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                        <th class="py-1 title-sec" colspan="6">تخفیف نهایی</th>
                        @if($invoice->created_in == 'website')
                            <th class="py-1 title-sec" colspan="2">هزینه حمل و نقل</th>
                        @endif
                        <th class="py-1 title-sec" @if($invoice->created_in == 'website') colspan="4"
                            @else colspan="6" @endif>مبلغ فاکتور پس از تخفیف نهایی</th>
                    </tr>
                    <tr>
                        <td colspan="6">{{ number_format($invoice->discount) }}</td>
                        @if($invoice->created_in == 'website')
                            <th colspan="2">{{ number_format($invoice->shipping_cost) }}</th>
                        @endif
                        <td @if($invoice->created_in == 'website') colspan="4"
                            @else colspan="6" @endif>{{ number_format($sum_invoice_net - $invoice->discount +($invoice->created_in == 'website' ? $invoice->shipping_cost : 0)) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="d-flex">
                                <span class="me-4">شرایط و نحوه فروش</span>
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
                            {{change_number_to_words($sum_invoice_net - $invoice->discount +($invoice->created_in == 'website' ? $invoice->shipping_cost : 0))}} ریال
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><small>توضیحات</small></td>
                        <td colspan="10">{{ $invoice->description }}</td>
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
                    @if($invoice->type == 'official')
                        <tr>
                            <td colspan="12"><strong>تمام اجناس ارائه شده دارای 18 ماه گارانتی از سوی شرکت صنایع ماشین
                                    های اداری ماندگار پارس می باشد</strong></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="6">
                            <small>مهر و امضای فروشنده</small>
                        </td>
                        <td colspan="6"><small>مهر و امضای خریدار</small></td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td>
                            <img src="{{ $invoice->user->sign_image ? public_path($invoice->user->sign_image) : '' }}"
                                 style="width: 10rem">
                            @if($invoice->type == 'official')
                                <img src="{{ public_path('assets/media/image/stamp.png') }}" style="width: 13rem">
                            @endif
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</form>
</body>
</html>

