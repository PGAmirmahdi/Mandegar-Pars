@extends('panel.layouts.master')
@section('title', 'پیامک‌ها')
<?php
$successMessages = [
    1 => 'پیام با موفقیت ارسال شد',
    200 => 'ارسال شده'
];

$errorMessages = [
    null => 'غیر فعال بودن دسترسی گزارش تحویل برای کاربر',
    -1 => 'ارسال نشده',
    -3 => 'نام کاربری یا رمز عبور اشتباه است',
    0 => 'ارسال شده به مخابرات',
    2 => 'نرسیده به گوشی',
    3 => 'خطای مخابراتی',
    5 => 'خطای نامشخص',
    8 => 'رسیده به مخابرات',
    16 => 'نرسیده به مخابرات',
    35 => 'لیست سیاه',
    100 => 'نامشخص',
    300 => 'فیلتر شده',
    400 => 'در لیست ارسال',
    500 => 'عدم پذیرش',
];
?>
@section('content')
    @can('sms-list')
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>پیامک‌ها</h6>
                    <div>
                        @can('sms-create')
                            <a href="{{ route('sms.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus mr-2"></i>
                                ایجاد پیامک
                            </a>
                        @endcan
                    </div>
                </div>
                <form action="{{ route('sms.search') }}" method="get" id="search_form"></form>
                <div class="row mb-3">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <input type="text" name="user_name" class="form-control" placeholder="نام ارسال کننده" value="{{ request()->user_name ?? '' }}" form="search_form">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <input type="text" name="receiver_name" class="form-control" placeholder="نام گیرنده" value="{{ request()->receiver_name ?? '' }}" form="search_form">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                        <input type="text" name="receiver_phone" class="form-control" placeholder="شماره گیرنده" value="{{ request()->receiver_phone ?? '' }}" form="search_form">
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
                            <th>نام ارسال کننده</th>
                            <th>نام گیرنده</th>
                            <th>شماره گیرنده</th>
                            <th>پیام</th>
                            <th>وضعیت</th>
                            <th>تاریخ ایجاد</th>
                            @can('sms-show')
                                <th>مشاهده</th>
                            @endcan
                            @can('sms-delete')
                                <th>حذف</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($smsList as $key => $sms)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    {{ $sms->user ? $sms->user->name . ' ' . $sms->user->family : 'نامشخص' }}
                                </td>
                                <td>{{ $sms->receiver_name }}</td>
                                <td>{{ $sms->receiver_phone }}</td>
                                <td>{{ Str::limit($sms->message, 60) }}</td>
                                <td>
                                    @if(array_key_exists($sms->status, $successMessages))
                                        {{ $successMessages[$sms->status] }}
                                    @elseif(array_key_exists($sms->status, $errorMessages))
                                        {{ $errorMessages[$sms->status] }}
                                    @else
                                        وضعیت نامشخص
                                    @endif
                                </td>
                                <td>{{ verta($sms->created_at)->format('H:i - Y/m/d') }}</td>
                                @can('sms-show')
                                    <td>
                                        <a class="btn btn-info btn-floating" href="{{ route('sms.show', $sms->id) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('sms-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('sms.destroy',$sms->id) }}" data-id="{{ $sms->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="d-flex justify-content-center">{{ $smsList->appends(request()->all())->links() }}</div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endcan
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
@endsection
