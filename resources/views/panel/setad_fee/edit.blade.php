@extends('panel.layouts.master')
@section('title', 'ایجاد کارمزد ستاد')
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
                                        <div class="text-center text-info d-block" id="price">{{old('price',$setad->price)?number_format(old('price',$setad->price)):''}}</div>
                                        @error('price')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                                        <label for="tracking_number">
                                            شماره شبا<span class="text-danger">*</span></label>
                                        <input type="text" name="shaba_number" class="form-control"
                                               value="{{old('shaba_number',$setad->shaba_number)}}" placeholder="به همراه IR">
                                        @error('shaba_number')
                                        <div class="invalid-feedback text-danger d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="tracking_number">توضیحات</label>
                                        <textarea class="form-control"
                                                  name="description">{{old('description',$setad->description)}}</textarea>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary mt-3">ثبت فرم</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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
                beforeSend: function() {
                    processing.show();
                },
                success: function(response) {
                    processing.hide();
                    handleResponse(response);
                },
                error: function(xhr, status, error) {
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




