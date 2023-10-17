@extends('panel.layouts.master')
@section('title', 'مشتریان')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشتریان</h6>
                @can('customers-create')
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد مشتری
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام حقیقی/حقوقی</th>
                        <th>نوع</th>
                        <th>شماره تماس 1</th>
                        <th>تاریخ ایجاد</th>
                        @can('customers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('customers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ \App\Models\Customer::TYPE[$customer->type] }}</td>
                            <td>{{ $customer->phone1 }}</td>
                            <td>{{ verta($customer->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('customers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('customers.edit', $customer->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('customers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('customers.destroy',$customer->id) }}" data-id="{{ $customer->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $customers->links() }}</div>
        </div>
    </div>
@endsection


