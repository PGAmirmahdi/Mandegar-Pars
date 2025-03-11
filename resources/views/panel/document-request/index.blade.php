@extends('panel.layouts.master')
@section('title', 'لیست درخواست مدارک')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                @can('document-request-create')
                    <a href="{{ route('document_request.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت درخواست مدارک
                    </a>
                @endcan
            </div>
            <form action="{{ route('document_request.search') }}" method="get" id="search_form"></form>
            <div class="row col-10 mb-3">
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <input type="text" name="title" form="search_form" class="form-control" placeholder="عنوان"
                           value="{{ request()->title ?? null }}">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="user" form="search_form" class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="0">
                        <option value="all" selected>ثبت کننده(همه)</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request()->user == $user->id ? 'selected' : '' }}>
                                {{ $user->name . " " . $user->family . ' - ' . $user->role->label}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 mt-2">
                    <select name="status" class="js-example-basic-single select2-hidden-accessible" id="status" form="search_form">
                        @foreach(\App\Models\DocumentRequest::STATUS as $key => $value)
                            <option value="{{ $key }}" {{ (old('status', $status ?? 'all') == $key) ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <select name="sender" form="search_form"
                            class="js-example-basic-single select2-hidden-accessible"
                            data-select2-id="1">
                        <option value="all" selected>ارسال کننده(همه)</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request()->sender == $user->id ? 'selected' : '' }}>
                                {{ $user->name . " " . $user->family . ' - ' . $user->role->label}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12 mt-2">
                    <button type="submit" class="btn btn-info" form="search_form"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>شناسه</th>
                        <th>ثبت کننده</th>
                        <th>عنوان</th>
                        <th>ارسال کننده</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        <th>ویرایش</th>
                        @if(in_array(auth()->user()->role->name,['ceo','office-manager','admin','accountant']))
                            <th>ارسال مدارک</th>
                        @endif
                        <th>مشاهده مدارک</th>
                        @can('document-request-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($documents as $key => $doc)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{$doc->id}}</td>
                            <td>{{ $doc->user->fullName() . '('  . $doc->user->role->label . ')'}}</td>
                            <td>{{ $doc->title }}</td>
                            <td>@if(isset($doc->sender))
                                    {{ $doc->sender->fullName() . '('  . $doc->user->role->label . ')'}}
                                @else
                                    <span
                                        class="badge badge-warning">{{ \App\Models\DocumentRequest::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            <td>
                                @if($doc->status == 'sent')
                                    <span
                                        class="badge badge-success">{{ \App\Models\DocumentRequest::STATUS['sent'] }}</span>
                                @elseif($doc->status == 'not-sent')
                                    <span
                                        class="badge badge-warning">{{ \App\Models\DocumentRequest::STATUS['not-sent'] }}</span>
                                @else
                                    <span
                                        class="badge badge-warning">{{ \App\Models\DocumentRequest::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($doc->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-primary btn-floating @if(in_array($doc->status, ['sent','not-sent'])) disabled @endif"
                                   @if(in_array($doc->status, ['sent','not-sent']))href="#" disabled
                                   @else href="{{ route('document_request.edit', $doc->id) }}" @endif>
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                            @if(in_array(auth()->user()->role->name,['ceo','office-manager','admin','accountant']))
                                <td>
                                    <a class="btn btn-facebook btn-floating"
                                       href="{{ route('document_request.send', $doc->id) }}">
                                        <i class="fa-solid fa-folder-tree"></i>
                                    </a>
                                </td>
                            @endif
                            <td>
                                <a class="btn btn-info btn-floating"
                                   href="{{ route('document_request.show', $doc->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @can('document-request-delete')
                                <td>
                                    <button
                                        class="btn btn-danger btn-floating trashRow @if(auth()->id() != $doc->user_id || in_array($doc->status, ['sent','not-sent'])) disabled @endif "
                                        data-url="{{ route('document_request.destroy',$doc->id) }}"
                                        data-id="{{ $doc->id }}"
                                        @if(auth()->id() != $doc->user_id || in_array($doc->status, ['sent','not-sent'])) disabled @endif>
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $documents->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

