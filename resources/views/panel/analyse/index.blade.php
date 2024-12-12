@extends('panel.layouts.master')
@section('title', 'لیست آنالیز')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست آنالیز</h6>
                    <a href="{{ route('analyse.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت آنالیز
                    </a>
            </div>
            <form method="GET" action="{{ route('analyse.index') }}" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <label for="start_date">از تاریخ</label>
                    <input type="text" id="start_date" name="start_date" class="form-control date-picker-shamsi-list" autocomplete="off" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <label for="end_date">تا تاریخ</label>
                    <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list" autocomplete="off" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100" form="search_form">فیلتر</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>برند</th>
                        <th>تاریخ آخرین آنالیز</th>
                        <th>نام محصول</th>
                        <th>تعداد کل</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupedProducts as $index => $groupedProduct)
                        @php
                            $product = \App\Models\Product::find($groupedProduct->product_id); // دریافت اطلاعات محصول
                            $lastAnalyse = \App\Models\Analyse::find($groupedProduct->last_analyse_id); // دریافت آخرین آنالیز
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $lastAnalyse->brand->slug ?? 'نامشخص' }}</td>
                            <td>{{ $lastAnalyse->date ?? 'نامشخص' }}</td>
                            <td>{{ $product->title ?? 'نامشخص' }}</td>
                            <td>{{ $groupedProduct->total_quantity }}</td>
                            <td>
                                <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('analyse.destroy',$lastAnalyse->id) }}" data-id="{{ $lastAnalyse->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

