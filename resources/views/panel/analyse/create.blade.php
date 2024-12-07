@extends('panel.layouts.master')
@section('title', 'ایجاد آنالیز')
    @section('content')
        <div class="card-title d-flex justify-content-between align-items-center">
            <h6>مرحله اول انتخاب تاریخ</h6>
        </div>
        <form method="POST" action="{{ route('analyse.step1.post') }}">
            @csrf
            <div class="form-group">
                <label for="date">تاریخ</label>
                <input type="text" id="date" name="date" class="form-control date-picker-shamsi-list" required>
            </div>
            <button type="submit" class="btn btn-primary">مرحله بعد</button>
        </form>
    @endsection
