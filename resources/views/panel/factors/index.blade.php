@extends('panel.layouts.master')
@section('title', 'فاکتور ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>فاکتور ها</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>خریدار</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>شماره تماس</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('invoices-edit')
                            <th>فاکتور</th>
                            <th>پیش فاکتور</th>
                            <th>ویرایش</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($factors as $key => $factor)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $factor->invoice->customer->name }}</td>
                            <td>{{ $factor->invoice->province }}</td>
                            <td>{{ $factor->invoice->city }}</td>
                            <td>{{ $factor->invoice->phone }}</td>
                            <td>
                                @if($factor->status == 'paid')
                                    <span class="badge badge-success">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Factor::STATUS[$factor->status] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($factor->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('invoices-edit')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'factor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-floating {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('invoices.show', [$factor->invoice->id, 'type' => 'pishfactor']) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-floating {{ $factor->invoice->created_in == 'website' ? 'disabled' : '' }}" href="{{ route('factors.edit', $factor->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $factors->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


