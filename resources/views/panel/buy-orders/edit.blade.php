@extends('panel.layouts.master')
@section('title', 'ویرایش سفارش خرید')
@section('styles')
    <style>
        table tbody tr td input{
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title mb-4">
                <h6>ویرایش سفارش خرید</h6>
            </div>
            <form action="{{ route('buy-orders.update', $buyOrder->id) }}" method="post">
                @csrf
                @method('put')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                <label for="customer_id">مشتری <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="js-example-basic-single select2-hidden-accessible">
                                    <option value="" selected>انتخاب کنید...</option>
                                    @foreach(\App\Models\Customer::all(['id','name', 'code']) as $customer)
                                        <option value="{{ $customer->id }}" {{ $customer->id == $buyOrder->customer_id ? 'selected' : '' }}>{{ $customer->code.' - '.$customer->name }}</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success mb-3" id="btn_add">
                                <i class="fa fa-plus mr-2"></i>
                                افزودن کالا
                            </button>
                        </div>
                        @error('products')
                        <h6 class="text-danger text-center d-block">{{ $message }}</h6>
                        @enderror
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>تعداد</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($buyOrder->items)
                                @foreach(json_decode($buyOrder->items) as $item)
                                    <tr>
                                        <td><input type="text" class="form-control" name="products[]" value="{{ $item->product }}" required></td>
                                        <td><input type="number" class="form-control" name="counts[]" min="1" value="{{ $item->count }}" required></td>
                                        <td><button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><input type="text" class="form-control" name="products[]" placeholder="HP 05A" required></td>
                                    <td><input type="number" class="form-control" name="counts[]" min="1" value="1" required></td>
                                    <td><button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" id="description" class="form-control" rows="5">{{ $buyOrder->description }}</textarea>
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
                        <td><input type="text" class="form-control" name="products[]" required></td>
                        <td><input type="number" class="form-control" name="counts[]" min="1" value="1" required></td>
                        <td><button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `)
            })

            // remove item
            $(document).on('click', '.btn_remove', function () {
                $(this).parent().parent().remove()
            })
        })
    </script>
@endsection

