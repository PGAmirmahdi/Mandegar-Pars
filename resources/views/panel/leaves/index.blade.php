@extends('panel.layouts.master')
@section('title', 'درخواست مرخصی')
@section('content')
    {{--  Leave Modal  --}}
    <div class="modal fade" id="leaveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveModalLabel">وضعیت درخواست</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>درخواست شما</h5>
                    <strong>عنوان</strong>
                    <p id="leave_title"></p>
                    <strong>نوع</strong>
                    <p id="leave_type"></p>
                    <strong>تاریخ مرخصی</strong>
                    <p id="leave_date"></p>
                    <strong>توضیحات</strong>
                    <p id="leave_desc"></p>
                    <div id="answer_sec" class="d-none">
                        <hr>
                        <h5>پاسخ درخواست</h5>
                        <p id="leave_answer"></p>
                        <strong>زمان پاسخ</strong>
                        <p id="leave_answer_time"></p>
                        <strong>توضیحات</strong>
                        <p id="leave_answer_text"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    {{--  End Leave Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>درخواست مرخصی</h6>
                @can('leaves-create')
                    <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        درخواست مرخصی
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>نوع</th>
                        @can('ceo')
                            <th>درخواست دهنده</th>
                        @endcan
                        <th>تاریخ مرخصی</th>
                        <th>وضعیت</th>
                        @can('ceo')
                            <th>تایید کننده</th>
                        @endcan
                        <th>تاریخ ایجاد</th>
                        @cannot('ceo')
                            <th>مشاهده</th>
                        @endcannot
                        @can('ceo')
                            <th>تعیین وضعیت</th>
                        @endcan
                        @cannot('ceo')
                            @can('leaves-delete')
                                <th>حذف</th>
                            @endcan
                        @endcannot
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaves as $key => $leave)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $leave->title }}</td>
                            <td>{{ \App\Models\Leave::TYPE[$leave->type] }}</td>
                            @can('ceo')
                                <td>{{ $leave->user->fullName() }}</td>
                            @endcan
                            <td>{{ verta($leave->from_date)->format('Y/m/d') }}</td>
                            <td>
                                @if($leave->status == 'accept')
                                    <span class="badge badge-success">{{ \App\Models\Leave::STATUS[$leave->status] }}</span>
                                @elseif($leave->status == 'reject')
                                    <span class="badge badge-danger">{{ \App\Models\Leave::STATUS[$leave->status] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Leave::STATUS[$leave->status] }}</span>
                                @endif
                            </td>
                            @can('ceo')
                                <td>{{ isset($leave->acceptor->name) && isset($leave->acceptor->family) ? $leave->acceptor->name . ' ' . $leave->acceptor->family : '' }}</td>
                            @endcan
                            <td>{{ verta($leave->created_at)->format('H:i - Y/m/d') }}</td>
                            @cannot('ceo')
                                <td>
                                    <button class="btn btn-info btn-floating btn_show" data-toggle="modal" data-target="#leaveModal" data-id="{{ $leave->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            @endcannot
                            @can('ceo')
                                <td>
                                    <a href="{{ route('leaves.edit', $leave->id) }}" class="btn btn-primary btn-floating">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </td>
                            @endcan
                            @cannot('ceo')
                                @can('leaves-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('leaves.destroy', $leave->id) }}" data-id="{{ $leave->id }}" {{ $leave->status != 'pending' ? 'disabled' : '' }}>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            @endcannot
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $leaves->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn_show', function () {
                let leave_id = $(this).data('id');

                $.ajax({
                    url: '/panel/get-leave-info',
                    type: 'post',
                    data: {
                        leave_id: leave_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        $('#leave_title').text(res.data.title);
                        $('#leave_desc').text(res.data.desc);
                        $('#leave_type').text(res.data.typeText);

                        if (res.data.type == 'hourly') {
                            $('#leave_date').text(res.data.to + ' تا ' + res.data.from + ' - ' + res.data.date);
                        } else {
                            $('#leave_date').text(res.data.date);
                        }

                        if (res.data.status != 'pending') {
                            $('#answer_sec').removeClass('d-none');
                            $('#leave_answer').text(`درخواست شما توسط "${res.data.acceptor}" به وضعیت "${res.data.statusText}" تغییر یافت`);
                            $('#leave_answer_text').text(res.data.answer);
                            $('#leave_answer_time').text(res.data.answer_time);
                        } else {
                            $('#answer_sec').addClass('d-none');
                        }
                    },
                    error: function (err) {
                        console.error('Error fetching leave info:', err);
                    }
                });
            });
        });
    </script>
@endsection
