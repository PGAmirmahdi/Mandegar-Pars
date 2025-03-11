@extends('panel.layouts.master')
@section('title', 'نمایش درخواست مدارک')

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
            <!-- نمایش عنوان کلی -->
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-3">
                <label for="title">عنوان کلی<span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" id="title"
                       value="{{ old('title', $document->title) }}" disabled>
            </div>

            <!-- نمایش جدول مدارک -->
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
                                        <input type="text" class="form-control" name="document_title[]"
                                               placeholder="عنوان مدرک"
                                               value="{{ old('document_title.' . $index, $doc['document_title']) }}"
                                               disabled>
                                    </td>
                                    <td>
                                        <div class="row">
                                            @if(isset($doc['document_file']) && !empty($doc['document_file']))
                                                <div class="col-12">
                                                    <a href="{{ asset($doc['document_file']) }}" target="_blank"
                                                       class="btn btn-info">
                                                        مشاهده فایل
                                                    </a>
                                                </div>
                                            @else
                                                <div class="col-12">
                                                    <span>هنوز فایلی ارسال نشده</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>
                                    <input type="text" class="form-control" disabled name="document_title[]"
                                           placeholder="عنوان مدرک">
                                </td>
                                <td>
                                    <span>هنوز مدرکی موجود نیست</span>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot>
                        <tr></tr>
                        </tfoot>
                    </table>
                </div>

                <!-- نمایش توضیحات -->
                <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                    <label for="description">توضیحات درخواست دهنده</label>
                    <textarea name="description" class="form-control" id="description" disabled
                              required>{{ old('description', $document->description) }}</textarea>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                    <label for="sender_description">توضیحات ارسال کننده</label>
                    <textarea name="sender_description" class="form-control" id="sender_description" disabled
                              required>{{ old('sender_description', $document->sender_description) }}</textarea>
                </div>
            </div>
            <div class="mt-5 row justify-content-between mx-1">
                <a href="{{ route('document_request.index') }}" class="btn btn-danger">بازگشت</a>
                @if($document->status == 'sent')
                    <a href="{{ route('document_request.send', $document->id) }}" class="btn btn-warning">
                        <i class="fa-solid fa-folder-tree text-light"></i>
                    </a>
                @elseif($document->status == 'pending')
                    <a href="{{ route('document_request.edit', $document->id) }}" class="btn btn-warning">
                        <i class="fa fa-edit text-light"></i>
                    </a>
                @else

                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // در این صفحه نیازی به اسکریپت‌های ارسال فرم نیست.
    </script>
@endsection
