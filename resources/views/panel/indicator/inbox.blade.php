@extends('panel.layouts.master')
@section('title', 'نامه نگاری')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">صندوق ورودی</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row ">
                <div class="col">
                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dataTable dtr-inline text-center"
                                       style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>عنوان</th>
                                        <th>شماره نامه</th>
                                        <th>تاریخ</th>
                                        <th>دانلود</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inbox as $key => $indicator)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $indicator->title }}</td>
                                            <td>{{ $indicator->number?? '---' }}</td>

                                            <td>{{ verta($indicator->created_at)->format('H:i - Y/m/d') }}</td>
                                            <td>
                                                <a class="btn btn-info btn-floating"
                                                   href="{{ route('indicator.download', $indicator->id) }}">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div
                                class="d-flex justify-content-center">{{ $inbox->appends(request()->all())->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



