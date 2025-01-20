@extends('panel.layouts.master')
@section('title', 'لیست ' . (in_array(auth()->user()->role->name, [
    'setad_sale', 'internet_sale', 'free_sale',
    'industrial_sale', 'global_sale', 'organization_sale'
])
    ? ' درخواست ' . auth()->user()->role->label
    : 'درخواست های فروش'))
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>
                    لیست
                    {{ in_array(auth()->user()->role->name, [
                        'setad_sale', 'internet_sale', 'free_sale',
                        'industrial_sale', 'global_sale', 'organization_sale'
                    ])
                        ? ' درخواست ' . auth()->user()->role->label
                        : 'درخواست های فروش' }}
                </h6>
                @can('sale-price-requests-create')
                    <a href="{{ route('sale_price_requests.create') }}" class="btn btn-primary">
                        <h6>ثبت
                            {{ in_array(auth()->user()->role->name, [
                                'setad_sale', 'internet_sale', 'free_sale',
                                'industrial_sale', 'global_sale', 'organization_sale'
                            ])
                                ? ' درخواست ' . auth()->user()->role->label
                                : 'درخواست های فروش' }}
                        </h6>
                        <i class="fa fa-plus ml-2"></i>
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
                                  action="{{ route('sale_price_requests.actionResult') }}">
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

            <div class="overflow-auto">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شناسه</th>
                        <th>ثبت کننده</th>
                        <th>زمان ثبت</th>
                        <th>مهلت تایید</th>
                        <th>تایید/رد کننده</th>
                        @can('setad_sale')
                            <th>مهلت باقی مانده</th>
                        @endcan
                        <th>وضعیت</th>
                        @canany(['Organ','ceo'])
                            <th>نتیجه نهایی</th>
                        @endcan
                        @canany(['ceo','admin'])
                            <th>ثبت قیمت</th>
                            <th>مشاهده قیمت</th>
                        @else
                            <th>مشاهده قیمت</th>
                            <th>ویرایش</th>
                        @endcanany
                        @can('sale-price-requests-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($saleprice_requests as $key => $saleprice_request )
                        @php
                            $paymentDue = verta($saleprice_request->date); // تاریخ هجری شمسی
                            $paymentDueGregorian = $paymentDue->toCarbon()->format('Y-m-d');
                            $today = verta(now())->format('Y-m-d');
                            // محاسبه تفاوت تاریخ‌ها به روز
                            $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($paymentDueGregorian), false);
                        @endphp
                        <tr class="@if($daysLeft <= 0 && !in_array($saleprice_request->status, ['accepted','winner','finished'])) table-danger @elseif($daysLeft > 0 && $daysLeft <= 2 && !in_array($saleprice_request->status, ['accepted','winner','lose'])) table-warning @elseif($daysLeft > 2) @elseif($saleprice_request->status == 'accepted') table-success @else @endif">
                            <td>{{ ++$key }}</td>
                            <td>{{$saleprice_request->code}}</td>
                            <td>{{ $saleprice_request->user->name . ' ' . $saleprice_request->user->family }}</td>
                            <td>{{ $saleprice_request->customer->name}}</td>
                            <td>{{ verta($saleprice_request->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                {{$saleprice_request->date . ' ' . $saleprice_request->hour}}
                            </td>

                            <td>
                                @if(in_array($saleprice_request->status, ['accepted', 'rejected','winner','lose','finished']))
                                    {{$saleprice_request->acceptor->name . ' ' . $saleprice_request->acceptor->family}}
                                @elseif($saleprice_request->status == 'pending')
                                    <span
                                        class="badge badge-warning">{{ \App\Models\SalePriceRequest::STATUS['pending'] }}</span>
                                @else
                                    نامشخص
                                @endif
                            </td>
                            @can('setad_sale')
                                <td>
                                    @if($saleprice_request->status == 'winner')
                                        <span
                                            class="badge badge-success">{{ \App\Models\SalePriceRequest::STATUS['winner'] }}</span>
                                    @elseif($saleprice_request->status == 'lose')
                                        <span
                                            class="badge badge-danger">{{ \App\Models\SalePriceRequest::STATUS['lose'] }}</span>
                                    @elseif(in_array($saleprice_request->status, ['pending','accepted','finished']))
                                        @if($daysLeft<0)
                                            {{$daysLeft * -1}} روز گذشته
                                        @elseif($daysLeft>0)
                                            {{$daysLeft}} روز
                                        @else
                                            بدون مهلت
                                        @endif
                                    @else
                                        نامشخص
                                    @endif
                                </td>
                            @endcan
                            <td>
                                @if($saleprice_request->status == 'accepted')
                                    <span
                                        class="badge badge-success">{{ \App\Models\SalePriceRequest::STATUS['accepted'] }}</span>
                                @elseif($saleprice_request->status == 'rejected')
                                    <span
                                        class="badge badge-danger">{{ \App\Models\SalePriceRequest::STATUS['rejected'] }}</span>
                                @elseif($saleprice_request->status == 'winner')
                                    <span
                                        class="badge badge-success">{{ \App\Models\SalePriceRequest::STATUS['winner'] }}</span>
                                @elseif($saleprice_request->status == 'lose')
                                    <span
                                        class="badge badge-danger">{{ \App\Models\SalePriceRequest::STATUS['lose'] }}</span>
                                @elseif($saleprice_request->status == 'finished')
                                    <span
                                        class="badge badge-info">{{ \App\Models\SalePriceRequest::STATUS['finished'] }}</span>
                                @else
                                    <span
                                        class="badge badge-warning">{{ \App\Models\SalePriceRequest::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            @canany(['Organ','ceo'])
                                <td>
                                    @if($saleprice_request->status == 'accepted')
                                        @can('Organ')
                                            <button class="btn btn-primary btn-floating btn-action-result"
                                                    data-id="{{ $saleprice_request->id }}">
                                                <i class="fa fa-atom"></i>
                                            </button>
                                        @else
                                            <span
                                                class="badge badge-warning">منتظر نتیجه</span>
                                        @endcan
                                    @elseif(in_array($saleprice_request->status,['pending','rejected']))
                                        <span
                                            class="badge badge-warning">{{ \App\Models\SalePriceRequest::STATUS['pending'] }}</span>
                                    @elseif($saleprice_request->status == 'winner')
                                        <span
                                            class="badge badge-success">{{ \App\Models\SalePriceRequest::STATUS['winner'] }}</span>
                                    @elseif($saleprice_request->status == 'lose')
                                        <span
                                            class="badge badge-warning">{{ \App\Models\SalePriceRequest::STATUS['lose'] }}</span>
                                    @elseif($saleprice_request->status == 'finished')
                                        <span
                                            class="badge badge-info">{{ \App\Models\SalePriceRequest::STATUS['finished'] }}</span>
                                    @else
                                        نامشخص
                                    @endif
                                </td>
                            @endcanany
                            @canany(['ceo','admin'])
                                <td>
                                    <a class="btn btn-primary btn-floating @if(in_array($saleprice_request->status, ['accepted', 'rejected','finished','winner','lose'])) disabled @endif"
                                       @if(in_array($saleprice_request->status, ['accepted', 'rejected','finished','winner','lose'])) disabled
                                       @endif
                                       href="{{ route('sale_price_requests.action', $saleprice_request->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-floating"
                                       href="{{ route('sale_price_requests.show', $saleprice_request->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a class="btn btn-info btn-floating"
                                       href="{{ route('sale_price_requests.show', $saleprice_request->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>

                                <td>
                                    <a class="btn btn-warning btn-floating @if($saleprice_request->status != 'pending') disabled @endif"
                                       @if($saleprice_request->status != 'pending') disabled @endif
                                       href="@if($saleprice_request->status == 'pending') {{ route('sale_price_requests.edit', $saleprice_request->id) }} @else # @endif">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>

                            @endcanany
                            @can('sale-price-requests-delete')
                                <td>
                                    <button
                                        class="btn btn-danger btn-floating trashRow @if(auth()->id() != $saleprice_request->user->id) disabled @endif "
                                        data-url="{{ route('sale_price_requests.destroy',$saleprice_request  ->id) }}"
                                        data-id="{{ $saleprice_request->id }}"
                                        @if(auth()->id() != $saleprice_request->user->id) disabled @endif>
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
                class="d-flex justify-content-center">{{ $saleprice_requests->appends(request()->all())->links() }}</div>
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

