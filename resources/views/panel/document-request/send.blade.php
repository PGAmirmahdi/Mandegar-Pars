@extends('panel.layouts.master')
@section('title', 'ارسال مدارک')

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
            <form action="{{ route('document_request.sendAction', $document->id) }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                    <label for="title">عنوان کلی<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control readonly" id="title"
                           value="{{ old('title', $document->title) }}" readonly>
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
                                <th>فایل مدرک</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                // تبدیل فایل‌های ذخیره‌شده به آرایه (در صورت نیاز)
                                $docs = is_array($document->document) ? $document->document : json_decode($document->document, true);
                            @endphp
                            @if($docs && count($docs) > 0)
                                @foreach($docs as $index => $doc)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control readonly" name="document_title[]" placeholder="عنوان مدرک"
                                                   value="{{ old('document_title.' . $index, $doc['document_title']) }}" readonly>
                                        </td>
                                        <td>
                                            <div class="row">
                                                @if(isset($doc['document_file']) && !empty($doc['document_file']))
                                                    <div class="col-9">
                                                        <input type="file" class="form-control" name="document_file[]" placeholder="فایل مدرک">
                                                    </div>
                                                    <div class="col-3">
                                                        <a href="{{ asset($doc['document_file']) }}" target="_blank" class="btn btn-info">مشاهده فایل</a>
                                                    </div>
                                                @else
                                                    <div class="col-12">
                                                        <input type="file" class="form-control" name="document_file[]" placeholder="فایل مدرک">
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <input type="text" class="form-control readonly" readonly name="document_title[]" placeholder="عنوان مدرک">
                                    </td>
                                    <td>
                                        <input type="file" class="form-control" name="document_file[]" placeholder="فایل مدرک">
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                        <label for="description">توضیحات درخواست دهنده</label>
                        <textarea name="description" class="form-control readonly" readonly id="description" required>{{ old('description', $document->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                        <label for="sender_description">توضیحات ارسال کننده</label>
                        <textarea name="sender_description" class="form-control" id="sender_description" required>{{ old('sender_description', $document->sender_description) }}</textarea>
                        @error('sender_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit" id="btn_form">به‌روز رسانی فرم</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#btn_form').on('click', function () {
                let button = $(this);
                // تغییر متن و غیر فعال کردن دکمه برای جلوگیری از چندبار کلیک
                button.prop('disabled', true).text('در حال ارسال...');
                button.closest('form').submit();
            });
        });
    </script>
@endsection
