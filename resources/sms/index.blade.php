@extends('panel.layouts.master')
@section('title', 'پیامک‌ها')
@section('content')
    @can('sms-list')
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex justify-content-between align-items-center">
                    <h6>پیامک‌ها</h6>
                    <div>
                        <form action="{{ route('sms.excel') }}" method="post" id="excel_form">
                            @csrf
                        </form>

                        @can('sms-create')
                            <a href="{{ route('sms.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus mr-2"></i>
                                ایجاد پیامک
                            </a>
                        @endcan
                    </div>

                </div>
                <form action="{{ route('sms.search') }}" method="get" id="search_form"></form>
                <div class="row mb-3">
                    <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                        <input type="text" name="receiver_name" class="form-control" placeholder="نام گیرنده" value="{{ request()->receiver_name ?? null }}" form="search_form">
                    </div>
                    <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                        <input type="text" name="receiver_phone" class="form-control" placeholder="شماره گیرنده" value="{{ request()->receiver_phone ?? null }}" form="search_form">
                    </div>
                    <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                        <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>نام گیرنده</th>
                            <th>شماره گیرنده</th>
                            <th>پیام</th>
                            <th>تاریخ ایجاد</th>
                            @can('sms-show')
                            <th>مشاهده</th>
                            @endcan
                            @can('sms-delete')
                                <th>حذف</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($smsList as $key => $sms)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $sms->receiver_name }}</td>
                                <td>{{ $sms->receiver_phone }}</td>
                                <td>{{ $sms->message }}</td>
                                <td>{{ $sms->status }}</td>
                                <td>{{ verta($sms->created_at)->format('H:i - Y/m/d') }}</td>
                                @can('sms-show')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('sms.show', $sms->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                @endcan
                                @can('sms-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('sms.destroy',$sms->id) }}" data-id="{{ $sms->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
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
                <div class="d-flex justify-content-center">{{ $smsList->appends(request()->all())->links() }}</div>
            </div>
        </div>
    @endcan
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
@endsection
