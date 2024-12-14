@extends('panel.layouts.master')
@section('title', 'بسته های ارسالی')
@section('content')
    {{--  Post Status Modal  --}}
    <div class="modal fade" id="postStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postStatusModalLabel">وضعیت مرسوله</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    {{--  End Post Status Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>بسته های ارسالی</h6>

                <div>
                    <form action="{{ route('packets.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('packets-create')
                        <a href="{{ route('packets.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ایجاد بسته ارسالی
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('packets.search') }}" method="get" id="search_form"></form>
{{--            @if(request()->ip() == '51.68.208.135')--}}
{{--                @dd($invoices)--}}
{{--            @endif--}}
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="invoice_id" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">سفارش (همه)</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ request()->invoice_id == $invoice->id ? 'selected' : '' }}>{{ $invoice->id.' - '.$invoice->customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="packet_status" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="2">
                        <option value="all">وضعیت بسته (همه)</option>
                        @foreach(\App\Models\Packet::PACKET_STATUS as $key => $value)
                            <option value="{{ $key }}" {{ request()->packet_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="invoice_status" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                        <option value="all">وضعیت فاکتور (همه)</option>
                        @foreach(\App\Models\Packet::INVOICE_STATUS as $key => $value)
                            <option value="{{ $key }}" {{ request()->invoice_status == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>گیرنده</th>
                        <th>آدرس</th>
                        <th>شماره سفارش</th>
                        <th>نوع ارسال</th>
                        <th>وضعیت بسته</th>
                        <th>وضعیت فاکتور</th>
                        <th>زمان ارسال</th>
                        <th>تاریخ ایجاد</th>
                        <th>وضعیت مرسوله</th>
                        @can('packets-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('packets-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($packets as $key => $packet)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $packet->receiver }}</td>
                            <td>{{ $packet->address }}</td>
                            <td>
                                <strong><u><a href="{{ route('invoices.show', [$packet->invoice_id, 'type' => 'pishfactor']) }}" class="text-primary" target="_blank">{{ $packet->invoice_id }}</a></u></strong>
                            </td>
                            <td>{{ \App\Models\Packet::SENT_TYPE[$packet->sent_type] }}</td>
                            <td>
                                @if($packet->packet_status == 'delivered')
                                    <span class="badge badge-success">{{ \App\Models\Packet::PACKET_STATUS[$packet->packet_status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Packet::PACKET_STATUS[$packet->packet_status] }}</span>
                                @endif
                            </td>
                            <td>
                                @if($packet->invoice_status == 'delivered')
                                    <span class="badge badge-success">{{ \App\Models\Packet::INVOICE_STATUS[$packet->invoice_status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Packet::INVOICE_STATUS[$packet->invoice_status] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($packet->sent_time)->format('Y/m/d') }}</td>
                            <td>{{ verta($packet->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <button class="btn btn-primary btn-floating btn_post_status" type="button" data-toggle="modal" data-target="#postStatusModal" data-code="{{ $packet->send_tracking_code }}" {{ $packet->send_tracking_code != null && $packet->sent_type == 'post' ? '' : 'disabled' }}>
                                    <i class="fa fa-truck"></i>
                                </button>
                            </td>
                            @can('packets-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('packets.edit', ['packet' => $packet->id, 'url' => request()->getRequestUri()]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('packets-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('packets.destroy',$packet->id) }}" data-id="{{ $packet->id }}">
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
            <div class="d-flex justify-content-center">{{ $packets->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // btn post status
        $('.btn_post_status').on('click', function () {
            var code = $(this).data('code');
            console.log($(this))
            $('#postStatusModal .modal-body').html(`<div class="spinner-grow text-primary"></div>`)

            $.ajax({
                url: "{{ route('get-post-status') }}",
                type: 'post',
                data: {
                    code
                },
                success: function (res) {
                    $('#postStatusModal .modal-body').html('')

                    $.each(res.data, function (i, item) {
                        if(item.is_header){
                            $('#postStatusModal .modal-body').append(`
                                    <table class="table table-bordered table-striped text-center">
                                        <thead class="bg-primary">
                                            <tr>
                                                <th colspan="2">${item.title}</th>
                                                <th>موقعیت</th>
                                                <th>ساعت</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                `)
                        }else{
                            $('#postStatusModal .modal-body table:last tbody').append(`
                                        <tr>
                                            <td>${item.row}</td>
                                            <td>${item.last_status}</td>
                                            <td>${item.location}</td>
                                            <td>${item.time}</td>
                                        </tr>
                                `)
                        }
                    })
                }
            })
        })
        // end btn post status
    </script>
@endsection


