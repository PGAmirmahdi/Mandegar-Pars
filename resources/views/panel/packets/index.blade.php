@extends('panel.layouts.master')
@section('title', 'بسته های ارسالی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>بسته های ارسالی</h6>
                @can('packets-create')
                    <a href="{{ route('packets.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد بسته ارسالی
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>گیرنده</th>
                        <th>آدرس</th>
                        <th>شماره فاکتور</th>
                        <th>نوع ارسال</th>
                        <th>کد رهگیری ارسالی شرکت</th>
                        <th>وضعیت بسته</th>
                        <th>وضعیت فاکتور</th>
                        <th>زمان ارسال</th>
                        <th>تاریخ ایجاد</th>
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
                                <strong><u><a href="{{ route('invoices.show', $packet->invoice_id) }}" class="text-primary" target="_blank">{{ $packet->invoice_id }}</a></u></strong>
                            </td>
                            <td>{{ \App\Models\Packet::SENT_TYPE[$packet->sent_type] }}</td>
                            <td>{{ $packet->send_tracking_code }}</td>
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
                            @can('packets-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('packets.edit', $packet->id) }}">
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
            <div class="d-flex justify-content-center">{{ $packets->links() }}</div>
        </div>
    </div>
@endsection


