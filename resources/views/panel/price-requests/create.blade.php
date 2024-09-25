@extends('panel.layouts.master')
@section('title', 'ثبت درخواست قیمت')
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
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>ثبت درخواست قیمت</h6>
                <button type="button" class="btn btn-success" id="btn_add">
                    <i class="fa fa-plus mr-2"></i>
                    افزودن کالا
                </button>
            </div>
            <form action="{{ route('price-requests.store') }}" method="post">
                @csrf
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
                                <tr>
                                    <td><input type="text" class="form-control" name="products[]" placeholder="HP 05A" required></td>
                                    <td><input type="number" class="form-control" name="counts[]" min="1" value="1" required></td>
                                    <td><button type="button" class="btn btn-danger btn-floating btn_remove"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="max_send_time">حداکثر زمان ثبت قیمت (ساعت)<span class="text-danger">*</span></label>
                        <input type="number" name="max_send_time" class="form-control" id="max_send_time" value="{{ old('max_send_time') ?? 1 }}" min="1" max="100" required>
                        @error('max_send_time')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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

