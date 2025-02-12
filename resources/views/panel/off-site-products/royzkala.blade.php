@extends('panel.layouts.master')
@section('title', 'وبسایت رویزکالا')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وبسایت رویزکالا</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نوع کارتریج</th>
                        <th>قیمت <span class="text-success">(30،000+)</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ urldecode($item->attributes->attribute_pa_cartridge14) }}</td>
                            <td>{{ number_format($item->display_price) }} تومان</td>
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


