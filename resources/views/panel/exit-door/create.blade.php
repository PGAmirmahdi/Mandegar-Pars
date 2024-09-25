@extends('panel.layouts.master')
@section('title', 'ثبت خروج')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت خروج</h6>
            </div>
            <form action="{{ route('exit-door.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <label for="inventory_report_id">سفارش<span class="text-danger">*</span></label>
                        <select class="js-example-basic-single select2-hidden-accessible" name="inventory_report_id" id="inventory_report_id">
                            <option value="">انتخاب کنید...</option>
                            @if($inventory_reports->count())
                                @foreach($inventory_reports as $inventory_report)
                                    <option value="{{ $inventory_report->id }}" {{ old('inventory_report_id') == $inventory_report->id ? 'selected' : '' }}> {{ $inventory_report->invoice->id }} - {{ $inventory_report->invoice->customer->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled selected>سفارشی موجود نیست!</option>
                            @endif
                        </select>
                        <span id="factor_link">
                            <a href="" class="btn-link" target="_blank">نمایش سفارش</a>
                        </span>
                        @error('inventory_report_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-4"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                <tr>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-xl-3 col-lg-3 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="status">وضعیت</label>
                            <select name="status" class="form-control" id="status">
                                @foreach(\App\Models\ExitDoor::STATUS as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
                        </div>
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
            $('#inventory_report_id').on('change', function () {
                var inventory_report_id = this.value;
                $('#properties_table tbody').html('<tr><td colspan="2"><div class="spinner-grow text-info" role="status"></div></td></tr>')

                $.ajax({
                    url: '/panel/get-in-outs/'+inventory_report_id,
                    type: 'get',
                    success: function (res){
                        let url = `/panel/invoices/${res.data.invoice_id}`
                        $('#factor_link a').attr('href', url);
                        $('#properties_table tbody').html('')
                        $.each(res.data.items, function (i, item) {
                            $('#properties_table tbody').append(`<tr>
                                <td>${item.inventory.title}</td>
                                <td>${item.count}</td>
                            </tr>`)
                        })
                    }
                })
            })
        })
    </script>
@endsection

