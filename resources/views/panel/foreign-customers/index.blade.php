@extends('panel.layouts.master')
@section('title', 'مشتریان خارجی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مشتریان خارجی</h6>
                <div>
                    <form action="{{ route('foreign-customers.excel') }}" method="post" id="excel_form">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>

                    @can('foreign-customers-create')
                        <a href="{{ route('foreign-customers.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ثبت مشتری
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('foreign-customers.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="country" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">کشور (همه)</option>
                        @foreach(\App\Models\Country::pluck('fa_name') as $country)
                            <option value="{{ $country }}" {{ request()->country == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="status" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="2">
                        <option value="all">وضعیت (همه)</option>
                        @foreach(\App\Models\ForeignCustomer::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>وبسایت</th>
                        <th>شماره واتساپ</th>
                        <th>ایمیل</th>
                        <th>کشور</th>
                        <th>وضعیت</th>
                        <th>پیوست</th>
                        <th>تاریخ ایجاد</th>
                        @can('foreign-customers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('foreign-customers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $key => $customer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                @if($customer->website)
                                    <a href="{{ $customer->website }}" class="btn btn-link" target="_blank">{{ $customer->website }}</a>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                @if($customer->phone)
                                    <a href="https://wa.me/{{ $customer->phone }}" class="btn btn-link" target="_blank">{{ $customer->phone }}</a>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                @if($customer->email)
                                    <a href="mailto:{{ $customer->email }}" class="btn btn-link">{{ $customer->email }}</a>
                                @else
                                    ---
                                @endif
                            </td>
                            <td>{{ $customer->country ?? '---' }}</td>
                            <td>
                                <span class="badge badge-{{ \App\Models\ForeignCustomer::STATUS_COLOR[$customer->status] }}">{{ \App\Models\ForeignCustomer::STATUS[$customer->status] }}</span>
                            </td>
                            <td>
                                @if($customer->docs)
                                    <span class="badge badge-success">دارد</span>
                                @else
                                    <span class="badge badge-warning">ندارد</span>
                                @endif
                            </td>
                            <td>{{ verta($customer->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('foreign-customers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('foreign-customers.edit', ['foreign_customer' => $customer->id, 'page' => request()->page]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('foreign-customers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('foreign-customers.destroy',$customer->id) }}" data-id="{{ $customer->id }}">
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
            <div class="d-flex justify-content-center">{{ $customers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
