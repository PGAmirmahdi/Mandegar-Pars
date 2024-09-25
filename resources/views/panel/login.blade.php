@extends('panel.layouts.master')
@section('title', 'ورود به اکانت همکاران')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ورود به اکانت همکار</h6>
            </div>
            <form action="{{ route('login-account') }}" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-4" id="users">
                        <label for="user">اکانت</label>
                        <select name="user" id="user" class="js-example-basic-single select2-hidden-accessible" data-select2-id="4" tabindex="-1" aria-hidden="true">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user') ? (in_array($user->id, old('user')) ? 'selected' : '') : '' }}>{{ $user->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('user')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ورود</button>
            </form>
        </div>
    </div>
@endsection

