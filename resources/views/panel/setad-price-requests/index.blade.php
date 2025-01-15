@extends('panel.layouts.master')
@section('title', 'لیست درخواست ستاد')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست درخواست ستاد</h6>
                @can('setad-price-requests-create')
                    <a href="{{ route('setad_price_requests.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت درخواست ستاد
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>کد</th>
                        <th>ثبت کننده</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        <th>مهلت تایید</th>
                        <th>مهلت باقی مانده</th>
                        <th>تایید/رد کننده</th>
                        @canany(['ceo','admin'])
                            <th>ثبت قیمت</th>
                        @else
                            <th>مشاهده قیمت</th>
                        @endcanany
                        @can('setad-price-requests-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($setadprice_requests as $key => $setadprice_request)
                        @php
                            $paymentDue = verta($setadprice_request->date); // تاریخ هجری شمسی
                            $paymentDueGregorian = $paymentDue->toCarbon()->format('Y-m-d');
                            $today = verta(now())->format('Y-m-d');
                            // محاسبه تفاوت تاریخ‌ها به روز
                            $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($paymentDueGregorian), false);
                        @endphp
                        <tr class="@if($daysLeft <= 0 && !in_array($setadprice_request->status, ['accepted'])) table-danger @elseif($daysLeft > 0 && $daysLeft <= 2 && !in_array($setadprice_request->status, ['accepted'])) table-warning @elseif($daysLeft > 2) @elseif($setadprice_request->status == 'accepted') table-success @endif">
                            <td>{{$setadprice_request->code}}</td>
                            <td>{{ ++$key }}</td>
                            <td>{{ $setadprice_request->user->name }}</td>
                            <td>
                                @if($setadprice_request->status == 'accepted')
                                    <span
                                        class="badge badge-success">{{ \App\Models\SetadPriceRequest::STATUS['accepted'] }}</span>
                                @elseif($setadprice_request->status == 'rejected')
                                    <span
                                        class="badge badge-success">{{ \App\Models\SetadPriceRequest::STATUS['rejected'] }}</span>
                                @else
                                    <span
                                        class="badge badge-warning">{{ \App\Models\SetadPriceRequest::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($setadprice_request->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                {{$setadprice_request->date . ' ' . $setadprice_request->hour}}
                            </td>
                            <td>
                                {{$daysLeft}} روز
                            </td>
                            <td>
                                @if(in_array($setadprice_request->status, ['accepted', 'rejected']))
                                    {{$setadprice_request->acceptor->fullName()}}
                                @elseif($setadprice_request->status == 'pending')
                                    منتظر تایید
                                @else
                                    نامشخص
                                @endif
                            </td>
                            @canany(['ceo','admin'])
                                <td>
                                    <a class="btn btn-primary btn-floating @if(in_array($setadprice_request->status, ['accepted', 'rejected'])) disabled @endif" @if(in_array($setadprice_request->status, ['accepted', 'rejected'])) disabled @endif
                                       href="{{ route('setad_price_requests.action', $setadprice_request->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a class="btn btn-info btn-floating"
                                       href="{{ route('setad_price_requests.show', $setadprice_request->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcanany
                            @can('setad-price-requests-delete')
                                <td>
                                    <button
                                        class="btn btn-danger btn-floating trashRow @if(auth()->id() != $setadprice_request->user->id) disabled @endif "
                                        data-url="{{ route('setad_price_requests.destroy',$setadprice_request->id) }}"
                                        data-id="{{ $setadprice_request->id }}"
                                        @if(auth()->id() != $setadprice_request->user->id) disabled @endif>
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div
                class="d-flex justify-content-center">{{ $setadprice_requests->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

