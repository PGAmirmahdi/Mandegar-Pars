@extends('panel.layouts.master')
@section('title', 'ویرایش یادداشت')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش یادداشت</h6>
            </div>
            <form action="{{ route('notes.update', $note->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ $note->title }}">
                        @error('title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="text">متن یادداشت<span class="text-danger">*</span></label>
                        <textarea name="text" class="form-control" id="text" rows="5">{{ $note->text }}</textarea>
                        @error('text')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="status">وضعیت <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="status">
                            @foreach(\App\Models\Note::STATUS as $key => $value)
                                <option value="{{ $key }}" {{ $note->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

