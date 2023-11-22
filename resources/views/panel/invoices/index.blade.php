@extends('panel.layouts.master')
@section('title', 'پیش فاکتور ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پیش فاکتور ها</h6>
                <div>
                    <form action="{{ route('invoices.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel-o mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('invoices-create')
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد پیش فاکتور
                        </a>
                    @endcan
                </div>

            </div>
            <form action="{{ route('invoices.search') }}" method="post" id="search_form">
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
                        <option value="return" {{ request()->status == 'return' ? 'selected' : '' }}>عودت داده شده</option>
                        <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>در دست اقدام</option>
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
                        <th>پیش فاکتور</th>
                        @can('invoices-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('invoices-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $key => $invoice)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->province }}</td>
                            <td>{{ $invoice->city }}</td>
                            <td>{{ $invoice->phone }}</td>
                            <td>
                                @if($invoice->status == 'paid')
                                    <span class="badge badge-success">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($invoice->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="text-primary" href="{{ route('invoices.show', [$invoice->id, 'type' => 'pishfactor']) }}">
                                    <u><strong>{{ $invoice->id }}</strong></u>
                                </a>
                            </td>
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('invoices.edit', $invoice->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('invoices-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('invoices.destroy',$invoice->id) }}" data-id="{{ $invoice->id }}">
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
            <div class="d-flex justify-content-center">{{ $invoices->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


