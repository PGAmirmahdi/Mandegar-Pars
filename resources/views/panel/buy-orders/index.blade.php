@extends('panel.layouts.master')
@section('title', 'سفارشات خرید')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>سفارشات خرید</h6>
                    @can('buy-orders-create')
                        <a href="{{ route('buy-orders.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ثبت سفارش خرید
                        </a>
                    @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        @canany(['admin','ceo','sales-manager'])
                            <th>همکار</th>
                        @endcanany
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        <th>مشاهده</th>
                        <th>چت درباره سفارش</th>
                            @can('buy-orders-edit')
                                <th>ویرایش</th>
                            @endcan
                            @can('buy-orders-delete')
                                <th>حذف</th>
                            @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $key => $order)
                        <tr>
                            <td>{{ ++$key }}</td>
                            @canany(['admin','ceo','sales-manager'])
                                <td>{{ $order->user->fullName() }}</td>
                            @endcanany
                            <td>
                                @if($order->status == 'bought')
                                    <span
                                        class="badge badge-success">{{ \App\Models\BuyOrder::STATUS['bought'] }}</span>
                                @else
                                    <span
                                        class="badge badge-warning">{{ \App\Models\BuyOrder::STATUS['order'] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($order->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('buy-orders.show', $order->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-info btn-floating"
                                   href="{{ route('buy-orders.comments.show', $order->id) }}">
                                    <i class="fa fa-comments"></i>
                                </a>
                            </td>
                                @can('buy-orders-edit')
                                    <td>
                                        <a class="btn btn-warning btn-floating {{ $order->status == 'bought' ? 'disabled' : '' }}"
                                           href="{{ route('buy-orders.edit', $order->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('buy-orders-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow"
                                                data-url="{{ route('buy-orders.destroy',$order->id) }}"
                                                data-id="{{ $order->id }}" {{ $order->status == 'bought' ? 'disabled' : '' }}>
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
            <div class="d-flex justify-content-center">{{ $orders->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

