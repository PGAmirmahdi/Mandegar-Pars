@extends('panel.layouts.master')
@section('title', 'ثبت درخواست ستاد')
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
                <h6>ثبت درخواست ستاد</h6>
                <button type="button" class="btn btn-success" id="btn_add">
                    <i class="fa fa-plus mr-2"></i>
                    افزودن کالا
                </button>
            </div>
            <form action="{{ route('setad_price_requests.store') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="col-12 row mb-4">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label class="form-label" for="customer">مشتری حقیقی/حقوقی<span
                                        class="text-danger">*</span></label>
                                <select name="customer" id="customer"
                                        class="js-example-basic-single  select2-hidden-accessible"
                                        data-select2-id="1">
                                    <option value="" disabled selected>انتخاب کنید...</option>
                                    @foreach(\App\Models\Customer::all(['id','name','code']) as $customer)
                                        <option
                                            value="{{ $customer->id }}" {{ old('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('buyer_name')
                                <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="payment_type">نوع پرداختی</label>
                                <select class="form-control" name="payment_type" id="payment_type">
                                    @foreach(\App\Models\Order::Payment_Type as $key => $value)
                                        <option value="{{ $key }}" {{ old('payment_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('payment_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-4">
                                <label for="date">تاریخ<span class="text-danger">*</span></label>
                                <input type="text" name="date" class="form-control date-picker-shamsi-list" id="date" value="{{ old('date') }}">
                                @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-3 mb-4 clock-sec">
                                <label>ساعت<span class="text-danger">*</span></label>
                                <div class="input-group clockpicker-autoclose-demo">
                                    <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-clock-o"></i>
                                </span>
                                    </div>
                                    <input type="text" name="hour" class="form-control text-left" value="{{ old('hour') }}" dir="ltr" required>
                                </div>
                                @error('hour')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>تعداد</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <select class="js-example-basic-single" name="products[]" required>
                                        <option value="" disabled selected>انتخاب کنید</option>
                                        @foreach($products as $item)
                                            <option
                                                value="{{ $item->id }}"
                                                {{ isset($productId) && $item->id == $productId ? 'selected' : '' }}>
                                                {{ $item->category->slug . ' - ' . $item->title . ' - ' . $item->productModels->slug }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" class="form-control" name="counts[]" min="1" value="1"
                                           required></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-floating btn_remove"><i
                                            class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-12 row mb-4">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description"
                                      class="description form-control"
                                      rows="10">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                            @enderror
                            <span class="text-info fst-italic">خط بعد Shift + Enter</span>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit">ثبت فرم</button>
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
                <option value="{{ $item->id }}"
                                        {{ isset($productId) && $item->id == $productId ? 'selected' : '' }}>
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
