@extends('panel.layouts.master')
@section('title', 'روزهای تحویل سفارش')
@section('styles')
    <style>
        .box{
            width: 200px !important;
            justify-content: center !important;
        }

        .is-holiday{
            color: red !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>انتخاب روز های تحویل سفارش</h6>
                <a href="https://time.ir" class="btn btn-link" target="_blank">
                    <i class="fa fa-arrow-up-right-from-square mr-2"></i>
                    تقویم
                </a>
            </div>
            <div class="d-flex justify-content-center">
                <div class="btn-group-lg btn-group-toggle text-center" data-toggle="buttons">
                    @foreach($days as $day)
                        @php
                            $disabled = \Hekmatinasser\Verta\Verta::parse($day['date']) < verta();
                        @endphp
                        <label class="btn box py-4 mt-2 {{ $disabled ? 'disabled' : '' }} {{ $day['is_selected'] ? ($disabled ? 'btn-primary' : 'btn-outline-primary active') : 'btn-outline-primary' }}">
                            <input type="checkbox" name="days[]" value="{{ json_encode($day) }}" {{ $day['is_selected'] ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                            <div class="{{ $day['is_holiday'] ? 'is-holiday' :  '' }}">
                                <span class="d-block">{{ $day['text'] }}</span>
                                <small>{{ $day['date'] }}</small>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="d-flex justify-content-center mt-5">
                <a href="{{ route('delivery-days.index') }}" class="btn btn-outline-success mr-3 {{ request()->week ? '' : 'disabled' }}">هفته جاری</a>
                <a href="{{ route('delivery-days.index', ['week' => 'next']) }}" class="btn btn-outline-success {{ request()->week ? 'disabled' : '' }}">هفته بعدی</a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('change','input[name="days[]"]', function () {
                let day = this.value;
                $.ajax({
                    url: 'https://app.mpsystem.ir/api/v1/select-day',
                    type: 'post',
                    data: {
                        day
                    },
                    success: function(res){
                    }
                })
            })
        })
    </script>
@endsection

