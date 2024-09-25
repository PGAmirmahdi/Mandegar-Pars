@extends('panel.layouts.master')
@section('title', 'درخواست مرخصی')
@section('content')
    <div class="card">
        <div class="card-body">
            @if(!auth()->user()->leavesCount())
                <div class="alert alert-warning">
                    <i class="fa fa-warning" style="font-size: large"></i>
                    <strong>توجه!</strong>
                    سقف مرخصی های روزانه شما در این ماه تمام شده است.
                </div>
            @endif
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>درخواست مرخصی</h6>
            </div>
            <form action="{{ route('leaves.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-4" id="users">
                        <label for="type">نوع<span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control">
                            @foreach(\App\Models\Leave::TYPE as $key => $value)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                        <label for="from_date">از تاریخ<span class="text-danger">*</span></label>
                        <input type="text" name="from_date" class="form-control date-picker-shamsi-list" id="from_date" value="{{ old('from_date') }}">
                        @error('from_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                        <label for="to_date">تا تاریخ<span class="text-danger">*</span></label>
                        <input type="text" name="to_date" class="form-control date-picker-shamsi-list" id="to_date" value="{{ old('to_date') }}">
                        @error('to_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4 clock-sec">
                        <label>از ساعت<span class="text-danger">*</span></label>
                        <div class="input-group clockpicker-autoclose-demo">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                            <input type="text" name="from" class="form-control text-left" value="{{ old('from') }}" dir="ltr" required>
                        </div>
                        @error('from')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 mb-4 clock-sec">
                        <label>تا ساعت<span class="text-danger">*</span></label>
                        <div class="input-group clockpicker-autoclose-demo">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                            </div>
                            <input type="text" name="to" class="form-control text-left" value="{{ old('to') }}" dir="ltr" required>
                        </div>
                        @error('to')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            clockSection($('select[name="type"]').val())

            // change type
            $(document).on('change', 'select[name="type"]', function () {
                clockSection(this.value)
            })
            // end change type

            // change from_date
            $(document).on('change', 'input[name="from_date"]', function () {
                // equals to_date and from_date if type is hourly
                if ($('select[name="type"]').val() == 'hourly'){
                    $('input[name="to_date"]').val(this.value)
                }
            })
            // end from_date
        })

        function clockSection(type) {
            if (type == 'hourly'){
                $('.clock-sec input').attr('required','required')
                // disable the to_date input and change it value equal to from_date if type is hourly
                $('input[name="to_date"]')
                    .attr('readonly','readonly')
                    .css('pointer-events','none')
                    .val($('input[name="from_date"]').val())
                $('.clock-sec').removeClass('d-none')
            }else{
                $('.clock-sec').addClass('d-none')
                $('input[name="to_date"]')
                    .css('pointer-events','auto')
                    .removeAttr('readonly')
                $('.clock-sec input').removeAttr('required')

            }
        }
    </script>
@endsection

