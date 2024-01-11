@extends('panel.layouts.master')
@can('accountant')
    @section('title', 'پیش فاکتور ها')
@else
    @section('title', 'سفارشات')
@endcan
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <strong>نکته!</strong> امکان <u>ویرایش</u> و <u>حذف</u>
                @can('accountant')
                    پیش فاکتور
                @else
                    سفارش
                @endcan
                هایی که وضعیت آنها فاکتور شده است وجود ندارد.
            </div>
            <div class="card-title d-flex justify-content-between align-items-center">
                @can('accountant')
                    <h6>پیش فاکتور ها</h6>
                @else
                    <h6>سفارشات</h6>
                @endcan
                <div>
                    <form action="{{ route('invoices.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('invoices-create')
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد
                            @can('accountant')
                                پیش فاکتور
                            @else
                                سفارش
                            @endcan
                        </a>
                    @endcan
                </div>

            </div>
            <form action="{{ route('invoices.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3 mt-5">
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
                        @foreach(\App\Models\Invoice::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
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
                <table class="table table-striped table-bordered dataTable dtr-inline text-center dataTable">
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
                        @can('accountant')
                            <th>پیش فاکتور</th>
                        @else
                            <th>سفارش</th>
                        @endcan
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
                            <td>{{ \App\Models\Invoice::TYPE[$invoice->type] }}</td>
                            <td>{{ $invoice->province }}</td>
                            <td>{{ $invoice->city }}</td>
                            <td>{{ $invoice->phone }}</td>
                            <td>
                                @can('accountant')
                                    @if($invoice->status == 'paid')
                                        <a href="{{ route('invoices.changeStatus', $invoice->id) }}" class="btn btn-success {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</a>
                                    @else
                                        <a href="{{ route('invoices.changeStatus', $invoice->id) }}" class="btn btn-warning {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</a>
                                    @endif
                                @else
                                    @if($invoice->status == 'paid')
                                        <span class="badge badge-success" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</span>
                                    @else
                                        <span class="badge badge-warning" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</span>
                                    @endif
                                @endcan
                            </td>
                            @can('accountant')
                                <td>{{ $invoice->user->fullName() }}</td>
                            @endcan
                            <td>{{ verta($invoice->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="text-primary" href="{{ route('invoices.show', [$invoice->id, 'type' => 'pishfactor']) }}">
                                    <u><strong>{{ $invoice->id }}</strong></u>
                                </a>
                            </td>
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }}" href="{{ route('invoices.edit', $invoice->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('invoices-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('invoices.destroy',$invoice->id) }}" data-id="{{ $invoice->id }}" {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }}>
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


