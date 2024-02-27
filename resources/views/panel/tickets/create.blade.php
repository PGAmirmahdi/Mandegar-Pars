@extends('panel.layouts.master')
@section('title', 'ثبت تیکت')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت تیکت</h6>
            </div>
            <form action="{{ route('tickets.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="receiver">گیرنده<span class="text-danger">*</span></label>
                        <select name="receiver" class="js-example-basic-single select2-hidden-accessible">
                            <option value="">انتخاب کنید...</option>
                            @can('accountant')
                                @foreach(\App\Models\User::where('id','!=', auth()->id())->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('receiver') == $user->id ? 'selected' : '' }}>{{ $user->role->label.' - '.$user->fullName() }}</option>
                                @endforeach
                            @else
                                @canany(['sales-manager','ceo','warehouse-keeper'])
                                    @foreach(\App\Models\User::where('id','!=', auth()->id())->get() as $user)
                                        <option value="{{ $user->id }}" {{ old('receiver') == $user->id ? 'selected' : '' }}>{{ $user->role->label.' - '.$user->fullName() }}</option>
                                    @endforeach
                                @else
                                    @php
                                        $accountants = \App\Models\User::whereHas('role' , function ($role) {
                                            $role->whereHas('permissions', function ($q) {
                                                $q->where('name', 'accountant');
                                            });
                                        })->pluck('id');
                                    @endphp
                                    @foreach(\App\Models\User::where('id','!=', auth()->id())->whereNotIn('id',$accountants)->get() as $user)
                                        <option value="{{ $user->id }}" {{ old('receiver') == $user->id ? 'selected' : '' }}>{{ $user->role->label.' - '.$user->fullName() }}</option>
                                    @endforeach
                                @endcanany
                            @endcan
                        </select>
                        @error('receiver')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="title">عنوان تیکت<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="file">فایل</label>
                        <input type="file" name="file" class="form-control" id="file">
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <a href="" target="_blank" class="btn btn-link d-none" id="file_preview">پیش نمایش</a>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2"></div>
                    <div class="col-xl-6 col-lg-6 col-md-6 mb-3">
                        <label for="text">متن تیکت<span class="text-danger">*</span></label>
                        <textarea type="text" name="text" class="form-control" id="text" rows="5">{{ old('text') }}</textarea>
                        @error('text')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#file').on('change', function () {
                $('#file_preview').removeClass('d-none')

                let file = this.files[0];
                let url = URL.createObjectURL(file);

                $('#file_preview').attr('href',url)
            })
        })
    </script>
@endsection

