@extends('panel.layouts.master')
@section('title', 'تیکت ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>تیکت ها</h6>
                @can('tickets-create')
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت تیکت
                    </a>
                @endcan
            </div>
{{--            <form action="{{ route('tickets.search') }}" method="post" id="search_form">--}}
{{--                @csrf--}}
{{--            </form>--}}
{{--            <div class="row mb-3">--}}
{{--                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">--}}
{{--                    <input type="text" name="code" class="form-control" placeholder="کد محصول" value="{{ request()->code ?? null }}" form="search_form">--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">--}}
{{--                    <input type="text" name="title" class="form-control" placeholder="عنوان محصول" value="{{ request()->title ?? null }}" form="search_form">--}}
{{--                </div>--}}
{{--                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">--}}
{{--                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>فرستنده</th>
                        <th>گیرنده</th>
                        <th>عنوان تیکت</th>
                        <th>شماره تیکت</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        @can('tickets-create')
                            <th>مشاهده</th>
                        @endcan
                        @can('tickets-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $key => $ticket)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $ticket->sender_id != auth()->id() ? $ticket->sender->fullName() : 'شما' }}</td>
                            <td>{{ $ticket->receiver_id != auth()->id() ? $ticket->receiver->fullName() : 'شما' }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>{{ $ticket->code }}</td>
                            <td>
                                @if($ticket->status == 'closed')
                                    <span class="badge badge-success">بسته شده</span>
                                @else
                                    <span class="badge badge-warning">درحال بررسی</span>
                                @endif
                            </td>
                            <td>{{ verta($ticket->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('tickets-create')
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('tickets.edit', $ticket->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('tickets-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('tickets.destroy',$ticket->id) }}" data-id="{{ $ticket->id }}">
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
            <div class="d-flex justify-content-center">{{ $tickets->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
@endsection
