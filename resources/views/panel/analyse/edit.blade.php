@extends('panel.layouts.master')
@section('title', 'ویرایش آنالیز')
@section('content')
    <div class="card">
        <div class="card-title d-flex justify-content-between align-items-center m-3">
            <h6>ویرایش آنالیز</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('analyse.update', $analyse->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="date">تاریخ شروع</label>
                    <input
                        type="text"
                        id="date"
                        name="date"
                        class="form-control readonly"
                        value="{{ $analyse->date }}"
                        autocomplete="off"
                        required
                        readonly
                    >
                </div>
                <div class="form-group">
                    <label for="to_date">تاریخ پایان</label>
                    <input
                        type="text"
                        id="to_date"
                        name="to_date"
                        class="form-control readonly"
                        value="{{ $analyse->to_date }}"
                        autocomplete="off"
                        required
                        readonly
                    >
                </div>
                <div class="form-group">
                    <label for="category">دسته‌بندی</label>
                    <select
                        id="category"
                        name="category_id"
                        class="form-control js-example-basic-single select2-hidden-accessible"
                        data-select2-id="1"
                        required
                        disabled
                    >
                        <option value="">انتخاب کنید</option>
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ $category->id == $analyse->category_id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="category_id" value="{{ $analyse->category_id }}">
                </div>
                <div class="form-group">
                    <label for="brand">برند</label>
                    <select
                        id="brand"
                        name="brand_id"
                        class="form-control js-example-basic-single select2-hidden-accessible"
                        data-select2-id="2"
                        required
                        disabled
                    >
                        <option value="">انتخاب کنید</option>
                        @foreach($brands as $brand)
                            <option
                                value="{{ $brand->id }}"
                                {{ $brand->id == $analyse->brand_id ? 'selected' : '' }}
                            >
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="brand_id" value="{{ $analyse->brand_id }}">
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>تعداد</th>
                        <th>موجودی انبار</th>
                        <th>موجودی لحظه ای</th>
                    </tr>
                    </thead>
                    <tbody id="products-table-body">
                    @foreach($analyse->products as $product)
                        <tr>
                            <td>{{ $product->title }}</td>
                            <td>
                                <input
                                    type="number"
                                    name="products[{{ $product->id }}][quantity]"
                                    min="0"
                                    class="form-control"
                                    value="{{ $product->pivot->quantity }}"
                                >
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="products[{{ $product->id }}][storage_count]"
                                    min="0"
                                    class="form-control"
                                    value="{{ $product->storage_count }}"
                                >
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="products[{{ $product->id }}][total_count]"
                                    min="0"
                                    class="form-control"
                                    value="{{ $product->total_count }}"
                                >
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success" id="submit_button">ثبت تغییرات</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);
                button.prop('disabled', true).text('در حال ارسال...');
                button.closest('form').submit();
            });
        });
        $(document).ready(function () {
            $('#category, #brand').change(function () {
                var category_id = $('#category').val();
                var brand_id = $('#brand').val();

                if (category_id && brand_id) {
                    $.ajax({
                        url: '{{ route('get.products') }}',
                        type: 'GET',
                        data: {
                            category_id: category_id,
                            brand_id: brand_id
                        },
                        success: function (data) {
                            $('#products-table-body').empty();
                            $.each(data.products, function (index, product) {
                                $('#products-table-body').append(
                                    '<tr>' +
                                    '<td>' + product.title + '</td>' +
                                    '<td><input type="number" name="products[' + product.id + '][quantity]" min="0" class="form-control" value="' + (product.quantity || 0) + '"></td>' +
                                    '<td><input type="number" name="products[' + product.id + '][storage_count]" min="0" class="form-control" value="' + (product.storage_count || 0) + '"></td>' +
                                    '</tr>'
                                );
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                } else {
                    $('#products-table-body').empty();
                }
            });
        });
    </script>
@endsection
