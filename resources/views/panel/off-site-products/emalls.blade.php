@extends('panel.layouts.master')
@section('title', 'وبسایت ایمالز')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>وبسایت ایمالز</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>فروشگاه</th>
                        <th>قیمت</th>
                        <th>آخرین ویرایش</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->Shoptitle }}</td>
                            <td>{{ number_format($item->Price) }} تومان </td>
                            <td>{{ $item->lastupdate }}</td>
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


