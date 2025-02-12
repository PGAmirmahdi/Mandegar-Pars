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
                        <th>خصوصیات</th>
                        <th>قیمت اصلی</th>
                        <th>قیمت <span class="text-success">(30،000+)</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                @foreach($item->attributes as $attribute)
                                    {{ urldecode($attribute).' ،' }}
                                @endforeach
                            </td>
                            <td>{{ number_format($item->display_price) }} تومان</td>
                            <td>{{ number_format($item->display_price + 30000) }} تومان</td>
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


