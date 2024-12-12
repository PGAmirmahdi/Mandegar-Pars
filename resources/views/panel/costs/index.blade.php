@extends('panel.layouts.master')
@section('title', 'بهای تمام شده')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">بهای تمام شده</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                            <div class="card-title d-flex justify-content-end">
                                <div>
                                    <form action="{{ route('costs.excel') }}" method="post" id="excel_form">
                                        @csrf
                                    </form>

                                    <button class="btn btn-success" form="excel_form">
                                        <i class="fa fa-file-excel mr-2"></i>
                                        دریافت اکسل
                                    </button>

                                    @can('costs-create')
                                        {{--                                        @cannot('accountant')--}}
                                        <a href="{{ route('costs.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus mr-2"></i>
                                            ایجاد بهای تمام شده
                                        </a>
                                        {{--                                        @endcannot--}}
                                    @endcan
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dataTable dtr-inline text-center"
                                       style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>نام کالا</th>
                                        <th>تعداد</th>
                                        <th>قیمت بدون مالیات و ارزش افزوده</th>
                                        <th>هزینه حمل و نقل</th>
                                        <th>سایر هزینه ها</th>
                                        <th>بهای تمام شده</th>
                                        @canany(['accountant', 'sales-manager','admin','ceo'])
                                            <th>همکار</th>
                                        @endcanany
                                        <th>تاریخ ایجاد</th>


                                        @can('costs-edit')
                                            <th>ویرایش</th>
                                        @endcan
                                        @can('costs-delete')
                                            <th>حذف</th>
                                        @endcan

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($costs as $key => $cost)
                                        <tr>
                                            <td>{{ ++$key }}</td>

                                            <td>{{$cost->product}}</td>
                                            <td>{{$cost->count}}</td>
                                            <td>{{ number_format($cost->price) }} ریال</td>
                                            <td>{{ number_format($cost->Logistic_price) }} ریال</td>
                                            <td>{{ number_format($cost->other_price) }} ریال</td>
                                            <td>{{ number_format($cost->final_price) }} ریال</td>
                                            @canany(['accountant', 'sales-manager','admin','ceo'])
                                                <td>{{ $cost->user->fullName() }}</td>
                                            @endcanany
                                            <td>{{ verta($cost->created_at)->format('H:i - Y/m/d') }}</td>



                                            @can('sales-manager')
                                                @can('costs-edit')
                                                    <td>
                                                        <a class="btn btn-warning btn-floating"
                                                           href="{{ route('costs.edit', $cost->id) }}" >
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcan
                                                @can('costs-delete')
                                                    <td>
                                                        <button class="btn btn-danger btn-floating trashRow"
                                                                data-url="{{ route('costs.destroy',$cost->id) }}"
                                                                data-id="{{ $cost->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                @endcan
                                            @else
                                                @can('costs-edit')
                                                    <td>
                                                        <a class="btn btn-warning btn-floating"
                                                           href="{{ route('costs.edit', $cost->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcan
                                                @can('costs-delete')
                                                    <td>
                                                        <button class="btn btn-danger btn-floating trashRow"
                                                                data-url="{{ route('costs.destroy',$cost->id) }}"
                                                                data-id="{{ $cost->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                @endcan
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
                            <div
                                class="d-flex justify-content-center">{{ $costs->appends(request()->all())->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="timelineModal" tabindex="-1" aria-labelledby="timelineModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="timelineModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <!-- تایم‌لاین عمودی -->
                    <div class="d-flex flex-column position-relative">

                        <!-- مرحله 1 (متن در چپ) -->
                        <div class="timeline-content" style="display: none;">
                        </div>


                        <div class="loading">
                            <div class="lds-roller">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--@section('scripts')--}}
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            $(document).on('click', '.show-status', function () {--}}
{{--                var id = $(this).data('id');--}}
{{--                var code = $(this).data('code');--}}
{{--                $('#timelineModalLabel').text(`وضعیت سفارش ${code}`)--}}
{{--                var loading = $('.loading');--}}
{{--                var timelineContent = $('.timeline-content');--}}
{{--                timelineContent.empty();--}}
{{--                loading.show();--}}
{{--                $.ajax({--}}
{{--                    url: '/panel/get-customer-order-status/' + id,--}}
{{--                    type: 'GET',--}}
{{--                    dataType: 'json',--}}
{{--                    success: function (response) {--}}
{{--                        loading.hide();--}}
{{--                        console.log('Response:', response);--}}
{{--                        response.forEach((stage, index) => {--}}
{{--                            const hasDate = stage.date !== '';--}}
{{--                            const stageClass = stage.pending ? 'bg-warning' : hasDate ? 'bg-success' : 'bg-secondary';--}}
{{--                            const icon = stage.pending ? '<i class="fa fa-undo rotate-icon"></i>' : hasDate ? '✓' : '✖';--}}
{{--                            const date = stage.pending ? 'در حال بررسی' : hasDate ? stage.date : '';--}}

{{--                            const progressBar = index === 0 ? '' : `--}}
{{--                            <div class="progress progress-vertical ${stageClass}">--}}
{{--                                <div class="progress-bar progress-bar-striped progress-bar-animated ${stageClass}" role="progressbar" style="height: 100%;"></div>--}}
{{--                            </div>--}}
{{--                        `;--}}

{{--                           const stageTemplate = `--}}
{{--                            ${progressBar}--}}
{{--                            <div class="timeline-stage stage-left d-flex align-items-center">--}}
{{--                                <div class="rounded-circle ${stageClass} text-white stage-circle me-2">${icon}</div>--}}
{{--                                <div>--}}
{{--                                    <h6 class="stage-text" style="font-weight: bolder;font-size: medium;">${stage.status_label}</h6>--}}
{{--                                    <small class="stage-text">${date}</small>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        `;--}}

{{--                            timelineContent.append(stageTemplate);--}}
{{--                        });--}}

{{--                        timelineContent.show();--}}
{{--                    }--}}
{{--                    ,--}}

{{--                    error: function (xhr, status, error) {--}}
{{--                        console.log('Error:', error);--}}
{{--                        loading.hide();--}}
{{--                    }--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}


