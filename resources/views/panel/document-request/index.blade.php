@extends('panel.layouts.master')
@section('title', 'لیست درخواست مدارک')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست درخواست مدارک</h6>
                @can('document-request-create')
                    <a href="{{ route('document_request.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-2"></i>
                        ثبت درخواست مدارک
                    </a>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ثبت کننده</th>
                        <th>عنوان</th>
                        <th>ارسال کننده</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        <th>ویرایش</th>
                        @if(in_array(auth()->user()->role->name,['ceo','office-manager','admin','accountant']))
                            <th>ارسال مدارک</th>
                        @else
                            <th>مشاهده مدارک</th>
                        @endif
                        @can('document-request-delete')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($documents as $key => $doc)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $doc->user->fullName() }}</td>
                            <td>{{ $doc->title }}</td>
                            <td>@if(isset($doc->sender))
                                    {{ $doc->sender->fullName() }}
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
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a class="btn btn-info btn-floating"
                                       href="{{ route('document_request.show', $doc->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endif
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

