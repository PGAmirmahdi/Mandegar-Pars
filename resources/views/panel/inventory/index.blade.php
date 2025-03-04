@extends('panel.layouts.master')
@section('title', 'انبار')
@section('content')
    {{--  Move Modal  --}}
    <div class="modal fade" id="moveModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveModalLabel">جابجایی کالا</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_warehouse_id">انتقال به انبار<span class="text-danger">*</span></label>
                        <select class="form-control" name="new_warehouse_id" id="new_warehouse_id" required form="move_form">
                            @foreach(\App\Models\Warehouse::where('id','!=',$warehouse_id)->get() as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="count">تعداد<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="count" id="count" min="1" value="1" required form="move_form">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" form="move_form">انتقال</button>
                    <form action="{{ route('inventory.move') }}" method="post" id="move_form">
                        @csrf
                        <input type="hidden" name="inventory_id" value="" id="inventory_id">
                        <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--  End Move Modal  --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>انبار</h6>
                <div>
                    <form action="{{ route('inventory.excel') }}" method="post" id="excel_form">
                        <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                        @csrf
                    </form>

                    <button class="btn btn-success" form="excel_form">
                        <i class="fa fa-file-excel mr-2"></i>
                        دریافت اکسل
                    </button>
                    @can('inventory-create')
                        <a href="{{ route('inventory.create', ['warehouse_id' => $warehouse_id]) }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                             افزودن کالا از لیست تمامی کالاها
                        </a>
                    @endcan
                </div>
            </div>
            <form action="{{ route('inventory.search') }}" method="get" id="search_form">
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
            </form>
            <div class="row mb-3">
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <input type="text" name="code" class="form-control" placeholder="کد کالا" value="{{ request()->code ?? null }}" form="search_form">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="category" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all">شرح کالا (همه)</option>
                        @foreach(\App\Models\Category::all(['id','name']) as $category)
                            <option value="{{ $category->id }}" {{ request()->category == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="model" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="2">
                        <option value="all">برند (همه)</option>
                        @foreach(\App\Models\ProductModel::all(['id','name']) as $model)
                            <option value="{{ $model->id }}" {{ request()->model ==  $model->id ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select name="product" form="search_form" class="js-example-basic-single select2-hidden-accessible" data-select2-id="3">
                        <option value="all">مدل کالا (همه)</option>
                        @foreach(\App\Models\Product::all(['id','title']) as $product)
                            <option value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 xl-lg-2 col-md-3 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <td>دسته بندی کالا</td>
                        <td>برند کالا</td>
                        <th>عنوان کالا</th>
                        <th>کد کالا</th>
                        <th>موجودی اولیه</th>
                        <th>موجودی فعلی</th>
                        <th>تعداد ورود</th>
                        <th>تعداد خروج</th>
                        <th>تاریخ ایجاد</th>
                        @can('inventory-edit')
                            <th>جابجایی</th>
                            <th>ویرایش</th>
                        @endcan
                        @can('inventory-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->product->category->name }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $item->product->productModels->slug }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $item->product->title }}</td>
                            <td style="font-family: 'Segoe UI Semibold';font-weight: bold">{{ $item->product->code }}</td>
                            <td>{{ number_format($item->initial_count) }}</td>
                            <td>{{ number_format($item->current_count) }}</td>
                            <td>{{ number_format($item->getInputCount()) }}</td>
                            <td>{{ number_format($item->getOutputCount()) }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('inventory-edit')
                                <td>
                                    <a class="btn btn-primary btn-floating btn_move" href="#moveModal" data-toggle="modal" data-id="{{ $item->id }}">
                                        <i class="fa fa-arrow-right-arrow-left"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('inventory.edit',[ $item->id,'warehouse_id' => $warehouse_id]) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('inventory-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('inventory.destroy',$item->id) }}" data-id="{{ $item->id }}">
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
            <div class="d-flex justify-content-center">{{ $data->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function (){
            $('.btn_move').on('click', function () {
                var inventory_id = $(this).data('id');
                $('#inventory_id').val(inventory_id)
            })
        })
    </script>
@endsection
