@extends('panel.layouts.master')
@section('title', 'لیست درخواست وضعیت چک')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>لیست درخواست وضعیت چک</h6>
                    @can('create-cheque-check')
                        <a href="{{ route('cheque.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus mr-2"></i>
                            ثبت درخواست وضعیت چک
                        </a>
                    @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>درخواست دهنده</th>
                        <th>حداکثر زمان ثبت وضعیت چک</th>
                        <th>وضعیت</th>
                        <th>زمان ثبت</th>
                        @can('edit-cheque-check')
                            <th>ثبت وضعیت چک</th>
                        @else
                            <th>مشاهده وضعیت چک</th>
                        @endcan
                        @can('delete-cheque-check')
                            <th>حذف</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cheque as $key => $Cheque)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $Cheque->user->fullName() }}</td>
                            <td>{{ $Cheque->max_send_time }} ساعت </td>
                            <td>
                                @if($Cheque->status == 'sent')
                                    <span class="badge badge-success">{{ \App\Models\Cheque::STATUS['sent'] }}</span>
                                @else
                                    <span class="badge badge-warning">{{ \App\Models\Cheque::STATUS['pending'] }}</span>
                                @endif
                            </td>
                            <td>{{ verta($Cheque->created_at)->format('H:i - Y/m/d') }}</td>
                            @can('edit-cheque-check')
                                <td>
                                    <a class="btn btn-primary btn-floating" href="{{ route('cheque.edit', $Cheque->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            @else
                                <td>
                                    <a class="btn btn-info btn-floating" href="{{ route('cheque.show', $Cheque->id) }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            @endcan
                            @can('delete-cheque-check')
                                <td>
                                    <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('cheque.destroy',$Cheque->id) }}" data-id="{{ $Cheque->id }}">
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
            <div class="d-flex justify-content-center">{{ $cheque->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

