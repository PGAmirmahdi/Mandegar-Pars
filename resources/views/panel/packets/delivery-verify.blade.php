@extends('panel.layouts.master')
@section('title', 'تایید تحویل مرسوله')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>تایید تحویل مرسوله</h6>
            </div>
            <form action="{{ route('delivery-verify') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-2 col-lg-2 col-md-2 mb-3">
                        <label for="code">کد تحویل <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" id="code" value="{{ old('code') }}" style="letter-spacing: 0.8em" maxlength="5">
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary" type="submit">تایید کد</button>
                </div>
            </form>

            @isset($packet)
                <div class="invoice-top mt-5">
                    <div class="alert alert-success text-center">
                        تحویل مرسوله با کد {{ $packet->delivery_code }} تایید شد
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="invoice-number mb-30">
                                <h2 class="name mb-10">نام شخص حقیقی/حقوقی : {{$packet->invoice->customer->name}}</h2>
                                <p class="invo-addr-1">
                                    شماره ثبت/ملی : {{$packet->invoice->customer->national_number}} <br/>
                                    کد پستی : {{$packet->invoice->customer->postal_code}} <br/>
                                    شماره تماس : {{$packet->invoice->customer->phone1}} <br/>
                                    آدرس : {{$packet->invoice->customer->province}}
                                    ،{{$packet->invoice->customer->city}} {{$packet->invoice->customer->address1}} <br/>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoice-center">
                    <div class="table-responsive">
                        <table class="table mb-0 table-striped invoice-table">
                            <thead class="bg-primary">
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
                            @php
                                // Decode JSON data from the order
                                $productsData = json_decode($packet->invoice->order->products);
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    \Illuminate\Support\Facades\Log::error("JSON decode error: " . json_last_error_msg());
                                    return;
                                }
                                $products = is_array($productsData) ? $productsData : [];
                                $otherProducts = isset($productsData->other_products) ? $productsData->other_products : [];

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
                                    $total = $total ?? 0; // اطمینان از مقداردهی اولیه

                                    if (!empty($order->created_in) && $order->created_in == 'website') {
                                        $total += (($product->counts ?? $product->other_counts ?? 0) *
                                                   (($product->prices ?? $product->other_prices ?? 0) + ($order->shipping_cost ?? 0)));
                                    } else {
                                        $total += (($product->counts ?? $product->other_counts ?? 0) *
                                                   ($product->prices ?? $product->other_prices ?? 0));
                                    }
                                @endphp
                            @endforeach

                            <tr class="tr2">
                                @if(!empty($order->created_in) && $order->created_in == 'website')
                                    <td>هزینه حمل و نقل</td>
                                    <td class="f-w-600 text-start active-color">{{ number_format($order->shipping_cost) }}</td>
                                @else
                                    <td></td>
                                    <td></td>
                                @endif
                                <td></td>
                                <td></td>
                                <td class="text-center f-w-600 active-color">جمع کل</td>
                                <td class="f-w-600 text-start active-color">{{ number_format($total) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endisset
        </div>
    </div>
@endsection
