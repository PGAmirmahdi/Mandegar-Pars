@extends('panel.layouts.master')
@section('title', 'تعیین وضعیت سفارش')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-5">
                <div class="w-100">
                    <h6>تعیین وضعیت سفارش</h6>
                </div>
            </div>
            <form action="{{ route('invoice.action.store', $invoice->id) }}" method="post" enctype="multipart/form-data" id="invoice_form">
                @csrf
                <div class="form-row mb-4">
                    <div class="col-12">
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary justify-content-center {{ old('status') == 'invoice' || old('status') == null ? 'active' : '' }}">
                                <input type="radio" id="status1" name="status" class="custom-control-input" value="invoice" form="invoice_form" {{ old('status') == 'invoice' || old('status') == null ? 'checked' : '' }}>پیش فاکتور
                            </label>
                            <label class="btn btn-outline-primary justify-content-center {{ old('status') == 'factor' ? 'active' : '' }}">
                                <input type="radio" id="status2" name="status" class="custom-control-input" value="factor" form="invoice_form" {{ old('status') == 'factor' ? 'checked' : '' }}>فاکتور
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-5 invoice_sec">
                        <div class="form-group">
                            <label for="invoice_file">فایل پیش فاکتور (PDF)<span class="text-danger">*</span></label>
                            <input type="file" name="invoice_file" class="form-control" id="invoice_file" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-5 factor_sec">
                        <div class="form-group">
                            <label for="factor_file">فایل فاکتور (PDF)<span class="text-danger">*</span></label>
                            <input type="file" name="factor_file" class="form-control" id="factor_file" accept="application/pdf">
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" type="submit" id="btn_form">
                    <i class="fa fa-paper-plane mr-2"></i>
                    <span id="btn_send_text">ثبت و ارسال به همکار فروش</span>
                </button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var status = $("input[name='status']").val();
            show_section(status);

            $("input[name='status']").on('change', function () {
                show_section(this.value);
            })

            function show_section(status)
            {
                if(status === 'invoice'){
                    $('.invoice_sec').removeClass('d-none')
                    $('.factor_sec').addClass('d-none')

                    $('#btn_send_text').text('ثبت و ارسال به همکار فروش')
                }else{
                    $('.invoice_sec').addClass('d-none')
                    $('.factor_sec').removeClass('d-none')

                    $('#btn_send_text').text('ثبت و ارسال به انباردار')
                }
            }
        })
    </script>
@endsection
