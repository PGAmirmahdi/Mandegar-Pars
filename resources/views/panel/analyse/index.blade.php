@extends('panel.layouts.master')
@section('title', 'لیست آنالیز')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست آنالیز</h6>
                    <a href="{{ route('analyse.step1') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت آنالیز
                    </a>
            </div>
            <form method="GET" action="{{ route('analyse.step1') }}" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <label for="start_date">از تاریخ</label>
                    <input type="text" id="start_date" name="start_date" class="form-control date-picker-shamsi-list" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <label for="end_date">تا تاریخ</label>
                    <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}" form="search_form">
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
                        <th>تاریخ آنالیز</th>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($analyses as $index => $analyse)
                        @foreach($analyse->analyseProducts as $product) <!-- اضافه کردن این خط برای جدا کردن محصولات -->
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $analyse->brand->slug ?? 'نامشخص' }}</td>
                            <td>{{ $analyse->date }}</td>
                            <td>{{ $product->product->title ?? 'نامشخص' }}</td> <!-- نام محصول -->
                            <td>{{ $product->quantity }}</td> <!-- تعداد محصول -->
                        </tr>
                        @endforeach
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

