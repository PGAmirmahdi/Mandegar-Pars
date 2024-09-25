@extends('panel.layouts.master')
@section('title', 'مشاهده پیام ارسال شده')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشاهده پیام ارسال شده</h6>
            </div>
            <div>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                        <h6>شماره موبایل: {{ $smsHistory->phone }} </h6>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                        <h6>فرستنده: {{ $smsHistory->user->fullName() }} </h6>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12">
                        وضعیت:
                        @if($smsHistory->status == 'sent')
                            <span class="badge badge-success">ارسال شده</span>
                        @else
                            <span class="badge badge-warning">ناموفق</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <span class="d-block"><strong>متن پیام:</strong></span>
                        <p>
                            {!! str_replace("\n",'<br>',$smsHistory->text) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


