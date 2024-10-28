@extends('panel.layouts.master')

@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-4">بارگذاری فایل جدید</h1>

        <div class="card p-4">
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">انتخاب فایل</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success mt-3">بارگذاری فایل</button>
            </form>
        </div>
    </div>
@endsection
