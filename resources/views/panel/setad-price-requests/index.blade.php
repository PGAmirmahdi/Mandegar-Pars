@extends('panel.layouts.master')
@section('title', 'لیست درخواست ستاد')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست درخواست ستاد</h6>
                @can('setad-price-requests-create')
                    <a href="{{ route('setad_price_requests.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت درخواست ستاد
                    </a>
                @endcan
            </div>
            <div class="modal fade" id="actionResultModal" tabindex="-1" aria-labelledby="actionResultModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="actionResultModalLabel">ثبت نتیجه نهایی</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                        </div>
                        <div class="modal-body">
                            <form id="actionResultForm" method="POST"
                                  action="{{ route('setad_price_requests.actionResult') }}">
                                @csrf
                                <input type="hidden" id="rowId" name="row_id">
                                <div class="mb-3">
                                    <label for="resultSelect" class="form-label">نتیجه نهایی</label>
                                    <select id="resultSelect" class="form-control" name="result">
                                        <option value="winner">برنده شدیم</option>
                                        <option value="lose">برنده نشدیم</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">توضیحات</label>
                                    <textarea id="description" class="form-control" name="description"
                                              rows="3"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لغو</button>
                                    <button type="submit" id="saveActionResult" class="btn btn-success">ذخیره</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('setad_price_requests.index') }}" method="get" id="search_form">
                <div class="row mt-2 mb-2">
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="code" form="search_form" class="form-control js-example-basic-single" data-select2-id="0">
                            <option value="all">شناسه (همه)</option>
                            @foreach(\App\Models\SetadPriceRequest::all() as $setadpricerequest)
                                <option value="{{ $setadpricerequest->code }}" {{ request()->code == $setadpricerequest->code ? 'selected' : '' }}>
                                    {{ $setadpricerequest->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="user" form="search_form" class="form-control js-example-basic-single" data-select2-id="1">
                            <option value="all" {{ request()->user == 'all' ? 'selected' : '' }}>ثبت کننده (همه)</option>
                            @foreach(\App\Models\User::whereIn('id', \App\Models\SetadPriceRequest::pluck('user_id'))->get() as $user)
                                <option value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                    {{ $user->name . ' ' . $user->family }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="acceptor" form="search_form" class="form-control js-example-basic-single" data-select2-id="2">
                            <option value="all" {{ request()->acceptor == 'all' ? 'selected' : '' }}>تایید کننده (همه)</option>
                            @foreach(\App\Models\User::whereIn('id', \App\Models\SetadPriceRequest::pluck('acceptor_id'))->get() as $user)
                                <option value="{{ $user->id }}" {{ request()->acceptor == $user->id ? 'selected' : '' }}>
                                    {{ $user->name . ' ' . $user->family }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <select name="status" form="search_form" class="form-control js-example-basic-single" data-select2-id="3">
                            <option value="all">وضعیت (همه)</option>
                            @foreach(\App\Models\SetadPriceRequest::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <input type="text" name="from_date" placeholder="از تاریخ(مهلت تایید)" form="search_form" autocomplete="off"
                               class="form-control date-picker-shamsi-list" value="{{ request()->from_date }}">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                        <input type="text" name="to_date" placeholder="تا تاریخ(مهلت تایید)" form="search_form" autocomplete="off"
                               class="form-control date-picker-shamsi-list" value="{{ request()->to_date }}">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                    <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>شناسه</th>
                            <th>ثبت کننده</th>
                            <th>وضعیت</th>
                            <th>زمان ثبت</th>
                            <th>مهلت تایید</th>
                            <th>مهلت باقی مانده</th>
                            <th>تایید/رد کننده</th>
                            <th>نتیجه نهایی</th>
                            @canany(['ceo','admin'])
                                <th>ثبت قیمت</th>
                            @else
                                <th>مشاهده قیمت</th>
                                <th>ویرایش</th>
                            @endcanany
                            @can('setad-price-requests-delete')
                                <th>حذف</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($setadprice_requests as $key => $setadprice_request)
                            @php
                                $paymentDue = verta($setadprice_request->date); // تاریخ هجری شمسی
                                $paymentDueGregorian = $paymentDue->toCarbon()->format('Y-m-d');
                                $today = verta(now())->format('Y-m-d');
                                // محاسبه تفاوت تاریخ‌ها به روز
                                $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($paymentDueGregorian), false);
                            @endphp
                            <tr class="@if($daysLeft <= 0 && !in_array($setadprice_request->status, ['accepted','winner'])) table-danger @elseif($daysLeft > 0 && $daysLeft <= 2 && !in_array($setadprice_request->status, ['accepted','winner','lose'])) table-warning @elseif($daysLeft > 2) @elseif($setadprice_request->status == 'accepted') table-success @else @endif">
                                <td>{{ ++$key }}</td>
                                <td>{{$setadprice_request->code}}</td>
                                <td>{{ $setadprice_request->user->name . ' ' . $setadprice_request->user->family }}</td>
                                <td>
                                    @if($setadprice_request->status == 'accepted')
                                        <span
                                            class="badge badge-success">{{ \App\Models\SetadPriceRequest::STATUS['accepted'] }}</span>
                                    @elseif($setadprice_request->status == 'rejected')
                                        <span
                                            class="badge badge-danger">{{ \App\Models\SetadPriceRequest::STATUS['rejected'] }}</span>
                                    @elseif($setadprice_request->status == 'winner')
                                        <span
                                            class="badge badge-success">{{ \App\Models\SetadPriceRequest::STATUS['winner'] }}</span>
                                    @elseif($setadprice_request->status == 'lose')
                                        <span
                                            class="badge badge-danger">{{ \App\Models\SetadPriceRequest::STATUS['lose'] }}</span>
                                    @else
                                        <span
                                            class="badge badge-warning">{{ \App\Models\SetadPriceRequest::STATUS['pending'] }}</span>
                                    @endif
                                </td>
                                <td>{{ verta($setadprice_request->created_at)->format('H:i - Y/m/d') }}</td>
                                <td>
                                    {{$setadprice_request->date . ' ' . $setadprice_request->hour}}
                                </td>
                                <td>
                                    @if($setadprice_request->status == 'winner')
                                        برنده
                                    @elseif($setadprice_request->status == 'lose')
                                        برنده نشدیم
                                    @elseif(in_array($setadprice_request->status, ['pending','accepted']))
                                        @if($daysLeft<0)
                                            {{$daysLeft}} روز گذشته
                                        @else
                                            {{$daysLeft}} روز
                                        @endif

                                    @else
                                        نامشخص
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($setadprice_request->status, ['accepted', 'rejected','winner','lose']))
                                        {{$setadprice_request->acceptor->name . ' ' . $setadprice_request->acceptor->family}}
                                    @elseif($setadprice_request->status == 'pending')
                                        منتظر تایید
                                    @else
                                        نامشخص
                                    @endif
                                </td>
                                <td>
                                    @if($setadprice_request->status == 'accepted')
                                        @can('Organ')
                                            <button class="btn btn-primary btn-floating btn-action-result"
                                                    data-id="{{ $setadprice_request->id }}">
                                                <i class="fa fa-atom"></i>
                                            </button>
                                        @else
                                            منتظر نتیجه
                                        @endcan
                                    @elseif(in_array($setadprice_request->status,['pending','rejected']))
                                        منتظر تایید
                                    @elseif($setadprice_request->status == 'winner')
                                        <span
                                            class="badge badge-success">{{ \App\Models\SetadPriceRequest::STATUS['winner'] }}</span>
                                    @elseif($setadprice_request->status == 'lose')
                                        <span
                                            class="badge badge-warning">{{ \App\Models\SetadPriceRequest::STATUS['lose'] }}</span>
                                    @else
                                        نامشخص
                                    @endif
                                </td>
                                @canany(['ceo','admin'])
                                    <td>
                                        <a class="btn btn-primary btn-floating @if(in_array($setadprice_request->status, ['accepted', 'rejected'])) disabled @endif"
                                           @if(in_array($setadprice_request->status, ['accepted', 'rejected'])) disabled
                                           @endif
                                           href="{{ route('setad_price_requests.action', $setadprice_request->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @else
                                    <td>
                                        <a class="btn btn-info btn-floating"
                                           href="{{ route('setad_price_requests.show', $setadprice_request->id) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>

                                    <td>
                                        <a class="btn btn-warning btn-floating @if($setadprice_request->status != 'pending') disabled @endif"
                                           @if($setadprice_request->status != 'pending') disabled @endif
                                           href="@if($setadprice_request->status == 'pending') {{ route('setad_price_requests.edit', $setadprice_request->id) }} @else # @endif">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>

                                @endcanany
                                @can('setad-price-requests-delete')
                                    <td>
                                        <button
                                            class="btn btn-danger btn-floating trashRow @if(auth()->id() != $setadprice_request->user->id) disabled @endif "
                                            data-url="{{ route('setad_price_requests.destroy',$setadprice_request->id) }}"
                                            data-id="{{ $setadprice_request->id }}"
                                            @if(auth()->id() != $setadprice_request->user->id) disabled @endif>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div
                    class="d-flex justify-content-center">{{ $setadprice_requests->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '.btn-action-result', function () {
            const rowId = $(this).data('id'); // دریافت ID ردیف
            $('#rowId').val(rowId); // مقداردهی به فیلد مخفی
            $('#actionResultModal').modal('show'); // نمایش Modal
        });
        {{--$('#saveActionResult').on('click', function () {--}}
        {{--    const data = $('#actionResultForm').serialize(); // سریال‌سازی اطلاعات فرم--}}
        {{--    console.log(data);--}}
        {{--    $.ajax({--}}
        {{--        url: "{{ url('/setad_price_requests/actionResult') }}",--}}
        {{--        type: 'POST',--}}
        {{--        data: data,--}}
        {{--        success: function (response) {--}}
        {{--            $('#actionResultModal').modal('hide'); // بستن Modal--}}
        {{--            alert('نتیجه با موفقیت ذخیره شد.');--}}
        {{--            // رفرش یا به‌روزرسانی جدول--}}
        {{--            location.reload();--}}
        {{--        },--}}
        {{--        error: function (xhr, status, error) {--}}
        {{--            console.error('Error:', error);--}}
        {{--            alert('خطایی رخ داد. لطفاً دوباره تلاش کنید.');--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

    </script>
@endsection

