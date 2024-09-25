@extends('panel.layouts.master')
@section('title', 'مشاهده درخواست ثبت چک')
@section('styles')
    <style>
        table tbody tr td input{
            text-align: center;
            width: fit-content !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>مشاهده درخواست ثبت چک</h6>
            </div>
            <div class="form-row">
                <div class="col-12 mb-3">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="bg-primary">
                        <tr>
                            <th>عنوان درخواست ثبت چک</th>
                            <th>شناسه صیادی</th>
                            <th>وضعیت چک</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(json_decode($cheque->items) as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->code }}</td>
                                <td>@if(isset($item->stats))
                                        {{$item->stats}}
                                    @else
                                        ثبت نشده
                                    @endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
