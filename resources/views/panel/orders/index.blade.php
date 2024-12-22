@extends('panel.layouts.master')
@section('title', 'سفارشات مشتری')
@section('styles')
    <style>
        #stats i.fa, i.fab {
            font-size: 15px;
        }


        .progress-vertical {
            width: 10px;
            height: 30px;
            min-height: 45px;
            margin: 0 15px -8px 0;
        }


        .timeline-stage {
            display: flex;
            align-items: center;
        }

        .stage-circle {
            width: 40px;
            height: 40px;
            line-height: 45px;
            text-align: center;
            flex-shrink: 0;
        }

        .progress, .progress-stacked {
            border-radius: 0;
        }


        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            margin: 1px 0;
        }

        .modal-body {
            margin-right: 20%;
        }

        @keyframes spin {
            from {
                transform: rotate(360deg);
            }
            to {
                transform: rotate(0deg);
            }
        }

        .rotate-icon {
            display: inline-block;
            animation: spin 1s linear infinite;
        }


        .lds-roller,
        .lds-roller div,
        .lds-roller div:after {
            box-sizing: border-box;
        }

        .lds-roller {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .lds-roller div {
            animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            transform-origin: 40px 40px;
        }

        .lds-roller div:after {
            content: " ";
            display: block;
            position: absolute;
            width: 7.2px;
            height: 7.2px;
            border-radius: 50%;
            background: currentColor;
            margin: -3.6px 0 0 -3.6px;
        }

        .lds-roller div:nth-child(1) {
            animation-delay: -0.036s;
        }

        .lds-roller div:nth-child(1):after {
            top: 62.62742px;
            left: 62.62742px;
        }

        .lds-roller div:nth-child(2) {
            animation-delay: -0.072s;
        }

        .lds-roller div:nth-child(2):after {
            top: 67.71281px;
            left: 56px;
        }

        .lds-roller div:nth-child(3) {
            animation-delay: -0.108s;
        }

        .lds-roller div:nth-child(3):after {
            top: 70.90963px;
            left: 48.28221px;
        }

        .lds-roller div:nth-child(4) {
            animation-delay: -0.144s;
        }

        .lds-roller div:nth-child(4):after {
            top: 72px;
            left: 40px;
        }

        .lds-roller div:nth-child(5) {
            animation-delay: -0.18s;
        }

        .lds-roller div:nth-child(5):after {
            top: 70.90963px;
            left: 31.71779px;
        }

        .lds-roller div:nth-child(6) {
            animation-delay: -0.216s;
        }

        .lds-roller div:nth-child(6):after {
            top: 67.71281px;
            left: 24px;
        }

        .lds-roller div:nth-child(7) {
            animation-delay: -0.252s;
        }

        .lds-roller div:nth-child(7):after {
            top: 62.62742px;
            left: 17.37258px;
        }

        .lds-roller div:nth-child(8) {
            animation-delay: -0.288s;
        }

        .lds-roller div:nth-child(8):after {
            top: 56px;
            left: 12.28719px;
        }

        @keyframes lds-roller {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .loading {
            margin-right: 30%;
        }


    </style>

@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">سفارشات مشتری</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                            @cannot('accountant')
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle font-size-20 align-middle"></i>
                                    <strong>توجه!</strong>
                                    همکار فروش گرامی درصورت نیاز به <u>تایید پیش فاکتور</u> توسط شما، <u>دکمه اقدام</u>
                                    فعال خواهد شد
                                </div>
                            @endcannot
                            @can('accountant')
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle font-size-20 align-middle"></i>
                                    <strong>توجه!</strong>
                                  حسابدار گرامی قیمت کالا ها ، به صورت قیمت تمام شده (به همراه مالیات ، ارزش افزوده و ...) محاسبه شده است . در صورت نیاز به اطلاعات بیشتر با واحد فروش ارتباط برقرار کنید.
                                </div>
                            @endcan
                            <div class="card-title d-flex justify-content-end">
                                <div>
                                    <form action="{{ route('orders.excel') }}" method="post" id="excel_form">
                                        @csrf
                                    </form>

                                    <button class="btn btn-success" form="excel_form">
                                        <i class="fa fa-file-excel mr-2"></i>
                                        دریافت اکسل
                                    </button>

                                    @can('customer-order-list')
                                        @cannot('accountant')
                                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus mr-2"></i>
                                                ایجاد سفارش
                                            </a>
                                        @endcannot
                                    @endcan
                                </div>
                            </div>
                            <form action="{{ route('orders.index') }}" method="get" id="search_form"></form>
                            <div class="row mb-3 mt-5">
                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                    <select name="customer_id" form="search_form" class="form-control"
                                            data-toggle="select2">
                                        <option value="all">خریدار (همه)</option>
                                        @foreach($customers as $customer)
                                            <option
                                                value="{{ $customer->id }}" {{ request()->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{--                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">--}}
                                {{--                                    <select name="province" form="search_form" class="form-control"--}}
                                {{--                                            data-toggle="select2">--}}
                                {{--                                        <option value="all">استان (همه)</option>--}}
                                {{--                                        @foreach(\App\Models\Province::all('name') as $province)--}}
                                {{--                                            <option--}}
                                {{--                                                value="{{ $province->name }}" {{ request()->province == $province->name ? 'selected' : '' }}>{{ $province->name }}</option>--}}
                                {{--                                        @endforeach--}}
                                {{--                                    </select>--}}
                                {{--                                </div>--}}
                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                    <select name="status" form="search_form" class="form-control" data-toggle="select2">
                                        <option value="all">وضعیت (همه)</option>
                                        {{--                                        @foreach(\App\Models\Invoice::STATUS as $key => $value)--}}
                                        {{--                                            <option--}}
                                        {{--                                                value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>{{ $value }}</option>--}}
                                        <option value="orders" {{ request()->status == 'order' ? 'selected' : '' }}>ثبت
                                            سفارش
                                        </option>
                                        <option value="pending" {{ request()->status == 'pending' ? 'selected' : '' }}>
                                            پیش فاکتور شده
                                        </option>
                                        <option
                                            value="invoiced" {{ request()->status == 'invoiced' ? 'selected' : '' }}>
                                            فاکتور شده
                                        </option>
                                        {{--                                        @endforeach--}}
                                    </select>
                                </div>
                                {{--                                @can('accountant')--}}
                                {{--                                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">--}}
                                {{--                                        <select name="user" form="search_form" class="form-control"--}}
                                {{--                                                data-toggle="select2">--}}
                                {{--                                            <option value="all">همکار (همه)</option>--}}
                                {{--                                            @foreach(\App\Models\User::whereIn('role_id', $roles_id)->get() as $user)--}}
                                {{--                                                <option--}}
                                {{--                                                    value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>{{ $user->fullName() }}</option>--}}
                                {{--                                            @endforeach--}}
                                {{--                                        </select>--}}
                                {{--                                    </div>--}}
                                {{--                                @endcan--}}
                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                    <input type="text" form="search_form" name="code" class="form-control"
                                           value="{{ request()->code ?? null }}" placeholder="شناسه سفارش">
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dataTable dtr-inline text-center"
                                       style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>شناسه سفارش</th>
                                        <th>نوع فروش</th>
                                        <th>خریدار</th>
                                        <th>درخواست جهت</th>
                                        <th>استان</th>
                                        <th>شهر</th>
                                        <th>شماره تماس</th>
                                        <th>وضعیت</th>
                                        @canany(['accountant', 'sales-manager'])
                                            <th>همکار</th>
                                        @endcanany
                                        <th>تاریخ ایجاد</th>
                                        <th>وضعیت سفارش</th>
                                        {{--                        @canany(['accountant','admin','ceo'])--}}
                                        <th>مشاهده سفارش</th>
                                        {{--                        @endcanany--}}
                                        {{--                                        <th>وضعیت سفارش</th>--}}

                                        @canany(['sales-manager','accountant'])
                                            <th>اقدام</th>
                                        @endcanany

                                        @cannot('accountant')
                                            @can('customer-order-edit')
                                                <th>ویرایش</th>
                                            @endcan
                                            @can('customer-order-delete')
                                                <th>حذف</th>
                                            @endcan
                                        @endcannot
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $search = request()->input('code');
                                    @endphp
                                    @foreach($orders as $key => $order)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            @php
                                                $highlightedNumber = $order->code ?? '---';
                                                if ($search) {
                                                    $highlightedNumber = str_ireplace($search, "<span class='bg-warning'>" . $search . "</span>", $highlightedNumber);
                                                }
                                            @endphp
                                            <td>{!! $highlightedNumber !!}</td>
                                            <td>{{ \App\Models\Customer::CUSTOMER_TYPE[$order->customer->customer_type] }}</td>
                                            <td>{{ $order->customer->name }}</td>
                                            <td>{{ \App\Models\Invoice::REQ_FOR[$order->req_for] }}</td>
                                            <td>{{ $order->customer->province }}</td>
                                            <td>{{ $order->customer->city }}</td>
                                            <td>{{ $order->customer->phone1 }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-primary d-block">{{ \App\Models\Invoice::STATUS[$order->status] }}</span>
                                            </td>
                                            @canany(['accountant', 'sales-manager'])
                                                <td>{{ $order->user->fullName() }}</td>
                                            @endcanany
                                            <td>{{ verta($order->created_at)->format('H:i - Y/m/d') }}</td>
                                            <td>
                                                <a class="btn btn-primary btn-floating show-status"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#timelineModal" data-id="{{$order->id}}"
                                                   data-code="{{$order->code}}">
                                                    <i class="fa fa-info"></i>
                                                </a>
                                            </td>
                                            {{--                            @canany(['accountant','admin','ceo'])--}}
                                            <td>
                                                <a class="btn btn-info btn-floating"
                                                   href="{{ route('orders.show', $order->id) }}" target="_blank">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>

                                            @can('warehouse-keeper')

                                                <td>
                                                    <a href="{{ $order->action ? $order->action->factor_file ?? '#' : '#' }}"
                                                       class="btn btn-primary btn-floating {{ $order->action ? $order->action->factor_file ? '' : 'disabled' : 'disabled' }}"
                                                       target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                </td>
                                            @else
                                                @canany(['sales-manager','accountant'])
                                                    <td>
                                                        <a class="btn btn-primary btn-floating @cannot('accountant') {{ $order->action ? '' : 'disabled' }} @endcannot"
                                                           href="{{ route('order.action', $order->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcanany
                                            @endcan
                                            @cannot('accountant')
                                                @can('sales-manager')
                                                    @can('customer-order-edit')
                                                        <td>
                                                            <a class="btn btn-warning btn-floating {{ $order->created_in == 'website' ? 'disabled' : '' }}"
                                                               href="{{ route('orders.edit', $order->id) }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    @endcan
                                                    @can('customer-order-delete')
                                                        <td>
                                                            <button class="btn btn-danger btn-floating trashRow"
                                                                    data-url="{{ route('orders.destroy',$order->id) }}"
                                                                    data-id="{{ $order->id }}" {{ $order->created_in == 'website' ? 'disabled' : '' }}>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    @endcan
                                                @else
                                                    @can('customer-order-edit')
                                                        <td>
                                                            <a class="btn btn-warning btn-floating {{ $order->created_in == 'website' || ($order->status == 'invoiced' && $order->req_for != 'amani-invoice') ? 'disabled' : '' }}"
                                                               href="{{ route('orders.edit', $order->id) }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        </td>
                                                    @endcan
                                                    @can('customer-order-delete')
                                                        <td>
                                                            <button class="btn btn-danger btn-floating trashRow"
                                                                    data-url="{{ route('orders.destroy',$order->id) }}"
                                                                    data-id="{{ $order->id }}" {{ $order->created_in == 'website' || $order->status == 'invoiced' ? 'disabled' : '' }}>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    @endcan
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
                            <div
                                class="d-flex justify-content-center">{{ $orders->appends(request()->all())->links() }}</div>
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

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.show-status', function () {
                var id = $(this).data('id');
                var code = $(this).data('code');
                $('#timelineModalLabel').text(`وضعیت سفارش ${code}`)
                var loading = $('.loading');
                var timelineContent = $('.timeline-content');
                timelineContent.empty();
                loading.show();
                $.ajax({
                    url: '/panel/get-customer-order-status/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        loading.hide();
                        console.log('Response:', response);
                        response.forEach((stage, index) => {
                            const hasDate = stage.date !== '';
                            const stageClass = stage.pending ? 'bg-warning' : hasDate ? 'bg-success' : 'bg-secondary';
                            const icon = stage.pending ? '<i class="fa fa-undo rotate-icon"></i>' : hasDate ? '✓' : '✖';
                            const date = stage.pending ? 'در حال بررسی' : hasDate ? stage.date : '';

                            const progressBar = index === 0 ? '' : `
                            <div class="progress progress-vertical ${stageClass}">
                                <div class="progress-bar progress-bar-striped progress-bar-animated ${stageClass}" role="progressbar" style="height: 100%;"></div>
                            </div>
                        `;

                            const stageTemplate = `
                            ${progressBar}
                            <div class="timeline-stage stage-left d-flex align-items-center">
                                <div class="rounded-circle ${stageClass} text-white stage-circle me-2">${icon}</div>
                                <div>
                                    <h6 class="stage-text" style="font-weight: bolder;font-size: medium;">${stage.status_label}</h6>
                                    <small class="stage-text">${date}</small>
                                </div>
                            </div>
                        `;

                            timelineContent.append(stageTemplate);
                        });

                        timelineContent.show();
                    }
                    ,

                    error: function (xhr, status, error) {
                        console.log('Error:', error);
                        loading.hide();
                    }
                });

            });
        });
    </script>
@endsection


