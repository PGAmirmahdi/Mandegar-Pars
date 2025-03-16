@extends('panel.layouts.master')
@section('title', 'نمایش ' . (in_array(auth()->user()->role->name, [
    'setad_sale', 'internet_sale', 'free_sale',
    'industrial_sale', 'global_sale', 'organization_sale'
])
    ? ' درخواست ' . auth()->user()->role->label
    : 'درخواست های فروش'))
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>{{ in_array(auth()->user()->role->name, [
                        'setad_sale', 'internet_sale', 'free_sale',
                        'industrial_sale', 'global_sale', 'organization_sale'
                    ])
                        ? ' درخواست ' . auth()->user()->role->label
                        : 'درخواست های فروش' }}نمایش </h6>
                @if($sale_price_request->type == 'setad_sale' && in_array($sale_price_request->status, ['winner','lose']))
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-4">
                        <label for="final_result">نتیجه نهایی:</label>
                        <span
                            class="badge @if($sale_price_request->final_result == 'winner') badge-success @elseif($sale_price_request->final_result == 'lose') badge-danger @endif">{{ \App\Models\SalePriceRequest::STATUS[$sale_price_request->final_result] }}</span>
                    </div>
                @endif
            </div>
            <form>
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="col-12 row mb-4">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label class="form-label" for="customer">مشتری حقیقی/حقوقی</label>
                                <select name="customer" id="customer" class="form-control" disabled>
                                    <option value="" disabled selected>انتخاب کنید...</option>
                                    @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                        <option
                                            value="{{ $customer->id }}" {{ $sale_price_request->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->code.' - '.$customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="payment_type">نوع پرداختی</label>
                                <select class="form-control" name="payment_type_display" id="payment_type_display"
                                        disabled>
                                    @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                        <option
                                            value="{{ $key }}" {{ $sale_price_request->payment_type == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($sale_price_request->type == 'setad_sale')
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="date">تاریخ موعد</label>
                                    <input type="text" name="date" class="form-control" id="date"
                                           value="{{ $sale_price_request->date . ' - ' . $sale_price_request->hour }}"
                                           disabled>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="need_no">شماره نیاز</label>
                                    <input type="text" name="need_no" class="form-control" id="need_no"
                                           value="{{ $sale_price_request->need_no }}" disabled>
                                </div>
                            @endif
                            @if($sale_price_request->acceptor)
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="acceptor">تایید کننده</label>
                                    <input type="text" name="acceptor" class="form-control" id="acceptor"
                                           value="{{ $sale_price_request->acceptor->name . ' - ' . $sale_price_request->acceptor->family }}"
                                           disabled>
                                </div>
                            @endif
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                <label for="type">نوع فروش</label>
                                <input type="text" name="type" class="form-control" id="type"
                                       value="{{ \App\Models\SalePriceRequest::TYPE[$sale_price_request->type]}}"
                                       disabled>
                            </div>
                            @if($sale_price_request->type !== 'setad_sale')
                                <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                    <label for="shipping_cost">هزینه ارسال(ریال)</label>
                                    <input type="text" name="shipping_cost" class="form-control" id="shipping_cost"
                                           value="{{ number_format($sale_price_request->shipping_cost) }}"
                                           disabled>
                                </div>
                            @endif
                        </div>
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>مدل</th>
                                <th>دسته‌بندی</th>
                                <th>تعداد</th>
                                <th>قیمت پیشنهادی کارشناس فروش(ریال)</th>
                                <th>قیمت نهایی مدیر(ریال)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($sale_price_request->products) as $index => $item)
                                <tr>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_name[{{ $index }}]" value="{{ $item->product_name }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="product_model[{{ $index }}]" value="{{ $item->product_model }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="category_name[{{ $index }}]" value="{{ $item->category_name }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="count[{{ $index }}]"
                                               value="{{ $item->count }}" readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text" name="price[{{ $index }}]"
                                               value="{{ isset($item->product_price) ? number_format($item->product_price) : "بدون قیمت" }}"
                                               readonly>
                                    </td>
                                    <td>
                                        <input class="form-control readonly" type="text"
                                               name="final_price[{{ $index }}]"
                                               value="{{ isset($item->final_price) ? number_format($item->final_price) : "بدون قیمت" }}"
                                               readonly>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <?php
                                $total_expert_price = 0;
                                $total_manager_price = 0;
                                ?>
                                @foreach(json_decode($sale_price_request->products) as $index => $item2)
                                        <?php
                                        $total_expert_price += isset($item2->product_price) ? $item2->product_price * $item2->count : 0;
                                        if (isset($item2->final_price) && $item2->final_price !== null) {
                                            $total_manager_price += $item2->final_price * $item2->count;
                                        }
                                        ?>
                                @endforeach
                                    <?php
                                    $total_expert_price += $sale_price_request->shipping_cost;
                                    $total_manager_price += $sale_price_request->shipping_cost;
                                    ?>
                                <td class="text-center font-weight-bold" colspan="1">
                                    مجموع قیمت کارشناس(ریال):
                                </td>
                                <td class="text-center font-weight-bold" colspan="1">
                                    {{ number_format($total_expert_price) }}
                                </td>
                                <td class="text-center font-weight-bold" colspan="1">
                                    مجموع قیمت مدیر(ریال):
                                </td>
                                <td class="text-center font-weight-bold" colspan="1">
                                    {{ $total_manager_price > $sale_price_request->shipping_cost ? number_format($total_manager_price) : 'ثبت نشده' }}
                                </td>
                                <td class="text-center font-weight-bold" colspan="1">
                                    اختلاف قیمت(ریال):
                                </td>
                                <td class="text-center font-weight-bold" colspan="1">
                                    {{ $total_manager_price > $sale_price_request->shipping_cost ? number_format($total_manager_price - $total_expert_price) : 'ثبت نشده' }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-12 row mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description" class="form-control" rows="10"
                                      disabled>{{ $sale_price_request->description }}</textarea>
                        </div>
                        @if($sale_price_request->type == 'setad_sale' && in_array($sale_price_request->status, ['winner','lose']))
                            <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                                <label for="type">توضیحات نهایی</label>
                                <textarea name="description" id="description" class="form-control" rows="10"
                                          disabled>{{ $sale_price_request->final_description }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
            <a href="{{ route('sale_price_requests.index',['type=' . $sale_price_request->type]) }}" class="btn btn-secondary">
                بازگشت
            </a>
        </div>
    </div>
@endsection
