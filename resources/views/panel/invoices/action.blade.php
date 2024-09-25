@extends('panel.layouts.master')
@section('title', 'تعیین وضعیت سفارش')
@section('styles')
    <style>
        .btn.btn-primary:not(:disabled):not(.disabled):focus, a.btn:not(:disabled):not(.disabled):focus[href="#next"], a.btn:not(:disabled):not(.disabled):focus[href="#previous"], .btn-primary{
            box-shadow: 0 0 !important;
            -webkit-box-shadow: 0 0 !important;
        }
    </style>
@endsection
@php
    $isInvoice = $invoice->action ? ($invoice->action->status == 'invoice' ? true : false) : false;
    $isFactor = $invoice->action ? ($invoice->action->status == 'factor' ? true : false) : false;
@endphp
@section('content')
    @if($invoice->action && \Illuminate\Support\Facades\Gate::allows('accountant'))
        {{--  invoice reset Modal  --}}
        <div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resetModalLabel">تایید حذف</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6>می خواهید فایل پیش فاکتور را حذف و مجدد بارگذاری کنید؟</h6>
                        <form action="{{ route('invoice.action.delete', $invoice->action->id) }}" method="post" id="deleteInvoiceAction">
                            @csrf
                            @method('put')
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
                        <button type="submit" class="btn btn-danger" form="deleteInvoiceAction">حذف</button>
                    </div>
                </div>
            </div>
        </div>
        {{--  end invoice reset Modal  --}}

        {{--  factor reset Modal  --}}
        <div class="modal fade" id="factorResetModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="factorResetModalLabel">تایید حذف</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6>می خواهید فایل فاکتور را حذف و مجدد بارگذاری کنید؟</h6>
                        <form action="{{ route('factor.action.delete', $invoice->action->id) }}" method="post" id="deleteFactorAction">
                            @csrf
                            @method('put')
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
                        <button type="submit" class="btn btn-danger" form="deleteFactorAction">حذف</button>
                    </div>
                </div>
            </div>
        </div>
        {{--  end factor reset Modal  --}}
    @endif
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <div class="w-100">
                    <h6>تعیین وضعیت سفارش</h6>
                </div>
            </div>
            @can('accountant')
                @if($invoice->action)
                    @if($invoice->action->confirm)
                        <div class="w-100 text-center">
                            @if($invoice->action->sent_to_warehouse)
                                <h5 class="text-success mt-3">تایید توسط همکار فروش - ارسال فاکتور به انبار</h5>
                            @else
                                <h5 class="text-success mt-3">تایید توسط همکار فروش - <span class="text-warning">در انتظار ارسال فاکتور به انبار</span></h5>
                            @endif
                        </div>
                    @else
                        @if($invoice->action->status == 'factor')
                            <div class="w-100 text-center">
                                <h5 class="text-success mt-3">ارسال فاکتور به انبار</h5>
                            </div>
                        @else
                            <div class="w-100 text-center">
                                <h5 class="text-warning mt-3">در انتظار تایید توسط همکار فروش</h5>
                            </div>
                        @endif
                    @endif
                @endif
            @else
                @if($invoice->action)
                    @if($invoice->action->status == 'factor')
                        <div class="w-100 text-center">
                            <h5 class="text-success mt-3">ارسال فاکتور به انبار توسط حسابداری</h5>
                        </div>
                    @else
                        @if($invoice->action->confirm)
                            <div class="w-100 text-center">
                                @if($invoice->action->sent_to_warehouse)
                                    <h5 class="text-success mt-3">ارسال تاییدیه - ارسال فاکتور به انبار توسط حسابداری</h5>
                                @else
                                    <h5 class="text-success mt-3">ارسال تاییدیه - <span class="text-warning">در انتظار ارسال فاکتور به انبار توسط حسابداری</span></h5>
                                @endif
                            </div>
                        @endif
                    @endif
                @endif
            @endcan
            <div class="mb-5"></div>
            <form action="{{ route('invoice.action.store', $invoice->id) }}" method="post" enctype="multipart/form-data" id="invoice_form">
                @csrf
                <div class="form-row mb-4">
                    <div class="col-12">
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn {{ $invoice->action ? 'disabled' : '' }} {{ $isInvoice == 'invoice' ? 'btn-primary' : 'btn-outline-primary' }} justify-content-center {{ old('status') == 'invoice' || old('status') == null ? 'active' : '' }}">
                                <input type="radio" id="status1" name="status" class="custom-control-input" value="invoice" form="invoice_form" {{ old('status') == 'invoice' || old('status') == null ? 'checked' : '' }} {{ $invoice->action ? 'disabled' : '' }}>پیش فاکتور
                            </label>
                            <label class="btn {{ $invoice->action ? 'disabled' : '' }} {{ $isFactor == 'factor' ? 'btn-primary' : 'btn-outline-primary' }} justify-content-center {{ old('status') == 'factor' ? 'active' : '' }}">
                                <input type="radio" id="status2" name="status" class="custom-control-input" value="factor" form="invoice_form" {{ old('status') == 'factor' ? 'checked' : '' }} {{ $invoice->action ? 'disabled' : '' }}>فاکتور
                            </label>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-5 invoice_sec {{ old('status') == 'factor' ? 'd-none' : '' }}">
                        @if($invoice->action)
                            @if($invoice->action->status != 'factor')
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ $invoice->action->invoice_file }}" class="btn btn-primary" download="{{ $invoice->action->invoice_file }}">
                                            <i class="fa fa-file-pdf mr-2"></i>
                                            دانلود فایل پیش فاکتور
                                        </a>
                                        @can('accountant')
                                            @if(!$invoice->action->confirm)
                                                <a href="#resetModal" class="nav-link" data-toggle="modal">
                                                    <i class="fa fa-times mr-2 text-danger"></i>
                                                    حذف و بارگذاری مجدد فایل
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            @endif
                            @if($invoice->action->factor_file)
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ $invoice->action->factor_file }}" class="btn btn-primary mt-3" download="{{ $invoice->action->factor_file }}">
                                            <i class="fa fa-file-pdf mr-2"></i>
                                            دانلود فایل فاکتور
                                        </a>
                                        @can('accountant')
                                            <a href="#factorResetModal" class="nav-link" data-toggle="modal">
                                                <i class="fa fa-times mr-2 text-danger"></i>
                                                حذف و بارگذاری مجدد فایل
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            @endif
                            @cannot('accountant')
                                @if(!$invoice->action->confirm && $invoice->action->status != 'factor')
                                    <div class="custom-control custom-checkbox mt-5">
                                        <input type="checkbox" class="custom-control-input" name="confirm" id="confirm">
                                        <label class="custom-control-label" for="confirm">پیش فاکتور مورد تایید است</label>
                                    </div>
                                @endif
                            @endcannot
                        @else
                            <div class="form-group">
                                <label for="invoice_file">فایل پیش فاکتور (PDF)<span class="text-danger">*</span></label>
                                <input type="file" name="invoice_file" class="form-control" id="invoice_file" accept="application/pdf">
                                @error('invoice_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    @can('accountant')
                        @if($invoice->action)
                            @if($invoice->action->confirm && !$invoice->action->sent_to_warehouse)
                                <div class="col-12"></div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-5">
                                    <div class="form-group">
                                        <label for="factor_file">فایل فاکتور (PDF)<span class="text-danger">*</span></label>
                                        <input type="file" name="factor_file" class="form-control" id="factor_file" accept="application/pdf" form="invoice_form">
                                        @error('factor_file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 mt-5 factor_sec {{ old('status') == 'invoice' ? 'd-none' : '' }}">
                                <div class="form-group">
                                    <label for="factor_file">فایل فاکتور (PDF)<span class="text-danger">*</span></label>
                                    <input type="file" name="factor_file" class="form-control" id="factor_file" accept="application/pdf">
                                    @error('factor_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    @endcan
                </div>
                @if($invoice->action)
                    @cannot('accountant')
                        @if(!$invoice->action->confirm && $invoice->action->status != 'factor')
                            <input type="hidden" name="send_to_accountant">
                            <button class="btn btn-success mt-3" type="submit" id="btn_send_to_accountant">
                                <i class="fa fa-paper-plane mr-2"></i>
                                <span>ثبت و ارسال به حسابدار</span>
                            </button>
                        @endif
                    @endcannot
                    @can('accountant')
                        @if($invoice->action->sent_to_warehouse == 0 && $invoice->action->status != 'factor' && $invoice->action->confirm)
                            <input type="hidden" name="send_to_warehouse">
                            <button class="btn btn-success mt-3" type="submit" id="btn_send_to_warehouse">
                                <i class="fa fa-paper-plane mr-2"></i>
                                <span>ثبت و ارسال به انبار</span>
                            </button>
                        @endif
                    @endcan
                @else
                    <button class="btn btn-success" type="submit" id="btn_form">
                        <i class="fa fa-paper-plane mr-2"></i>
                        <span id="btn_send_text">ثبت و ارسال به همکار فروش</span>
                    </button>
                @endif
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var status = $("input[name='status']").val();
            @if(!old('status'))
                show_section(status);
            @endif

            check_confirm($('#confirm').is(':checked'));

            $("input[name='status']").on('change', function () {
                show_section(this.value);
            })

            $('#confirm').on('change', function () {
                check_confirm(this.checked)
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

            function check_confirm(confirm)
            {
                if(confirm){
                    $('#btn_send_to_accountant').removeAttr('disabled')
                }else{
                    $('#btn_send_to_accountant').attr('disabled', 'disabled')
                }
            }
        })
    </script>
@endsection
