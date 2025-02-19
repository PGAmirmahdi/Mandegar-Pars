@extends('panel.layouts.master')
@section('title', 'ثبت نام های سایت')
@php
    use Hekmatinasser\Verta\Verta;
@endphp
@section('styles')

@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">آخرین ثبت نام ها</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead>
                                <tr class="text-muted small">
                                    <th class="text-center">نام/شماره تلفن</th>
                                    <th class="text-center">تاریخ</th>
                                </tr>
                                </thead>
                                <tbody class="small">
                                @foreach ($data['customers']['customers_details'] as $customer2)
                                    <tr>
                                        <td class="text-center">{{ $customer2['name'] }}</td>
                                        <td class="text-center">{{ verta($customer2['registration_date'])->format('Y/m/d H:i') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
