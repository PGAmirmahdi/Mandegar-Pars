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
                    <input type="text" id="start_date" name="start_date" class="form-control date-picker-shamsi-list"
                           autocomplete="off" placeholder="از تاریخ" value="{{ request()->start_date ?? null }}"
                           form="search_form">
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list"
                           autocomplete="off" placeholder="تا تاریخ" value="{{ request()->end_date ?? null }}"
                           form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary" form="search_form"><i class="fa fa-filter"></i>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                @foreach($analyses as $month => $monthAnalyses)
                    <h5 class="mt-4">{{ $monthNames[$month] }}</h5> <!-- نمایش نام ماه به فارسی -->
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>دسته‌بندی</th>
                            <th>برند</th>
                            <th>از تاریخ</th>
                            <th>تا تاریخ</th>
                            @can('admin')
                                <th>تاریخ ایجاد</th>
                            @endcan
                            <th>محصولات</th>
                            @can('analyse-edit')
                                <th>
                                    ویرایش
                                </th>
                            @endcan
                            @can('analyse-delete')
                                <th>
                                    حذف
                                </th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @php $index = 0; @endphp
                        @foreach($monthAnalyses as $analyse)
                            <tr>
                                <td>{{ ++$index }}</td>
                                <td>{{ $analyse->category->name }}</td>
                                <td>{{ $analyse->brand->name }}</td>
                                <td>{{ \Verta::parse($analyse->date)->format('%d %B %Y') }}</td>
                                <td>{{ \Verta::parse($analyse->to_date)->format('%d %B %Y') }}</td>
                                @can('admin')
                                    <td>{{\Verta::parse($analyse->created_at)->format('H:i')}}</td>
                                @endcan
                                <td>
                                    <a href="{{ route('analyse.show', $analyse->id) }}"
                                       class="btn btn-lg btn-outline-behance btn-floating">
                                        <i class="fa fa-chart-simple"></i>
                                    </a>
                                </td>
                                @php
                                    $isDisabled = $analyse->created_at->addDay() < \Carbon\Carbon::now();
                                @endphp
                                @can('analyse-edit')
                                    <td>
                                        <a class="btn btn-primary btn-floating" href="{{ route('analyse.edit', $analyse->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('analyse-delete')
                                    <td>
                                        <button
                                            class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ $isDisabled ? '#' : route('analyse.destroy', $analyse->id) }}"
                                            data-id="{{ $analyse->id }}"
                                            {{ $isDisabled ? 'disabled' : '' }}
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </div>
@endsection
