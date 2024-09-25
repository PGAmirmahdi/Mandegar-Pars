@extends('panel.layouts.master')
@section('title', 'فاکتور ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>فاکتور ها</h6>
                <div>
                    <form action="{{ route('factors.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>
                </div>
            </div>
            <form action="{{ route('factors.search') }}" method="get" id="search_form"></form>
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
                        <option value="canceled" {{ request()->status == 'canceled' ? 'selected' : '' }}>ابطال شده</option>
                    </select>
                </div>
                @can('accountant')
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="user" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="4">
                            <option value="all">همکار (همه)</option>
                            @foreach(\App\Models\User::whereIn('role_id', $roles_id)->get() as $user)
                                <option value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>{{ $user->fullName() }}</option>
                            @endforeach
                        </select>
                    </div>
                @endcan
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
                        <th>نوع</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>وضعیت</th>
                        @can('accountant')
                            <th>همکار</th>
                        @endcan
                        <th>تاریخ ایجاد</th>
                        <th>فاکتور</th>
                        <th>پیش فاکتور</th>
                        @can('invoices-edit')
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
                            <td>{{ \App\Models\Invoice::TYPE[$factor->invoice->type] }}</td>
                            <td>{{ $factor->invoice->province }}</td>
                            <td>{{ $factor->invoice->city }}</td>
                            <td>{{ $factor->invoice->phone }}</td>
                            <td>
                                @can('accountant')
                                    @if($factor->status == 'paid')
                                        <a href="{{ route('factors.changeStatus', $factor->id) }}" class="btn btn-success {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Factor::STATUS[$factor->status] }}</a>
                                    @elseif($factor->status == 'canceled')
                                        <a href="{{ route('factors.changeStatus', $factor->id) }}" class="btn btn-danger disabled" style="display: block ruby">{{ \App\Models\Factor::STATUS[$factor->status] }}</a>
                                    @else
                                        <a href="{{ route('factors.changeStatus', $factor->id) }}" class="btn btn-warning {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Factor::STATUS[$factor->status] }}</a>
                                    @endif
                                @else
                                    @if($factor->status == 'paid')
                                        <span class="badge badge-success">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                    @endif
                                @endcan
                            </td>
                            @can('accountant')
                                <td>{{ $factor->invoice->user->fullName() }}</td>
                            @endcan
                            <td>{{ verta($factor->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="text-primary" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'factor']) }}">
                                    <u><strong>{{ $factor->id }}</strong></u>
                                </a>
                            </td>
                            <td>
                                @if($factor->invoice->created_in != 'website')
                                    <a class="text-primary" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'pishfactor']) }}">
                                        <u><strong>{{ $factor->invoice->id }}</strong></u>
                                    </a>
                                @else
                                    <u><strong>{{ $factor->invoice->id }}</strong></u>
                                @endif
                            </td>
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('factors.edit', $factor->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('invoices-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('factors.destroy',$factor->id) }}" data-id="{{ $factor->id }}" {{ $factor->invoice->created_in == 'website' || $factor->inventory_report != null ? 'disabled' : '' }}>
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


