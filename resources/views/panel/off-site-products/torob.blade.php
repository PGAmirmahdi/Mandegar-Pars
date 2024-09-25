@extends('panel.layouts.master')
@section('title', 'وبسایت ترب')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وبسایت ترب</h6>
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
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td><a href="{{ $item->page_url }}" class="btn-link" target="_blank">{{ $item->shop_name }}</a></td>
                            <td>{{ number_format($item->price) }} تومان</td>
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


