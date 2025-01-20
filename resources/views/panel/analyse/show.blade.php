@extends('panel.layouts.master')
@section('title', 'ریز جزئیات آنالیز')
@section('content')
    <div class="alert alert-info">
        <i class="fa fa-info-circle font-size-20 align-middle"></i>
        <strong>توجه!</strong>
        موجودی انبار موجودی زمان ثبت آنالیز هستش و موجودی لحظه ای موجودی فعلی انبار هستش
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ریز جزئیات آنالیز در تاریخ {{ $analyse->date ?? 'نامشخص' }}</h6>
                <a href="{{route('analyse.index')}}" class="btn btn-danger">بازگشت</a>
            </div>
            <!-- فرم جستجو -->
            <form method="GET" action="{{ route('analyse.show', $analyse->id) }}" id="search_form" class="mb-3">
                <div class="row">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible">
                            <option value="all" {{ request()->product == 'all' ? 'selected' : '' }}>مدل کالا (همه)</option>
                            @foreach($allProducts as $product)
                                <option value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                    {{ $product->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <button type="submit" class="btn btn-primary">جستجو</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>دسته بندی</th>
                        <th>برند</th>
                        <th>نام محصول</th>
                        <th>تعداد آنالیز</th>
                        <th>موجودی انبار</th>
                        <th>موجودی لحظه ای</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $analyse->category->slug ?? 'نامشخص' }}</td>
                            <td>{{ $analyse->brand->slug ?? 'نامشخص' }}</td>
                            <td>{{ $product->title ?? 'نامشخص' }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>{{ $product->pivot->storage_count ?? 'نامشخص' }}</td>
                            <td>{{ $product->total_count ?? 'نامشخص' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
