@extends('panel.layouts.master')
@section('title', 'سفارشات')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>سفارشات</h6>
                <div>
                    <form action="{{ route('invoices.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('invoices-create')
                        @cannot('accountant')
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus mr-2"></i>
                                ایجاد سفارش
                            </a>
                        @endcannot
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
                        <th>درخواست جهت</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>وضعیت</th>
                        @can('accountant')
                            <th>همکار</th>
                        @endcan
                        <th>تاریخ ایجاد</th>
                        @can('accountant')
                            <th>مشاهده سفارش</th>
                        @endcan
                        @cannot('accountant')
                            @can('invoices-edit')
                                <th>ویرایش</th>
                            @endcan
                            @can('invoices-delete')
                                <th>حذف</th>
                            @endcan
                        @endcannot
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $key => $invoice)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ \App\Models\Invoice::TYPE[$invoice->type] }}</td>
                            <td>{{ \App\Models\Invoice::REQ_FOR[$invoice->req_for] }}</td>
                            <td>{{ $invoice->province }}</td>
                            <td>{{ $invoice->city }}</td>
                            <td>{{ $invoice->phone }}</td>
                            <td>
                                @can('accountant')
                                    @if($invoice->status == 'paid')
                                        <a href="{{ route('invoices.changeStatus', $invoice->id) }}" class="btn btn-success {{ $invoice->created_in == 'website' ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</a>
                                    @else
                                        <a href="{{ route('invoices.changeStatus', $invoice->id) }}" class="btn btn-warning {{ $invoice->created_in == 'website' ? 'disabled' : '' }}" style="display: block ruby">{{ \App\Models\Invoice::STATUS[$invoice->status] }}</a>
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
                            @can('accountant')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('invoices.show', $invoice->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @cannot('accountant')
                                @can('invoices-edit')
                                    <td>
                                        <a class="btn btn-warning btn-floating {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }} @canany(['admin','warehouse','accountant','ceo']) @else {{ $invoice->status == 'pending' ? 'disabled' : '' }} @endcanany" href="{{ route('invoices.edit', $invoice->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('invoices-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('invoices.destroy',$invoice->id) }}" data-id="{{ $invoice->id }}" {{ $invoice->created_in == 'website' || $invoice->factor ? 'disabled' : '' }} @canany(['admin','warehouse','accountant','ceo']) @else {{ $invoice->status == 'pending' ? 'disabled' : '' }} @endcanany>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            @endcannot
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


