@extends('panel.layouts.master')

@section('title', 'لیست حمل و نقل‌ها')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست حمل و نقل‌ها</h6>
                @can('transport-create')
                    <a href="{{ route('transports.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        افزودن حمل و نقل
                    </a>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شماره سفارش</th>
                        <th>حمل و نقل‌کننده‌ها</th>
                        <th>قیمت کل</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        @can('transport-edit')
                            <th>ویرایش/آپلود بیجک</th>
                        @endcan
                        @can('accountant-transport')
                            <th>اقدام</th>  <!-- دکمه اقدام به حسابدار -->
                        @endcan
                        @can('transport-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transports as $key => $transport)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $transport->invoice->id }}</td>

                            <!-- نمایش حمل‌ونقل‌کنندگان و جزئیات آنها -->
                            <td>
                                @foreach($transport->items as $item)
                                    <div>
                                        <strong>{{ $item->transporter->name }}</strong>
                                        - مبلغ: {{ number_format($item->price) . ' ریال ' }}
                                        - نوع پرداخت:
                                        @if($item->payment_type == 'prepaid')
                                            <span class="badge badge-info">پیش پرداخت</span>
                                        @elseif($item->payment_type == 'paid')
                                            <span class="badge badge-primary">پس پرداخت</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>

                            <td>{{ number_format($transport->items->sum('price')) . ' ریال ' }}</td>
                            <td>
                                @if($transport->status == 'level1')
                                    <span class="badge badge-warning">منتظر انتخاب حسابدار</span>
                                @elseif($transport->status == 'level2')
                                    <span class="badge badge-warning">منتظر پرداخت</span>
                                @elseif($transport->status == 'level3')
                                    <span class="badge badge-warning">منتظر تایید نهایی</span>
                                @elseif($transport->status == 'level4')
                                    <span class="badge badge-success">تکمیل شده</span>
                                @else
                                    <span class="badge badge-info">نامشخص</span>
                                @endif
                            </td>
                            <td>{{ verta($transport->created_at)->format('H:i - Y/m/d') }}</td>

                            @can('transport-edit')
                                @if($transport->status == 'level1')
                                <td>
                                    <a class="btn btn-primary btn-floating" href="{{ route('transports.edit', $transport->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                    @elseif($transport->status == 'level2')
                                    <td>
                                        <a class="btn btn-primary btn-floating" href="{{ route('transports.bijak', $transport->id) }}">
                                            <i class="fa-solid fa-upload"></i>
                                        </a>
                                    </td>
                                    @else
                                    <td>
                                        <a class="btn btn-primary btn-floating disabled" href="#" disabled>
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    @endif
                            @endcan

                            @can('accountant-transport')
                                @if($transport->status == 'level1')
                                    <td>
                                        <a href="{{ route('transports.accounting', $transport->id) }}" class="btn btn-info btn btn-floating">
                                            <i class="fa fa-calculator"></i>
                                        </a>
                                    </td>
                                @elseif($transport->status == 'level3')
                                    <td>
                                        <a href="{{ route('transports.finalaccounting', $transport->id) }}" class="btn btn-info btn btn-floating">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                @elseif($transport->status == 'level4')
                                    <td><span class="badge badge-success">تکمیل شده</span></td>
                                    @else
                                    <td><span class="badge badge-warning">منتظر انباردار</span></td>  <!-- اگر وضعیت 'level1' نباشد، دکمه نمایش داده نمی‌شود -->
                                @endif
                            @endcan

                            @can('transport-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('transports.destroy', $transport->id) }}" data-id="{{ $transport->id }}">
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

            <div class="d-flex justify-content-center">{{ $transports->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
