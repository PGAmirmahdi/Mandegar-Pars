@extends('panel.layouts.master')
@section('title', 'نامه نگاری')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">نامه ها</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex justify-content-end">
                                @can('indicator')
                                    <a href="{{ route('indicator.excel')}}" class="btn btn-success mx-1">
                                        <i class="fa fa-file-excel mr-2"></i>
                                        خروجی اکسل
                                    </a>
                                    <a href="{{ route('indicator.create') }}" class="btn btn-primary mx-1">
                                        <i class="fa fa-plus mr-2"></i>
                                        ایجاد نامه جدید
                                    </a>
                                @endcan
                            </div>
                            <div class="card-title d-flex justify-content-start">
                                @can('indicator')
                                    <form action="{{url('/panel/indicator')}}">
                                        <div class="input-group">
                                            <input type="text" name="number" class="form-control"
                                                   placeholder="شماره نامه">
                                            <input type="submit" class="btn btn-info" value="جستجو">
                                        </div>
                                    </form>
                                @endcan
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dataTable dtr-inline text-center"
                                       style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>عنوان</th>
                                        <th>خطاب به</th>
                                        <th>شماره نامه</th>
                                        @if(auth()->user()->isCEO() || auth()->user()->isAdmin())
                                            <th>ایجاد شده توسط</th>
                                        @endif

                                        <th>تاریخ</th>
                                        {{--                                        @can('coupons-edit')--}}
                                        <th>دانلود</th>
                                        <th>ویرایش</th>
                                        {{--                                        <th>حذف</th>--}}
                                        {{--                                        @endcan--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $search = request()->input('number');
                                    @endphp
                                    @foreach($indicators as $key => $indicator)
                                        <tr>
                                            <td>{{ ++$key }}</td>

                                            <td>{{ $indicator->title }}</td>
                                            <td>{{ $indicator->to??'-' }}</td>
                                            @php
                                                $highlightedNumber = $indicator->number ?? '---';
                                                if ($search) {
                                                    $highlightedNumber = str_ireplace($search, "<span class='bg-warning'>" . $search . "</span>", $highlightedNumber);
                                                }
                                            @endphp
                                            <td>{!! $highlightedNumber !!}</td>
                                            @if(auth()->user()->isCEO() || auth()->user()->isAdmin())
                                                <td>{{ $indicator->user->fullName()}}</td>
                                            @endif
                                            <td>{{ verta($indicator->created_at)->format('H:i - Y/m/d') }}</td>
                                            {{--                                            @can('coupons-edit')--}}
                                            <td><a class="btn btn-info btn-floating"
                                                   href="{{ route('indicator.download', $indicator->id) }}">
                                                    <i class="fa fa-download"></i>
                                                </a></td>
                                            <td>
                                                <a class="btn btn-warning btn-floating"
                                                   href="{{ route('indicator.edit', $indicator->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                            {{--                                            @endcan--}}
                                            {{--                                            @can('coupons-delete')--}}

                                            {{--                                            <td>--}}
                                            {{--                                                <button class="btn btn-danger btn-floating trashRow"--}}
                                            {{--                                                        data-url="{{ route('indicator.destroy',$indicator->id) }}"--}}
                                            {{--                                                        data-id="{{ $indicator->id }}">--}}
                                            {{--                                                    <i class="fa fa-trash"></i>--}}
                                            {{--                                                </button>--}}
                                            {{--                                            </td>--}}
                                            {{--                                            @endcan--}}
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
                                class="d-flex justify-content-center">{{ $indicators->appends(request()->all())->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



