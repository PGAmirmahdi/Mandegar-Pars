@extends('panel.layouts.master')

@section("title",'انتخاب تاریخ')

@section('content')
    <div class="card">
        <div class="card-body">
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
            <hr>
            <form method="GET" action="{{ route('analyse.step1') }}">
                @csrf
                <div class="form-row">
                    <div class="col-md-5">
                        <label for="start_date">از تاریخ</label>
                        <input type="text" id="start_date" name="start_date" class="form-control date-picker-shamsi-list" value="{{ request()->start_date }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date">تا تاریخ</label>
                        <input type="text" id="end_date" name="end_date" class="form-control date-picker-shamsi-list" value="{{ request()->end_date }}">
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-block mt-2">فیلتر</button>
                    </div>
                </div>
            </form>
            <h3 class="mt-3">آنالیزهای ثبت شده</h3>
            @if($analyses->isEmpty())
                <p>هیچ آنالیزی ثبت نشده است.</p>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>برند</th>
                        <th>تاریخ آنالیز</th>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($analyses as $index => $analyse)
                        @foreach($analyse->analyseProducts as $product) <!-- اضافه کردن این خط برای جدا کردن محصولات -->
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $analyse->brand->slug ?? 'نامشخص' }}</td>
                            <td>{{ $analyse->date }}</td>
                            <td>{{ $product->product->title ?? 'نامشخص' }}</td> <!-- نام محصول -->
                            <td>{{ $product->quantity }}</td> <!-- تعداد محصول -->
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
