@extends('panel.layouts.master')
@section('title', 'ارسال پیام به گروه')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ارسال پیام به گروه</h6>
            </div>
            <form id="group-message-form" action="{{ route('whatsapp.sendToGroup') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                        <label for="group_link">لینک گروه <span class="text-danger">*</span></label>
                        <input type="text" name="group_id" class="form-control" id="group_id"
                               placeholder="https://whatsapp.com/..."
                               value="{{ old('group_id') }}">
                        @error('group_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                        <label for="description">متن پیام <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" id="description" rows="5">{{ old('description', $defaultMessage) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ارسال پیام</button>
            </form>
        </div>
    </div>
@endsection
