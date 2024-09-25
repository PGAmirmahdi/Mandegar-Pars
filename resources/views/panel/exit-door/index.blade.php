@extends('panel.layouts.master')
@section('title', 'ثبت خروج')
@section('content')
    {{--  description Modal  --}}
    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">توضیحات</h5>
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
    {{--  end description Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت خروج</h6>
                <a href="{{ route('exit-door.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-2"></i>
                    ثبت خروج
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>سفارش</th>
                        <th>شماره سفارش</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th>توضیحات</th>
                        <th>رسید انبار</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->inventory_report->invoice->customer->name }}</td>
                            <td>
                                <strong><u><a href="{{ route('invoices.show', [$item->inventory_report->invoice_id]) }}" class="text-primary" target="_blank">{{ $item->inventory_report->invoice_id }}</a></u></strong>
                            </td>
                            <td>
                                @if($item->status == 'confirmed')
                                    <span class="badge badge-success">تایید شده</span>
                                @else
                                    <span class="badge badge-warning">تایید نشده</span>
                                @endif
                            </td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <button class="btn btn-info btn-floating btn_modal" data-toggle="modal" data-target="#descriptionModal" data-id="{{ $item->id }}" {{ $item->description ? '' : 'disabled' }}>
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('inventory-reports.show', $item->inventory_report_id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('exit-door.destroy', $item->id) }}" data-id="{{ $item->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
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
            <div class="d-flex justify-content-center">{{ $data->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#descriptionModal .modal-body').html('<div class="spinner-grow text-primary"></div>')

            $('.btn_modal').on('click', function () {
                let exit_door_id = $(this).data('id');
                $.ajax({
                    url: '/panel/exit-door-desc/'+exit_door_id,
                    type: 'get',
                    success: function (res) {
                        $('#descriptionModal .modal-body').html(res.data)
                    }
                })
            })
        })
    </script>
@endsection


