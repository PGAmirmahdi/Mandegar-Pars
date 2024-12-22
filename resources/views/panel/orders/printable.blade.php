<!DOCTYPE html>
<html lang="zxx" dir="rtl">
<head>
    <title>چاپ سفارش مشتری</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->

    <link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.min-invoice.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/style-invoice.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/font/primary-iran-yekan.css">
</head>
<body>

{{--@dd($order)--}}
<!-- Invoice 1 start -->
<div class="invoice-1 invoice-content" style="font-family: primary-font">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner clearfix">
                    <div class="invoice-info clearfix" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row g-0">
                                <div class="col-sm-6">
                                    <div class="invoice-logo">
                                        <!-- logo started -->
                                        <div class="logo">
                                            <img src="/assets/images/header-logo.png"
                                                 alt="logo">
                                        </div>
                                        <!-- logo ended -->
                                    </div>
                                </div>
                                <div class="col-sm-6 invoice-id">
                                    <div class="info">
                                        <h1 class="color-white inv-header-1">سفارش مشتری</h1>
                                        <p class="color-white mb-1"> شماره سفارش : {{$order->code}}</p>
                                        <p class="color-white mb-0"> تاریخ
                                            : {{verta($order->created_at)->format('%Y/%m/%d')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-top">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="invoice-number mb-30">
                                        <h4 class="inv-title-1 mb-3">مشخصات مشتری</h4>
                                        <h2 class="name mb-10">نام شخص حقیقی/حقوقی : {{$order->customer->name}}</h2>
                                        <h3 class="name mb-10">نوع فروش : {{\App\Models\Customer::CUSTOMER_TYPE[$order->customer->customer_type]}}</h3>
                                        <p class="invo-addr-1">
                                            شماره ثبت/ملی : {{$order->customer->national_number}} <br/>
                                            کد پستی : {{$order->customer->postal_code}} <br/>
                                            شماره تماس : {{$order->customer->phone1}} <br/>
                                            آدرس : {{$order->customer->province}}
                                            ،{{$order->customer->city}} {{$order->customer->address1}} <br/>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-center">
                            <div class="table-responsive">
                                <table class="table mb-0 table-striped invoice-table">
                                    <thead class="bg-active">
                                    <tr class="tr">
                                        <th class="pl0 text-end">ردیف</th>
                                        <th class="pl0 text-end">کالا</th>
                                        <th class="text-center">تعداد</th>
                                        <th class="text-center">رنگ</th>
                                        <th class="text-center">قیمت (ریال)</th>
                                        <th class="text-start">جمع (ریال)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{--                                                                @dd(array_merge(json_decode($order->products)->products, json_decode($order->products)->other_products))--}}

                                    @php
                                        // Decode JSON data from the order
                                        $productsData = json_decode($order->products);

                                        // Extract products and other products
                                        $products = $productsData->products ?? [];
                                        $otherProducts = $productsData->other_products ?? [];

                                        // Merge products and other products, ensuring they are arrays
                                        $mergedProducts = array_merge(
                                            is_array($products) ? $products : [$products],
                                            is_array($otherProducts) ? $otherProducts : [$otherProducts]
                                        );

                                        $total = 0;
                                    @endphp

                                    @foreach($mergedProducts as $product)
                                        <tr class="tr">
                                            <td>
                                                <div class="item-desc-1 text-end">
                                                    <span>{{ $loop->index + 1 }}</span>
                                                </div>
                                            </td>

                                            <td class="pl0">
                                                {{ isset($product->products) ? \App\Models\Product::whereId($product->products)->first()->title : ($product->other_products ?? 'N/A') }}                                            </td>

                                            @php
                                                $units = isset($product->units) ? (\App\Models\Product::UNITS[$product->units] ?? 'N/A') : (\App\Models\Product::UNITS[$product->other_units] ?? 'N/A');
                                            @endphp

                                            <td class="text-center">
                                                {{ ($product->counts ?? $product->other_counts) . ' ' . ($units ?? '') }}
                                            </td>

                                            <td class="text-center">
                                                @php
                                                    $color = isset($product->colors) ? (\App\Models\Product::COLORS[$product->colors] ?? 'N/A') : ($product->other_colors ?? 'N/A');
                                                @endphp
                                                {{ $color }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($product->prices ?? $product->other_prices) ?? 0 }}
                                            </td>

                                            <td class="text-start">
                                                {{
                                                    number_format(
                                                        ($product->counts ?? $product->other_counts ?? 0) *
                                                        ($product->prices ?? $product->other_prices ?? 0)
                                                    )
                                                }}
                                            </td>
                                        </tr>

                                        @php
                                            $total += (($product->counts ?? $product->other_counts ?? 0) *
                                                       ($product->prices ?? $product->other_prices ?? 0));
                                        @endphp
                                    @endforeach


                                    <tr class="tr2">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center f-w-600 active-color">جمع کل</td>
                                        <td class="f-w-600 text-start active-color">{{ number_format($total) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-bottom">
                            <div class="row">
                                <div class="col-lg-6 col-md-8 col-sm-7">
                                    @if(!is_null($order->description))
                                        <div class="mb-30 dear-client">
                                            <h3 class="inv-title-1">توضیحات</h3>
                                            <p>{!! nl2br(e($order->description)) !!}</p>

                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="invoice-btn-section clearfix d-print-none">
                        <a href="{{route('orders.index')}} " class="btn btn-lg btn-print">
                            <i class="fa fa-print"></i>بازگشت
                        </a>
                        <a href="javascript:window.print()" class="btn btn-lg btn-download btn-theme">
                            <i class="fa fa-print"></i>چاپ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 1 end -->

</body>
</html>
