@extends('panel.layouts.master')
@section('title', 'وبسایت دیجی کالا')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وبسایت دیجی کالا</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>فروشگاه</th>
                            <th>قیمت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->variants as $key => $item)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td><a href="{{ $item->seller->url }}" class="btn-link" target="_blank">{{ $item->seller->title }}</a></td>
                                <td>{{ number_format($item->price->selling_price * 0.1) }} تومان</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection


