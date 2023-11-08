@extends('panel.layouts.master')
@section('title', 'ویرایش بسته ارسالی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش بسته ارسالی</h6>
            </div>
            <form action="{{ route('packets.update', $packet->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice">پیش فاکتور<span class="text-danger">*</span></label>
                        <select class="form-control" name="invoice" id="invoice">
                            @if($invoices->count())
                                <option value="{{ $packet->invoice_id }}" selected> {{ $packet->invoice_id }} - {{ $packet->invoice->customer->name }}</option>
                                @foreach($invoices as $invoiceId => $customerName)
                                    <option value="{{ $invoiceId }}" {{ $packet->invoice_id == $invoiceId ? 'selected' : '' }}> {{ $invoiceId }} - {{ $customerName }}</option>
                                @endforeach
                            @else
                                <option value="{{ $packet->invoice_id }}" selected> {{ $packet->invoice_id }} - {{ $packet->invoice->customer->name }}</option>
                            @endif
                        </select>
                        @error('invoice')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver">گیرنده <span class="text-danger">*</span></label>
                        <input type="text" name="receiver" class="form-control" id="receiver" value="{{ $packet->receiver }}">
                        @error('receiver')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="address">آدرس <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" id="address" value="{{ $packet->address }}">
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sent_time">زمان ارسال <span class="text-danger">*</span></label>
                        <input type="text" name="sent_time" class="form-control date-picker-shamsi-list" id="sent_time" value="{{ verta($packet->sent_time)->format('Y/m/d') }}">
                        @error('sent_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="sent_type">نوع ارسال <span class="text-danger">*</span></label>
                        <select class="form-control" name="sent_type" id="sent_type">
                            @foreach(\App\Models\Packet::SENT_TYPE as $key => $value)
                                <option value="{{ $key }}" {{ $packet->sent_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('sent_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="send_tracking_code">کد رهگیری ارسالی <span class="text-danger">*</span></label>
                        <input type="text" name="send_tracking_code" class="form-control" id="send_tracking_code" value="{{ $packet->send_tracking_code }}">
                        @error('send_tracking_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receive_tracking_code">کد رهگیری دریافتی </label>
                        <input type="text" name="receive_tracking_code" class="form-control" id="receive_tracking_code" value="{{ $packet->receive_tracking_code }}">
                        @error('receive_tracking_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="packet_status">وضعیت بسته <span class="text-danger">*</span></label>
                        <select class="form-control" name="packet_status" id="packet_status">
                            @foreach(\App\Models\Packet::PACKET_STATUS as $key => $value)
                                <option value="{{ $key }}" {{ $packet->packet_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('packet_status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="invoice_status">وضعیت فاکتور <span class="text-danger">*</span></label>
                        <select class="form-control" name="invoice_status" id="invoice_status">
                            @foreach(\App\Models\Packet::INVOICE_STATUS as $key => $value)
                                <option value="{{ $key }}" {{ $packet->invoice_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('invoice_status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control">{{ $packet->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

