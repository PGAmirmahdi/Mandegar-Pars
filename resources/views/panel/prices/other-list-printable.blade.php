@extends('panel.layouts.master')
@section('title','لیست قیمت ها')
@php
    $sellers = \Illuminate\Support\Facades\DB::table('price_list_sellers')->get();
@endphp
@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت ها - (ریال)</h3>
            <div style="overflow-x: auto" class="tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th colspan="{{ \Illuminate\Support\Facades\DB::table('price_list_sellers')->count() }}">تامین کننده</th>
                    </tr>
                    <tr>
                        <th>
                            <div style="display: block ruby">
                                <span>برند/مدل</span>
                            </div>
                        </th>
                        @foreach($sellers as $seller)
                            <th class="seller">
                                <span>{{ $seller->name }}</span>
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\Illuminate\Support\Facades\DB::table('price_list_models')->get() as $model)
                        <tr>
                            <th style="display: block ruby">
                                <span>{{ $model->name }}</span>
                            </th>
                            @for($i = 0; $i < \Illuminate\Support\Facades\DB::table('price_list_sellers')->count(); $i++)
                                @php $item = \Illuminate\Support\Facades\DB::table('price_list')->where(['model_id' => $model->id, 'seller_id' => $sellers[$i]->id])->first() @endphp
                                <td>{{ $item ? number_format($item->price) : '-' }}</td>
                            @endfor
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
