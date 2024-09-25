@extends('panel.layouts.master')
@section('title', 'سفارشات خرید')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>سفارشات خرید</h6>
                @cannot('ceo')
                    @can('buy-orders-create')
                        <a href="{{ route('buy-orders.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ثبت سفارش خرید
                        </a>
                    @endcan
                @endcannot
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>مشتری</th>
                        <th>استان</th>
                        <th>شهر</th>
                        <th>وضعیت</th>
                        @canany(['admin','ceo','sales-manager'])
                            <th>همکار</th>
                        @endcanany
                        <th>زمان ثبت</th>
                        <th>مشاهده</th>
                        @cannot('ceo')
                            @can('buy-orders-edit')
                                <th>ویرایش</th>
                            @endcan
                            @can('buy-orders-delete')
                                <th>حذف</th>
                            @endcan
                        @endcannot
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $key => $order)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->customer->province }}</td>
                            <td>{{ $order->customer->city }}</td>
                            <td>
                                @can('ceo')
                                    @if($order->status == 'bought')
                                        <form action="{{ route('buy-orders.changeStatus', $order->id) }}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-success">{{ \App\Models\BuyOrder::STATUS['bought'] }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('buy-orders.changeStatus', $order->id) }}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">{{ \App\Models\BuyOrder::STATUS['order'] }}</button>
                                        </form>
                                    @endif
                                @else
                                    @if($order->status == 'bought')
                                        <span class="badge badge-success">{{ \App\Models\BuyOrder::STATUS['bought'] }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ \App\Models\BuyOrder::STATUS['order'] }}</span>
                                    @endif
                                @endcan
                            </td>
                            @canany(['admin','ceo','sales-manager'])
                                <td>{{ $order->user->fullName() }}</td>
                            @endcanany
                            <td>{{ verta($order->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('buy-orders.show', $order->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @cannot('ceo')
                                @can('buy-orders-edit')
                                    <td>
                                        <a class="btn btn-warning btn-floating {{ $order->status == 'bought' ? 'disabled' : '' }}" href="{{ route('buy-orders.edit', $order->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                @endcan
                                @can('buy-orders-delete')
                                    <td>
                                        <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('buy-orders.destroy',$order->id) }}" data-id="{{ $order->id }}" {{ $order->status == 'bought' ? 'disabled' : '' }}>
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
            <div class="d-flex justify-content-center">{{ $orders->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

