@extends('panel.layouts.master')
@section('title','لیست قیمت ها')
@php
    $sellers = \Illuminate\Support\Facades\DB::table('price_list_sellers')->get();
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex row justify-content-between align-items-center px-4">
                <h3 class="text-left mb-4 d-inline">لیست قیمت ها - (ریال)</h3>
                <h3 class="text-right mb-4 d-inline">{{ verta(now())->format('Y/m/d') . ' - ' .  verta(now())->format('l')}}</h3>
            </div>
            <form action="{{ route('other-prices-list') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="category" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">دسته بندی (همه)</option>
                        @foreach(\App\Models\Category::all(['id','name']) as $category)
                            <option value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="product_id" form="search_form"
                            class="js-example-basic-single select2-hidden-accessible" data-select2-id="7">
                        <option value="all">نام کالا</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request()->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div style="overflow-x: auto" class="tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th colspan="{{$sellers->count()}}">
                            <i class="fa fa-plus text-success mr-2" data-toggle="modal" data-target="#addSellerModal"
                               id="btn_seller"></i>
                            فروشنده
                        </th>
                    </tr>
                    <tr>
                        <th>
                            <strong class="bolder">ردیف</strong>
                        </th>
                        <th>
                            <div style="display: block ruby">
                                <strong class="bolder">مدل</strong>
                                <span class="bolder">(برند)</span>
                            </div>
                        </th>
                        @foreach($sellers as $seller)
                            <th class="seller">
                                <i class="fa fa-times text-danger btn_remove_seller mr-2" data-toggle="modal"
                                   data-target="#removeSellerModal" data-seller_id="{{ $seller->id }}"></i>
                                <span>{{ $seller->name }}</span>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        {{-- استفاده از جدول محصولات --}}
                        <tr>
                            <th>
                                <strong class="bolder" style="font-family:sans-serif !important">{{ ++$key }}</strong>
                            </th>
                            <th style="display: block ruby">
                                <strong class="bolder" style="font-family:sans-serif !important">{{ $product->title }}</strong>
                                <strong class="bolder" style="font-family:sans-serif !important">({{ $product->productModels->slug }})</strong>
                            </th>
                            @for($i = 0; $i < $sellers->count(); $i++)
                                @php
                                    $item = \Illuminate\Support\Facades\DB::table('price_list')
                                        ->where(['product_id' => $product->id, 'seller_id' => $sellers[$i]->id])
                                        ->first();
                                @endphp
                                <td>
                                    <input type="text" class="item" data-product_id="{{ $product->id }}"
                                           data-seller_id="{{ $sellers[$i]->id }}"
                                           value="{{ $item ? number_format($item->price) : '-' }}">
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
        </div>
    </div>
@endsection
