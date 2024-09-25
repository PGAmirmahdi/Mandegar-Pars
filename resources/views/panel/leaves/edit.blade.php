@extends('panel.layouts.master')
@section('title', 'تعیین وضعیت مرخصی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>تعیین وضعیت مرخصی</h6>
            </div>
            <form action="{{ route('leaves.update', $leave->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-3">
                                <strong>درخواست دهنده:</strong>
                                <span>{{ $leave->user->fullName() }}</span>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-3">
                                <strong>عنوان درخواست:</strong>
                                <span>{{ $leave->title }}</span>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-3">
                                <strong>نوع:</strong>
                                <span>{{ \App\Models\Leave::TYPE[$leave->type] }}</span>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mb-3">
                                <strong>زمان مرخصی:</strong>
                                @if($leave->type == 'hourly')
                                    <span>{{ verta($leave->from_date)->format('Y/m/d').' - '.verta($leave->from)->format('H:i'). ' تا '.verta($leave->to)->format('H:i') }}</span>
                                @else
                                    <span>{{ verta($leave->from_date)->format('Y/m/d').' - '.verta($leave->to_date)->format('Y/m/d') }}</span>
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <strong>توضیحات:</strong>
                                <p>{{ $leave->desc }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-4" id="users">
                            <label for="status">وضعیت<span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control">
                                @foreach(\App\Models\Leave::STATUS as $key => $value)
                                    <option value="{{ $key }}" {{ $leave->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-3">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control" id="description" rows="5">{{ $leave->answer }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

