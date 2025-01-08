@extends('panel.layouts.master')
@section('title','لیست قیمت ها')
@php
    $sellers = \Illuminate\Support\Facades\DB::table('price_list_sellers')->get();
@endphp
@section('content')
    <style>
        #price_table td:hover {
            background-color: #e3daff !important;
        }

        #price_table .item {
            text-align: center;
            background: transparent;
            border: 0;
        }

        #price_table .item:focus {
            border-bottom: 2px solid #5d4a9c;
        }

        #btn_save {
            width: 100%;
            justify-content: center;
            border-radius: 0;
            padding: .8rem;
            font-size: larger;
        }

        #price_table {
            box-shadow: 0 5px 5px 0 lightgray;
        }

        #btn_model, #btn_seller, .btn_remove_seller, .btn_remove_model {
            vertical-align: middle;
            cursor: pointer;
        }

        /* table th sticky */
        .tableFixHead {
            overflow: auto !important;
            height: 800px !important;
        }

        .tableFixHead thead th {
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
                        @foreach(\App\Models\Product::all()->where('status','=','approved') as $product)
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
                                    <input type="text" class="item readonly disabled" data-product_id="{{ $product->id }}"
                                           data-seller_id="{{ $sellers[$i]->id }}"
                                           value="{{ $item ? number_format($item->price) : '-' }}" readonly disabled>
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
                <div class="d-flex justify-content-center">{{ $products->appends(request()->all())->links() }}</div>
            </div>
        </div>
    </div>
@endsection
