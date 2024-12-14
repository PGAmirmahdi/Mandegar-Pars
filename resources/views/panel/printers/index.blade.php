@extends('panel.layouts.master')
@section('title', 'پرینتر ها')
@section('content')
    {{--  cartridges Modal  --}}
    <div class="modal fade" id="cartridgesModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartridgesModalLabel">مشاهده کارتریج های سازگار</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body" style="direction: ltr; text-align: left; line-height: 2rem">
                    <ul></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
    {{--  end cartridges Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>پرینتر ها</h6>
                @can('printers-create')
                    <a href="{{ route('printers.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد پرینتر
                    </a>
                @endcan
            </div>
            <form action="{{ route('printers.search') }}" method="get" id="search_form"></form>
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="name" form="search_form" class="form-control" placeholder="نام پرینتر" value="{{ request()->name ?? null }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="brand" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="1">
                        <option value="all">برند (همه)</option>
                        @foreach(\App\Models\Printer::BRANDS as $value)
                            <option value="{{ $value }}" {{ request()->brand == $value ? 'selected' : '' }}>{{ $value }}</option>
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
                        <th>نام پرینتر</th>
                        <th>برند</th>
                        <th>تاریخ ایجاد</th>
                        <th>کارتریج ها</th>
                        @can('printers-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('printers-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($printers as $key => $printer)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $printer->name }}</td>
                            <td>{{ $printer->brand }}</td>
                            <td>{{ verta($printer->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <button class="btn btn-info btn-floating btn_show" data-id="{{ $printer->id }}" data-toggle="modal" data-target="#cartridgesModal">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                            @can('printers-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('printers.edit', $printer->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('printers-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('printers.destroy',$printer->id) }}" data-id="{{ $printer->id }}">
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
            <div class="d-flex justify-content-center">{{ $printers->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('.btn_show').on('click', function () {
                $('#cartridgesModal .modal-body ul').html('<div class="spinner-grow text-primary"></div>')

                let id = $(this).data('id')
                $.ajax({
                    url: '/api/get-cartridges/' + id,
                    type: 'get',
                    success: function (res) {
                        $('#cartridgesModal .modal-body ul').html('')

                        $.each(res, function (i, item){
                            $('#cartridgesModal .modal-body ul').append(`<li>${item}</li>`);
                        })
                    }
                })
            })
        })
    </script>
@endsection

