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
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="category" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">شرح کالا (همه)</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="model" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="2">
                        <option value="all">برند (همه)</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}" {{ request()->model == $model->id ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <input type="text" id="start_date" name="start_date" class="form-control date-picker-shamsi-list" autocomplete="off" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list" autocomplete="off" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary" form="search_form"><i class="fa fa-filter"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>دسته بندی</th>
                        <th>برند</th>
                        <th>تاریخ آنالیز</th>
                        <th>ریز جزئیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                    $index = 0;
                    @endphp
                    @foreach($analyses as $analyse)
                        <tr>
                            <td>{{ $index += 1 }}</td>
                            <td>{{ $analyse->category->name }}</td>
                            <td>{{ $analyse->brand->name }}</td>
                            <td><strong style="font-size: 20px">{{ $analyse->date }}</strong></td>
                            <td>
                                <a href="{{ route('analyse.show', $analyse->id) }}" class="btn btn-lg btn-outline-behance btn-floating">
                                    <i class="fa fa-chart-simple"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
