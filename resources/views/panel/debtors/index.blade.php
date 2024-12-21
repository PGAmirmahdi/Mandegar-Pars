@extends('panel.layouts.master')

@section('title', 'لیست بدهکاران')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> برخی از بدهکاران به صورت خودکار توسط سیستم پس از ایجاد سفارش فروش ایجاد میشوند </div>
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست بدهکاران</h6>
                @can('debtor-create')
                    <a href="{{ route('debtors.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        افزودن بدهکار
                    </a>
                @endcan
            </div>
            <form method="GET" action="{{ route('debtors.index') }}" class="mb-4">
                <div class="row">
                    <!-- جستجو بر اساس کد مشتری -->
                    <div class="col-md-3">
                        <label for="customer_code">کد مشتری</label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="customer_code" id="customer_code"  data-select2-id="1">
                            <option value="">انتخاب کد مشتری</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->code }}" {{ request('customer_code') == $customer->code ? 'selected' : '' }}>
                                    {{ $customer->code . ' - ' .  $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- جستجو بر اساس نام مشتری -->
                    <div class="col-md-3">
                        <label for="customer_name">نام مشتری</label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="customer_name" id="customer_name"  data-select2-id="2">
                            <option value="">انتخاب نام مشتری</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_name') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- جستجو بر اساس وضعیت بدهی -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <label for="status">وضعیت پرداخت</label>
                        <select name="status" id="status" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                            <option value="all">وضعیت (همه)</option>
                            @foreach(App\Models\Debtor::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- دکمه جستجو -->
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> جستجو
                        </button>
                        <a href="{{ route('debtors.index') }}" class="btn btn-secondary">
                             نمایش همه
                        </a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کد مشتری</th>
                        <th>نام مشتری</th>
                        <th>مبلغ بدهکاری</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        @can('debtor-show')
                            <th>جزئیات</th>  <!-- دکمه اقدام به حسابدار -->
                        @endcan
                        @can('debtor-edit')
                            <th>بارگذاری رسید/ویرایش</th>
                        @endcan
                        @can('debtor-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($debtors as $key => $debtor)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $debtor->customer->code }}</td>
                            <td>{{ $debtor->customer->name }}</td>
                            <td>{{ number_format($debtor->price) . ' ریال ' }}</td>
                            <td>
                                @if($debtor->status == 'unpaid')
                                    <span class="badge badge-warning">پرداخت نشده</span>
                                @elseif($debtor->status == 'paid')
                                    <span class="badge badge-success">پرداخت شده</span>
                                @elseif($debtor->status == 'partial')
                                    <span class="badge badge-warning">پرداخت ناقص</span>
                                @else
                                    <span class="badge badge-info">نامشخص</span>
                                @endif
                            </td>
                            <td>{{ verta($debtor->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('debtor-show')
                                    <td>
                                        <a class="btn btn-primary btn-floating" href="{{ route('debtors.show', $debtor->id) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                            @endcan
                            @can('debtor-edit')
                                    <td>
                                        <a class="btn btn-primary btn-floating" href="{{ route('debtors.edit', $debtor->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                            @endcan
                            @can('debtor-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('debtors.destroy', $debtor->id) }}" data-id="{{ $debtor->id }}">
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
            <div class="d-flex justify-content-center">{{ $debtors->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
