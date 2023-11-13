@extends('panel.layouts.master')
@section('title', 'فاکتور ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>فاکتور ها</h6>
            </div>
            <form action="{{ route('factors.search') }}" method="post" id="search_form">
                @csrf
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="customer_id" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">خریدار (همه)</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request()->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="province" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="2">
                        <option value="all">استان (همه)</option>
                        @foreach(\App\Models\Province::all('name') as $province)
                            <option value="{{ $province->name }}" {{ request()->province == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="status" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                        <option value="all">وضعیت (همه)</option>
                        <option value="invoiced" {{ request()->status == 'invoiced' ? 'selected' : '' }}>فاکتور شده</option>
                        <option value="paid" {{ request()->status == 'paid' ? 'selected' : '' }}>تسویه شده</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <input type="text" form="search_form" name="need_no" class="form-control" value="{{ request()->need_no ?? null }}" placeholder="شماره نیاز">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>خریدار</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('invoices-edit')
                            <th>فاکتور</th>
                            <th>پیش فاکتور</th>
                            <th>ویرایش</th>
                        @endcan
                        @can('invoices-edit')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($factors as $key => $factor)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $factor->invoice->customer->name }}</td>
                            <td>{{ $factor->invoice->province }}</td>
                            <td>{{ $factor->invoice->city }}</td>
                            <td>{{ $factor->invoice->phone }}</td>
                            <td>
                                @if($factor->status == 'paid')
                                    <span class="badge badge-success">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($factor->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'factor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-floating {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'pishfactor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('factors.edit', $factor->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('invoices-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('factors.destroy',$factor->id) }}" data-id="{{ $factor->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $factors->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


