@extends('panel.layouts.master')
@section('title', 'لیست درخواست قیمت')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست درخواست قیمت</h6>
                    @can('price-requests-create')
                        <a href="{{ route('price-requests.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ثبت درخواست قیمت
                        </a>
                    @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>درخواست دهنده</th>
                        <th>حداکثر زمان ثبت قیمت</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        @can('ceo')
                            <th>ثبت قیمت</th>
                        @else
                            <th>مشاهده قیمت</th>
                        @endcan
                        @can('price-requests-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($price_requests as $key => $price_request)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $price_request->user->fullName() }}</td>
                            <td>{{ $price_request->max_send_time }} ساعت </td>
                            <td>
                                @if($price_request->status == 'sent')
                                    <span class="badge badge-success">{{ \App\Models\PriceRequest::STATUS['sent'] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\PriceRequest::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($price_request->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('ceo')
                                <td>
                                    <a class="btn btn-primary btn-floating" href="{{ route('price-requests.edit', $price_request->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('price-requests.show', $price_request->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('price-requests-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('price-requests.destroy',$price_request->id) }}" data-id="{{ $price_request->id }}">
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
            <div class="d-flex justify-content-center">{{ $price_requests->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

