@extends('panel.layouts.master')
@section('title', 'گزارش ورود/خروج کالا')
@section('content')
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">گزارش ورود/خروج برای کالا: {{ $inventory->product->title }}</h6>
            <div class="table-responsive overflow-auto">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نوع</th>
                        <th>تعداد</th>
                        <th>تاریخ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $key => $report)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $report->type == 'input' ? 'ورود' : 'خروج' }}</td>
                            <td>{{ number_format($report->count) }}</td>
                            <td>{{ verta($report->created_at)->format('H:i - Y/m/d') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <a href="{{ route('inventory.index', ['warehouse_id' => $warehouse_id]) }}" class="btn btn-secondary">بازگشت به انبار</a>
        </div>
    </div>
@endsection
