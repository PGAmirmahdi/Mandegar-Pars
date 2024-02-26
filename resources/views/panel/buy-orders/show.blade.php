@extends('panel.layouts.master')
@section('title', 'مشاهده سفارش خرید')
@section('styles')
    <style>
        table tbody tr td input{
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between mb-4">
                <h6>مشاهده سفارش خرید</h6>
                <div>
                    @can('ceo')
                        @if($buyOrder->status == 'bought')
                            <form action="{{ route('buy-orders.changeStatus', $buyOrder->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-success">{{ \App\Models\BuyOrder::STATUS['bought'] }}</button>
                            </form>
                        @else
                            <form action="{{ route('buy-orders.changeStatus', $buyOrder->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-warning">{{ \App\Models\BuyOrder::STATUS['order'] }}</button>
                            </form>
                        @endif
                    @else
                        @if($buyOrder->status == 'bought')
                            <span class="badge badge-success">{{ \App\Models\BuyOrder::STATUS['bought'] }}</span>
                        @else
                            <span class="badge badge-warning">{{ \App\Models\BuyOrder::STATUS['order'] }}</span>
                        @endif
                    @endcan
                </div>
            </div>
            <div class="form-row">
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                            <label for="customer_id">مشتری</label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-single select2-hidden-accessible" disabled>
                                <option value="{{ $buyOrder->customer->id }}" selected>{{ $buyOrder->customer->code.' - '.$buyOrder->customer->name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="bg-primary">
                        <tr>
                            <th>عنوان کالا</th>
                            <th>تعداد</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach(json_decode($buyOrder->items) as $item)
                                <tr>
                                    <td>{{ $item->product }}</td>
                                    <td>{{ number_format($item->count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr></tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                    <label for="description">توضیحات</label>
                    <textarea name="description" id="description" class="form-control" rows="5" disabled>{{ $buyOrder->description }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endsection

