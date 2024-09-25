@extends('panel.layouts.master')
@section('title', 'تغییرات نرم افزار')

@section('styles')
    <style>
        #app_updates ul:not(.list-unstyled) li{
            list-style-type: disclosure-closed
        }
        #app_updates ul{
            line-height: 2rem;
        }
    </style>
@endsection
@section('content')
    @php
        $updates = \App\Models\SoftwareUpdate::latest()->get();
    @endphp
    @foreach($updates as $update)
        <div class="alert alert-success alert-with-border alert-dismissible fade show mb-4 pr-3" id="app_updates">
            <div>
                <i class="ti-announcement d-inline m-r-10"></i>
                <h5 class="alert-heading d-inline">بروزرسانی نرم افزار - تاریخ انتشار {{ verta($update->date)->format('Y/m/d') }} - نسخه {{ $update->version }}</h5>
            </div>
            <ul>
                @foreach(explode(',', $update->description) as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
@endsection
