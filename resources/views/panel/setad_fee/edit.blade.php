@extends('panel.layouts.master')
@section('title', 'ویرایش کارمزد ستاد')
@section('styles')
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        .processing {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">ایجاد کارمزد ستاد</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col">
                    <div class="card">
                        <form action="{{ route('setad-fee.update',$setad->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label for="order">
                                            شماره سفارش<span class="text-danger">*</span></label>
                                        <input type="number" name="order" id="order" class="form-control text-start"
                                               value="{{old('order',$order->code)}}">
                                        <span class="text-info  processing">درحال بررسی ...</span>
                                        <div id="desc-order"></div>
                                        @error('order')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label for="tracking_number">
                                            شماره پیگیری<span class="text-danger">*</span></label>
                                        <input type="text" name="tracking_number" class="form-control"
                                               value="{{old('tracking_number',$setad->tracking_number)}}">
                                        @error('tracking_number')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label for="tracking_number">
                                            مبلغ کارمزد(ریال)<span class="text-danger">*</span></label>
                                        <input type="number" name="price" id="priceInput" class="form-control"
                                               value="{{old('price',$setad->price)}}">
                                        <div class="text-center text-info d-block"
                                             id="price">{{old('price',$setad->price)?number_format(old('price',$setad->price)):''}}</div>
                                        @error('price')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{--                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">--}}
                                    {{--                                        <label for="tracking_number">--}}
                                    {{--                                            شماره شبا<span class="text-danger">*</span></label>--}}
                                    {{--                                        <input type="text" name="shaba_number" class="form-control"--}}
                                    {{--                                               value="{{old('shaba_number',$setad->shaba_number)}}" placeholder="به همراه IR">--}}
                                    {{--                                        @error('shaba_number')--}}
                                    {{--                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>--}}
                                    {{--                                        @enderror--}}
                                    {{--                                    </div>--}}
                                    <div class="col-12 mb-3">
                                        <label for="tracking_number">توضیحات</label>
                                        <textarea class="form-control"
                                                  name="description">{{old('description',$setad->description)}}</textarea>
                                    </div>
                                    <div class="col-4 mb-3">
                                        @if($setad->receipt_file_path == null)
                                            <label for="tracking_number">آپلود رسید کارمزد (PDF)<span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="receipt"
                                                   accept="application/pdf">
                                        @else
                                            <a href="{{ $setad->receipt_file_path }}" class="btn btn-primary mt-3"
                                               download="{{ $setad->receipt_file_path }}">
                                                <i class="fa fa-file-pdf mr-2"></i>
                                                دانلود فایل رسید
                                            </a>
                                            <a href="#factorResetModal" class="nav-link" data-bs-toggle="modal">
                                                <i class="fa fa-times mr-2 text-danger"></i>
                                                حذف و بارگذاری مجدد فایل
                                            </a>
                                        @endif
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary mt-3" id="submit_button">ثبت فرم</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="factorResetModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="factorResetModalLabel">تایید حذف</h5>
                    <button type="button" class="ban" data-bs-dismiss="modal" aria-label="بستن">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>می خواهید فایل رسید را حذف و مجدد بارگذاری کنید؟</h6>
                    <form action="{{ route('receipt.action.delete', $setad->id) }}" method="post"
                          id="deleteFactorAction">
                        @csrf
                        @method('put')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لغو</button>
                    <button type="submit" class="btn btn-danger" form="deleteFactorAction">حذف</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).on('click', '.nav-link', function () {
            const factor = $(this).data('id'); // دریافت ID ردیف
            $('#factor').val(factor); // مقداردهی به فیلد مخفی
            $('#factorResetModal').modal('show'); // نمایش Modal
        });
        $(document).ready(function () {
            $('#submit_button').on('click', function () {
                let button = $(this);
                let dots = 0;

                // غیرفعال کردن دکمه
                button.prop('disabled', true).text('در حال ارسال');

                // ایجاد افکت چشمک‌زن و تغییر نقطه‌ها
                let interval = setInterval(() => {
                    dots = (dots + 1) % 4; // مقدار 0 تا 3
                    let text = 'در حال ارسال' + '.'.repeat(dots);
                    button.text(text).fadeOut(3000).fadeIn(3000); // افکت چشمک زدن
                }, 6000);

                // ارسال فرم به صورت خودکار
                button.closest('form').submit();

                // متوقف کردن افکت بعد از ارسال (اختیاری، چون صفحه معمولاً رفرش می‌شود)
                setTimeout(() => clearInterval(interval), 10000);
            });
        });
        $('#order').on('input', function (event) {
            var value = $(this).val();
            var processing = $('.processing');
            var desc = $('#desc-order');
            processing.show();
            if (value.length > 8) {
                $(this).val(value.slice(0, 8));
            }
            desc.empty();
            console.log('مقدار وارد شده: ' + value);
            $.ajax({
                url: '/panel/search-setad-fee/' + $(this).val(),
                method: 'GET',
                beforeSend: function () {
                    processing.show();
                },
                success: function (response) {
                    processing.hide();
                    handleResponse(response);
                },
                error: function (xhr, status, error) {
                    processing.hide();
                    console.error('خطا در ارسال درخواست:', error);
                }
            });

            function handleResponse(response) {
                const desc = $('#desc-order');
                desc.empty();
                if (response.status === 'success') {
                    desc.append(`
            <span class="text-success">مشتری : ${response.data.customer}</span>
            <br>
            <span class="text-success">جمع سفارش : ${response.data.total} ریال</span>
        `);
                } else {
                    console.log(response.status)
                    desc.append(`
            <span class="text-danger">شماره پیگیری یافت نشد!</span>
        `);
                }
            }

        });

        $('#priceInput').on('input', function () {
            let value = $(this).val().replace(/,/g, '');
            let formattedValue = '';

            if (value) {

                formattedValue = Number(value).toLocaleString();
                $('#price').text(formattedValue);
            } else {
                $('#price').text('');
            }
        });
    </script>
@endsection




