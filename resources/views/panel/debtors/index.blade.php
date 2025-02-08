@php use Carbon\Carbon; @endphp
@extends('panel.layouts.master')

@section('title', 'لیست بدهکاران')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> برخی از بدهکاران به صورت خودکار توسط سیستم
                پس از ایجاد سفارش فروش ایجاد میشوند و مقادیر(تاریخ موعد پرداخت،تاریخ خرید،شماره فاکتورآن باید به صورت
                دستی به روز شود)
            </div>
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست بدهکاران</h6>
                @can('debtor-create')
                    <a href="{{ route('debtors.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        افزودن بدهکار
                    </a>
                @endcan
            </div>
            <form method="GET" action="{{ route('debtors.search') }}" class="mb-4">
                <div class="row">
                    <!-- جستجو بر اساس کد مشتری -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <label for="customer_id">مشتری</label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="customer_id" id="customer_id" data-select2-id="1">
                            <option value="all">انتخاب مشتری (همه)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->code . ' - ' .  $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- جستجو بر اساس وضعیت بدهی -->
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <label for="status">وضعیت پرداخت</label>
                        <select name="status" id="status"
                                class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                            <option value="all">وضعیت (همه)</option>
                            @foreach(App\Models\Debtor::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- دکمه جستجو -->
                    <div class="col-md-3 align-self-end mt-2">
                        <button type="submit" class="btn btn-primary">جستجو</button>
                    </div>
                </div>
            </form>
            <div class="overflow-auto">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>تاریخ خرید</th>
                        <th>کد مشتری</th>
                        <th>نام مشتری</th>
                        <th>شماره فاکتور</th>
                        <th>شماره تلفن مشتری</th>
                        <th>میزان طلب</th>
                        <th>تاریخ موعد پرداخت</th>
                        <th>وضعیت</th>
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
                        @php
                            $paymentDue = verta($debtor->payment_due); // تاریخ هجری شمسی
                            $paymentDueGregorian = $paymentDue->toCarbon()->format('Y-m-d'); // تبدیل به میلادی با استفاده از Carbon
                            $today = verta(now())->format('Y-m-d');
                            // محاسبه تفاوت تاریخ‌ها به روز
                            $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($paymentDueGregorian), false);
                        @endphp
                        <tr class="@if($daysLeft <= 0 && !in_array($debtor->status, ['paid', 'partial'])) table-danger @elseif($daysLeft > 0 && $daysLeft <= 2 && !in_array($debtor->status, ['paid', 'partial'])) table-warning @elseif($daysLeft > 2) @elseif($debtor->status == 'paid') table-success @endif">
                            <td>{{ ++$key }}</td>
                            <td>{{$debtor->buy_date ?? '---'}}</td>
                            <td>{{ $debtor->customer->code }}</td>
                            <td>{{ $debtor->customer->name }}</td>
                            <td>{{ $debtor->factor_number }}</td>
                            <td>{{$debtor->customer->phone1}}</td>
                            <td>{{ number_format($debtor->price) . ' ریال ' }}</td>
                            <td>{{ $debtor->payment_due ?? '---'}}</td>
                            <td>
                                @if($debtor->status == 'unpaid')
                                    <span class="badge badge-warning">پرداخت نشده</span>
                                @elseif($debtor->status == 'paid')
                                    <span class="badge badge-success">پرداخت شده</span>
                                @elseif($debtor->status == 'partial')
                                    <span class="badge badge-warning">پرداخت ناقص</span>
                                @elseif($debtor->status == 'followed')
                                    <span class="badge badge-primary">پیگیری شود</span>
                                @else
                                    <span class="badge badge-info">نامشخص</span>
                                @endif
                            </td>
                            @can('debtor-show')
                                <td>
                                    <a class="btn btn-primary btn-floating"
                                       href="{{ route('debtors.show', $debtor->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('debtor-edit')
                                <td>
                                    @if($debtor->status == 'paid')
                                        <a class="btn btn-primary btn-floating disabled" disabled href="#">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-primary btn-floating"
                                           href="{{ route('debtors.edit', $debtor->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            @endcan
                            @can('debtor-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('debtors.destroy', $debtor->id) }}"
                                            data-id="{{ $debtor->id }}">
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
