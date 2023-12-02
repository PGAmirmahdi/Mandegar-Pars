@extends('panel.layouts.master')
@section('title', 'پیام های ارسال شده')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پیام های ارسال شده</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        @canany(['admin','ceo'])
                            <th>فرستنده</th>
                        @endcanany
                        <th>شماره موبایل</th>
                        <th>متن پیام</th>
                        <th>وضعیت</th>
                        <th>زمان ارسال</th>
                        <th>مشاهده</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sms_histories as $key => $sms_history)
                        <tr>
                            <td>{{ ++$key }}</td>
                            @canany(['admin','ceo'])
                                <td>{{ $sms_history->user->fullName() }}</td>
                            @endcanany
                            <td>{{ $sms_history->phone }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($sms_history->text,40) }}</td>
                            <td>
                                @if($sms_history->status == 'sent')
                                    <span class="badge badge-success">ارسال شده</span>
                                @else
                                    <span class="badge badge-warning">ناموفق</span>
                                @endif
                            </td>
                            <td>{{ verta($sms_history->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('sms-histories.show', $sms_history->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $sms_histories->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection


