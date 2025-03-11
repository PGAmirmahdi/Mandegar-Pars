@extends('panel.layouts.master')
@section('title', 'ثبت درخواست مدارک')

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
                <button type="button" class="btn btn-success" id="btn_add">
                    <i class="fa fa-plus mr-2"></i>
                    افزودن مدرک
                </button>
            </div>
            <form action="{{ route('document_request.store') }}" method="post">
                @csrf
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                    <label for="title">عنوان کلی<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title"
                           value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-row">
                    <div class="col-12 mb-3 table-responsive">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان مدرک</th>
                                <th>حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" name="document_title[]"
                                           placeholder="عنوان مدرک">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-floating btn_remove">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="description">توضیحات</label>
                        <textarea name="description" class="form-control" id="description"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
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
            // اضافه کردن سطر جدید برای مدرک
            $('#btn_add').click(function () {
                $('table tbody').append(`
                <tr>
                    <td>
                        <input type="text" class="form-control" name="document_title[]" placeholder="عنوان مدرک">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-floating btn_remove">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            });

            // حذف سطر مدرک
            $(document).on('click', '.btn_remove', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
