@extends('panel.layouts.master')
@section('title', 'ویرایش وظیفه')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش وظیفه</h6>
            </div>
            <form action="{{ route('tasks.update', $task->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="title">عنوان<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ $task->title }}" placeholder="تماس با مشتری">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                        <label for="description">توضیحات<span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" id="description" rows="5">{{ $task->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-4" id="users">
                        <label for="users">تخصیص به </label>
                        <select name="users[]" id="users" class="js-example-basic-single select2-hidden-accessible" multiple="" data-select2-id="4" tabindex="-1" aria-hidden="true">
                            @foreach(\App\Models\User::where('id','!=',auth()->id())->get() as $user)
                                <option value="{{ $user->id }}" {{ $task->users->pluck('id')->toArray() ? (in_array($user->id, $task->users->pluck('id')->toArray()) ? 'selected' : '') : '' }}>{{ $user->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('users')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

