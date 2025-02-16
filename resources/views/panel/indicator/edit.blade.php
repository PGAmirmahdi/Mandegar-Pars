@extends('panel.layouts.master')
@section('title', 'نامه نگاری')
@section('styles')
    <style>
        <
        style >

        @font-face {
            font-family: dana;
            font-style: normal;
            font-weight: 400;
            font-display: auto;
            src: url({{asset('/assets/fonts/farsi-fonts/sahel-300.eot')}}) format("embedded-opentype"),
            url({{asset('/assets/fonts/farsi-fonts/sahel-300.woff2')}}) format("woff2"),
            url({{asset('/assets/fonts/farsi-fonts/sahel-300.woff')}}) format("woff"),
            url({{asset('/assets/fonts/farsi-fonts/sahel-300.ttf')}}) format("truetype")
        }

        body {
            font-family: dana, Sans-Serif;
        }

    </style>
    <link rel="stylesheet" href="{{asset('/vendors/clockpicker/bootstrap-clockpicker.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('/vendors/datepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('/vendors/datepicker-jalali/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('/assets/css/summernote.rtl.css')}}" type="text/css">

@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">ویرایش نامه</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('indicator.store') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="title" class="form-label">عنوان <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title"
                                               value="{{ old('title',$indicator->title) }}">
                                        @error('title')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback text-danger d-block" id="error-title"></div>

                                    </div>
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="to_date" class="form-label">تاریخ</label>
                                        <input type="text" name="date" class="form-control date-picker-shamsi-list"
                                               id="date" value="{{ old('date',$indicator->date) }}">
                                        @error('date')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{--                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">--}}
                                    {{--                                        <label for="number" class="form-label">شماره نامه</label>--}}
                                    {{--                                        <input type="text" class="form-control" name="number" id="number"--}}
                                    {{--                                               value="{{ old('number',$indicator->number) }}">--}}
                                    {{--                                        @error('number')--}}
                                    {{--                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>--}}
                                    {{--                                        @enderror--}}
                                    {{--                                    </div>--}}
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="attachment" class="form-label">پیوست</label>
                                        <input type="text" class="form-control" name="attachment" id="attachment"
                                               value="{{ old('attachment',$indicator->attachment) }}">
                                        @error('attachment')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="attachment" class="form-label">سربرگ</label>
                                        <select name="header" class="form-control" id="header">
                                            <option value="info" {{$indicator->header=='info'?'selected':''}}>سربرگ
                                                فارسی پرسو
                                                تجارت
                                                (Info)
                                            </option>
                                            <option value="sale" {{$indicator->header=='sale'?'selected':''}}>سربرگ
                                                فارسی پرسو
                                                تجارت
                                                (Sale)
                                            </option>
                                            <option value="english" {{$indicator->header=='english'?'selected':''}}>
                                                سربرگ
                                                انگلیسی پرسو
                                                تجارت
                                                انگلیسی پرسو تجارت
                                            </option>
                                        </select>
                                        @error('header')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="attachment" class="form-label">ارسال به</label>
                                        <select name="receiver[]" class="form-control" id="receiver"
                                                data-toggle="select2"
                                                multiple>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}"
                                                        @if(in_array($user->id,$receivers)) selected @endif>{{$user->name.' '.$user->family}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2 col-xl-3 col-lg-3 col-md-3">
                                        <label for="attachment" class="form-label">خطاب به</label>
                                        <input type="text" class="form-control" name="to" id="to"
                                               value="{{ old('to',$indicator->to) }}">
                                        @error('to')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-2 col-xl-12 col-lg-12 col-md-12">
                                        <label for="code" class="form-label">متن نامه<span
                                                class="text-danger">*</span></label>
                                        <textarea type="text" class="summernote-basic" name="text"
                                                  id="text">{{ old('text',$indicator->text) }}</textarea>
                                        @error('text')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback text-danger d-block" id="error-text"></div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-warning mt-3">ویرایش نامه</button>
                                    <button type="button" id="preview-button" class="btn btn-primary mt-3">پیش نمایش</button>
                                </div>


                            </form>
                            <form action="{{route('indicator.preview')}}" id="preview-form" method="POST"
                                  target="_blank">
                                @csrf
                                <input type="hidden" name="date" id="date-preview">
                                <input type="hidden" name="attachment" id="attachment-preview">
                                <input type="hidden" name="text" id="text-preview">
                            </form>
                        </div>
                    </div>


                    {{--                    <button type="button" id="exportPdf" class="btn btn-danger mt-3"--}}
                    {{--                            onsubmit="e.preventDefault()" disabled>--}}
                    {{--                        خروجی PDF--}}
                    {{--                        <i class="fas fa-file-pdf"></i>--}}
                    {{--                    </button>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{asset('/vendors/datepicker-jalali/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js')}}"></script>
    <script src="{{asset('/vendors/datepicker/daterangepicker.js')}}"></script>
    <script src="{{asset('/assets/js/examples/datepicker.js')}}"></script>
    <script src="{{asset('/vendors/clockpicker/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{asset('/assets/js/examples/clockpicker.js')}}"></script>
    <script src="{{asset('/assets/js/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('/assets/js/ckeditor/adapters/jquery.js')}}"></script>
    <script src="{{asset('/assets/js/summernote.js')}}"></script>
    <script src="{{asset('/assets/js/editor.js')}}"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#preview-button').click(function () {
                $('#date-preview').val($('#date').val());
                $('#attachment-preview').val($('#attachment').val());
                $('#text-preview').val($('#text').val());
                $('#preview-form').submit();
            });


        });
    </script>
@endsection
