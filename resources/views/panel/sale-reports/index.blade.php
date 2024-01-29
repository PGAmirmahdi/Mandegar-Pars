@extends('panel.layouts.master')
@section('title', 'گزارشات فروش')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>گزارشات فروش</h6>
                @can('sale-reports-create')
                    <a href="{{ route('sale-reports.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد گزارش فروش
                    </a>
                @endcan
            </div>
            <form action="{{ route('sale-reports.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="invoice_id" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">سفارش (همه)</option>
                        @foreach($invoices as $invoice_id => $customer_name)
                            <option value="{{ $invoice_id }}" {{ request()->invoice_id == $invoice_id ? 'selected' : '' }}>{{ $invoice_id.' - '.$customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="person_name" class="form-control" placeholder="نام شخص" value="{{ request()->person_name ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="organ_name" class="form-control" placeholder="نام سازمان" value="{{ request()->organ_name ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="national_code" class="form-control" placeholder="کد/شناسه ملی" value="{{ request()->national_code ?? null }}" form="search_form">
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
                        <th>نام شخص</th>
                        <th>نام سازمان</th>
                        <th>کد/شناسه ملی</th>
                        <th>شماره سفارش</th>
                        <th>نوع پرداخت</th>
                        <th>تاریخ ایجاد</th>
                        @can('sale-reports-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('sale-reports-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sale_reports as $key => $sale_report)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $sale_report->person_name }}</td>
                            <td>{{ $sale_report->organ_name ?? '---' }}</td>
                            <td>{{ $sale_report->national_code ?? '---' }}</td>
                            <td>
                                @if($sale_report->invoice_id)
                                    <strong><u><a href="{{ route('invoices.show', [$sale_report->invoice_id, 'type' => 'pishfactor']) }}" class="text-primary" target="_blank">{{ $sale_report->invoice_id }}</a></u></strong>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>{{ $sale_report->payment_type ?? '---' }}</td>
                            <td>{{ verta($sale_report->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('sale-reports-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('sale-reports.edit', $sale_report->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('sale-reports-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('sale-reports.destroy',$sale_report->id) }}" data-id="{{ $sale_report->id }}">
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
            <div class="d-flex justify-content-center">{{ $sale_reports->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


