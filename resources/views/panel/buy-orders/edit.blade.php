@extends('panel.layouts.master')
@section('title', 'ویرایش سفارش خرید')
@section('styles')
    <style>
        table tbody tr td input {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>ویرایش سفارش خرید</h6>
                <button type="button" class="btn btn-success" id="btn_add">
                    <i class="fa fa-plus mr-2"></i>
                    افزودن کالا
                </button>
            </div>
            <form action="{{ route('buy-orders.update', $buyOrder->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>تعداد</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($buyOrder->items) as $item)
                                <tr>
                                    <td>
                                        <select class="js-example-basic-single" name="products[]" required>
                                            <option value="" disabled>انتخاب کنید</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $product->id == $item->product ? 'selected' : '' }}>
                                                    {{ $product->category->name . ' - ' . $product->title . ' - ' . $product->productModels->slug }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control" name="counts[]" min="1" value="{{ $item->count }}" required></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                            <label for="description">توضیحات</label>
                            <textarea name="description" id="description" class="form-control" rows="5">{{ $buyOrder->description }}</textarea>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit">به‌روزرسانی فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // add item
            $(document).on('click', '#btn_add', function () {
                $('table tbody').append(`
                    <tr>
                        <td>
                            <select class="js-example-basic-single" name="products[]" required>
                                <option value="" disabled selected>انتخاب کنید</option>
                                @foreach($products as $item)
                <option value="{{ $item->id }}">
                                        {{ $item->category->name . ' - ' . $item->title . ' - ' . $item->productModels->slug }}
                </option>
@endforeach
                </select>
            </td>
            <td><input type="number" class="form-control" name="counts[]" min="1" value="1" required></td>
            <td><button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button></td>
        </tr>
`);

                // Reinitialize select2 after adding new row
                $('.js-example-basic-single').select2();
            });

            // remove item
            $(document).on('click', '.btn_remove', function () {
                $(this).parent().parent().remove()
            });
        });
    </script>
@endsection
