@extends('panel.layouts.master')
@section('title', 'بهای تمام شده')

@section('content')
    <style>
        select[name=product]{
            width: 200px !important;
        }
    </style>
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">بهای تمام شده</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                            <!-- فرم جستجو و دکمه‌های اکسل/ایجاد -->
                            <div class="card-title d-flex justify-content-between align-items-center">
                                <!-- فرم جستجو -->
                                <div class="col-3">
                                    <form action="{{ route('costs.search') }}" method="get" class="d-flex" id="search_form">
                                        <select name="product" form="search_form"
                                                class="js-example-basic-single select2-hidden-accessible"
                                                data-select2-id="0">
                                            <option value="all">نام کالا(همه)</option>
                                            @foreach(\App\Models\Product::all() as $product)
                                                <option
                                                    value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>{{ $product->title }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary ms-2">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <!-- دکمه‌های دریافت اکسل و ایجاد بهای تمام شده -->
                                <div>
                                    <form action="{{ route('costs.excel') }}" method="post" id="excel_form">
                                        @csrf
                                    </form>
                                    <button class="btn btn-success" form="excel_form">
                                        <i class="fa fa-file-excel mr-2"></i>
                                        دریافت اکسل
                                    </button>

                                    @can('costs-create')
                                        <a href="{{ route('costs.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus mr-2"></i>
                                            ایجاد بهای تمام شده
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <!-- پایان فرم جستجو و دکمه‌ها -->

                            <div class="overflow-auto">
                                <table class="table table-striped table-bordered dataTable dtr-inline text-center" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>نام کالا</th>
                                        <th>تعداد</th>
                                        <th>قیمت بدون مالیات و ارزش افزوده</th>
                                        <th>هزینه حمل و نقل</th>
                                        <th>سایر هزینه ها</th>
                                        <th>بهای تمام شده</th>
                                        @canany(['accountant', 'sales-manager','admin','ceo'])
                                            <th>همکار</th>
                                        @endcanany
                                        <th>تاریخ ایجاد</th>
                                        @can('costs-edit')
                                            <th>ویرایش</th>
                                        @endcan
                                        @can('costs-delete')
                                            <th>حذف</th>
                                        @endcan
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($costs as $key => $cost)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $cost->product->title }}</td>
                                            <td>{{ $cost->count }}</td>
                                            <td>{{ number_format($cost->price) }} ریال</td>
                                            <td>{{ number_format($cost->Logistic_price) }} ریال</td>
                                            <td>{{ number_format($cost->other_price) }} ریال</td>
                                            <td>{{ number_format($cost->final_price) }} ریال</td>
                                            @canany(['accountant', 'sales-manager','admin','ceo'])
                                                <td>{{ $cost->user->fullName() }}</td>
                                            @endcanany
                                            <td>{{ verta($cost->created_at)->format('H:i - Y/m/d') }}</td>
                                            @can('sales-manager')
                                                @can('costs-edit')
                                                    <td>
                                                        <a class="btn btn-warning btn-floating" href="{{ route('costs.edit', $cost->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcan
                                                @can('costs-delete')
                                                    <td>
                                                        <button class="btn btn-danger btn-floating trashRow"
                                                                data-url="{{ route('costs.destroy', $cost->id) }}"
                                                                data-id="{{ $cost->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                @endcan
                                            @else
                                                @can('costs-edit')
                                                    <td>
                                                        <a class="btn btn-warning btn-floating" href="{{ route('costs.edit', $cost->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @endcan
                                                @can('costs-delete')
                                                    <td>
                                                        <button class="btn btn-danger btn-floating trashRow"
                                                                data-url="{{ route('costs.destroy', $cost->id) }}"
                                                                data-id="{{ $cost->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                @endcan
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
                            <div class="d-flex justify-content-center">
                                {{ $costs->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal (در صورت نیاز به استفاده از آن) -->
    <div class="modal fade" id="timelineModal" tabindex="-1" aria-labelledby="timelineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="timelineModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <!-- تایم‌لاین عمودی -->
                    <div class="d-flex flex-column position-relative">
                        <!-- مرحله 1 (متن در چپ) -->
                        <div class="timeline-content" style="display: none;">
                        </div>
                        <div class="loading">
                            <div class="lds-roller">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection
