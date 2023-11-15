@extends('panel.layouts.master')
@switch(request()->website)
    @case('torob')
            @php
                $title = 'محصولات ترب';
            @endphp
        @break
    @case('digikala')
            @php
                $title = 'محصولات دیجیکالا';
            @endphp
        @break
@endswitch

@section('title', $title)
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>{{ $title }}</h6>
                <a href="{{ route('off-site-products.create', request()->website) }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-2"></i>
                    ایجاد محصول
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>تاریخ ایجاد</th>
                        <th>مشاهده قیمت</th>
                        <th>ویرایش</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('off-site-products.show', $item->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-warning btn-floating" href="{{ route('off-site-products.edit', $item->id) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('off-site-products.destroy',$item->id) }}" data-id="{{ $item->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
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
