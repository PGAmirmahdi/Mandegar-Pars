@extends('panel.layouts.master')
@section('title', 'پیش فاکتور ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پیش فاکتور ها</h6>
                @can('invoices-create')
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد پیش فاکتور
                    </a>
                @endcan
            </div>
            <div class="row mb-3">
                <div class="col">
                    <form action="{{ route('invoices.search') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                <select class="form-control" name="status">
                                    <option value="all">وضعیت (همه)</option>
                                    <option value="paid">تسویه شده</option>
                                    <option value="pending">در دست اقدام</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                <button class="btn btn-primary">جستجو</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>خریدار</th>
                        <th>شماره اقتصادی</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('invoices-edit')
                            <th>پیش فاکتور</th>
                            <th>فاکتور</th>
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
                            <td>{{ $invoice->economical_number }}</td>
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
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('invoices.show', [$invoice->id, 'type' => 'pishfactor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('invoices.show', [$invoice->id, 'type' => 'factor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('invoices.edit', $invoice->id) }}">
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


