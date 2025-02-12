@extends('panel.layouts.master')
@section('title', 'وبسایت آریا پرینت')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وبسایت آریا پرینت</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>قیمت</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data['offers'] as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $data['name'] }}</td>
                            <td>{{ number_format($item->price * 0.1) }} تومان</td>
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


