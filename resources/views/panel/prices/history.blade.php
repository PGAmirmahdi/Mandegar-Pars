@extends('panel.layouts.master')
@section('title', 'تاریخچه قیمت ها')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>تاریخچه قیمت ها</h6>
            </div>
            <form action="" method="post" id="search_form">
                @csrf
            </form>
            <div class="row mb-3">
                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                    <input type="text" name="title" class="form-control" placeholder="عنوان محصول" value="{{ request()->title ?? null }}" form="search_form">
                </div>
                <div class="col-xl-3 xl-lg-3 col-md-4 col-sm-12">
                    <button type="submit" class="btn btn-primary" form="search_form">جستجو</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>فیلد قیمت</th>
                        <th>قیمت قبلی</th>
                        <th>قیمت تغییر داده شده</th>
                        <th>تاریخ ویرایش</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pricesHistory as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->product->title }}</td>
                            <td>{{ \App\Models\PriceHistory::FIELDS[$item->price_field] }}</td>
                            <td>{{ number_format($item->price_amount_from) }}</td>
                            <td>{{ number_format($item->price_amount_to) }}</td>
                            <td>{{ verta($item->created_at)->format('Y/m/d') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-center">{{ $pricesHistory->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/lazysizes.min.js') }}"></script>
@endsection
