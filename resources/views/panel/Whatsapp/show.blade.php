@extends('panel.layouts.master')
@section('title', 'مشاهده پیام واتساپی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشاهده پیام واتساپی</h6>
                @if($status=='failed')<span class="badge badge-danger">ناموفق</span>@elseif($status == 'successful')<span class="badge badge-success">موفق</span>@else<span class="badge badge-secondary">{{ $status }}</span>@endif
            </div>
            <form id="sms-form">
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="user_id">نام ارسال کننده</label>
                        <input type="text" class="form-control" id="user_id" value="{{ $user->name . ' ' . $user->family }}" readonly>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_name">نام گیرنده</label>
                        <input type="text" class="form-control" id="receiver_name" value="{{ $receiver_name }}" readonly>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver_phone">شماره گیرنده</label>
                        <input type="text" class="form-control" id="receiver_phone" value="{{ $receiver_phone }}" readonly>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="message">متن پیام</label>
                        <textarea id="message" class="form-control" rows="10" readonly>
{{ $message }}
                        </textarea>
                    </div>
                    <div class="col-12 d-flex row justify-content-end"><a href="{{ route('whatsapp.index') }}" class="btn btn-danger" type="submit">بازگشت</a></div>
                </div>
            </form>
        </div>
    </div>
    <style>
        .modal-content {
            width: 300px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
            outline: 0;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    {{--Jquery--}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
