@extends('panel.layouts.master')
@section('title', 'مشتریان')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشتریان</h6>
                <div>
                    <form action="{{ route('customers.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('customers-create')
                        <a href="{{ route('customers.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد مشتری
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('customers.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="code" form="search_form" class="form-control" placeholder="کد مشتری" value="{{ request()->code ?? null }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="name" form="search_form" class="form-control" placeholder="نام مشتری" value="{{ request()->name ?? null }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="province" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">استان (همه)</option>
                        @foreach(\App\Models\Province::all() as $province)
                            <option value="{{ $province->name }}" {{ request()->province == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="customer_type" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="2">
                        <option value="all">مشتری (همه)</option>
                        @foreach(\App\Models\Customer::CUSTOMER_TYPE as $key => $value)
                            <option value="{{ $key }}" {{ request()->customer_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="type" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                        <option value="all">نوع (همه)</option>
                        @foreach(\App\Models\Customer::TYPE as $key => $value)
                            <option value="{{ $key }}" {{ request()->type == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
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
                        <th>نام حقیقی/حقوقی</th>
                        <th>کد مشتری</th>
                        <th>نوع</th>
                        <th>مشتری</th>
                        <th>استان</th>
                        <th>شماره تماس 1</th>
                        <th>تعداد سفارش</th>
                        <th>تاریخ ایجاد</th>
                        @can('customers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('customers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->code ?? '---' }}</td>
                            <td>{{ \App\Models\Customer::TYPE[$customer->type] }}</td>
                            <td>{{ \App\Models\Customer::CUSTOMER_TYPE[$customer->customer_type] }}</td>
                            <td>{{ $customer->province }}</td>
                            <td>{{ $customer->phone1 }}</td>
                            <td>{{ $customer->invoices()->count() }}</td>
                            <td>{{ verta($customer->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('customers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('customers.edit', ['customer' => $customer->id, 'url' => request()->getRequestUri()]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('customers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('customers.destroy',$customer->id) }}" data-id="{{ $customer->id }}">
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
            <div class="d-flex justify-content-center">{{ $customers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


