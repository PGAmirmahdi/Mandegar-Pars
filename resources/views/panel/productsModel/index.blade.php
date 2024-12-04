@extends('panel.layouts.master')
@section('title', 'مدل ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>مدل ها</h6>
                @can('products-create')
                    <a href="{{ route('productsModel.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ایجاد مدل
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام مدل</th>
                        <th>اسلاگ</th>
                        <th>دسته بندی</th>
                        <th>تاریخ ایجاد</th>
                        @can('productsModel-edit')
                            <th>ویرایش</th>
                        @endcan
                        @can('productsModel-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($productsModel as $key => $model)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $model->name }}</td>
                            <td>{{ $model->slug }}</td>
                            <td>{{ $model->category->name }}</td> <!-- نمایش دسته‌بندی محصول -->
                            <td>{{ verta($model->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('productsModel-edit')
                                <td>
                                    <a class="btn btn-warning btn-floating" href="{{ route('productsModel.edit', $model->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('productsModel-delete')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('productsModel.destroy',$model->id) }}" data-id="{{ $model->id }}">
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
            <div class="d-flex justify-content-center">{{ $productsModel->links() }}</div>
        </div>
    </div>
@endsection
