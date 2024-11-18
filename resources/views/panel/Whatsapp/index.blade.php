@extends('panel.layouts.master')
@section('title', 'واتساپ')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>واتساپ</h6>
                @can('whatsapp-create')
                    <div>
                        <a href="{{ route('whatsapp.createGroup') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ارسال به گروه
                        </a>
                        <a href="{{ route('whatsapp.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ارسال پیام
                        </a>
                    </div>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center" style="width: 100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام ارسال کننده</th>
                        <th>نام گیرنده</th>
                        <th>شماره تلفن گیرنده</th>
                        <th>توضیحات</th>
                        <th>تاریخ ارسال</th>
                        <th>وضعیت</th>
                        <th>مشاهده</th>
                        @can('whatsapp-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($whatsapps as $key => $whatsapp)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $whatsapp->sender_name }}</td>
                            <td>{{ $whatsapp->receiver_name }}</td>
                            <td>{{ $whatsapp->phone }}</td>
                            <td>{{ Str::limit($whatsapp->description) }}</td>
                            <td>{{ verta($whatsapp->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>@if($whatsapp->status=='failed')
                                    <span class="badge badge-danger">ناموفق</span>
                                @elseif($whatsapp->status == 'successful')
                                    <span class="badge badge-success">موفق</span>
                                @else
                                    <span class="badge badge-secondary">{{ $whatsapp->status }}</span>
                                @endif</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('whatsapp.show', $whatsapp->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @can('whatsapp-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow"
                                            data-url="{{ route('whatsapp.destroy',$whatsapp->id) }}"
                                            data-id="{{ $whatsapp->id }}">
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
            <div class="d-flex justify-content-center">{{ $whatsapps->links() }}</div>
        </div>
    </div>
@endsection
