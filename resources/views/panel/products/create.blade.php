@extends('panel.layouts.master')
@section('title', 'ایجاد محصول')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ایجاد محصول</h6>
            </div>
            <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="title">عنوان محصول<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" placeholder="پرینتر HP">
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="code">کد محصول<span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" id="code" value="{{ old('code') }}">
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
{{--                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">--}}
{{--                        <label for="slug">اسلاگ<span class="text-danger">*</span></label>--}}
{{--                        <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug') }}" placeholder="hp-printer">--}}
{{--                        @error('slug')--}}
{{--                            <div class="invalid-feedback d-block">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="image">تصویر<span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" id="image" value="{{ old('image') }}">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="category">دسته بندی <span class="text-danger">*</span></label>
                        <select class="form-control" name="category" id="category">
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="system_price">قیمت سامانه (ریال)<span class="text-danger">*</span></label>
                        <input type="text" name="system_price" class="form-control" id="system_price" value="{{ old('system_price') }}">
                        <small id="system_price_words" class="text-primary"></small>
                        @error('system_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="partner_price_tehran">قیمت همکار - تهران (ریال)<span class="text-danger">*</span></label>
                        <input type="text" name="partner_price_tehran" class="form-control" id="partner_price_tehran" value="{{ old('partner_price_tehran') }}">
                        <small id="partner_price_tehran_words" class="text-primary"></small>
                        @error('partner_price_tehran')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="partner_price_other">قیمت همکار - شهرستان (ریال)<span class="text-danger">*</span></label>
                        <input type="text" name="partner_price_other" class="form-control" id="partner_price_other" value="{{ old('partner_price_other') }}">
                        <small id="partner_price_other_words" class="text-primary"></small>
                        @error('partner_price_other')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                        <label for="single_price">قیمت تک فروشی (ریال)<span class="text-danger">*</span></label>
                        <input type="text" name="single_price" class="form-control" id="single_price" value="{{ old('single_price') }}">
                        <small id="single_price_words" class="text-primary"></small>
                        @error('single_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="editor-demo2">توضیحات </label>
                        <textarea id="editor-demo2"></textarea>
                        @error('category')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-xl-6 col-lg-8 col-md-6 col-sm-12 mb-3" id="printer_properties">
                        <div class="d-flex justify-content-between mb-3">
                            <label>ویژگی های محصول </label>
                            <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن ویژگی</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                    <tr>
                                        <th>رنگ</th>
                                        <th>تعداد چاپ</th>
                                        <th>تعداد</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <select class="form-control" name="colors[]">
                                            @foreach(\App\Models\Product::COLORS as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="print_count[]" class="form-control" min="0" value="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="counts[]" class="form-control" min="0" value="0" required>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/assets/js/number2word.js') }}" type="text/javascript"></script>
    <script>
        var number2Word = new Number2Word();

        var printer_category_id = "{{ \App\Models\Category::where('slug','printer')->first()->id }}";
        var colors = [];

        @foreach(\App\Models\Product::COLORS as $key => $value)
            colors.push({
                "key": "{{ $key }}",
                "value": "{{ $value }}",
            })
        @endforeach

        var options_html;

        $.each(colors, function (i, item) {
            options_html = `<option value="${item.key}">${item.value}</option>`
        })


        $(document).ready(function () {
            printers_properties($('select[name="category"]').val());

            // add property
                $('#btn_add').on('click', function () {
                    $('#properties_table tbody').append(`
                        <tr>
                            <td>
                                <select class="form-control" name="colors[]">${options_html}</select>
                            </td>
                            <td><input type="number" name="print_count[]" class="form-control" min="0" value="0" required></td>
                            <td><input type="number" name="counts[]" class="form-control" min="0" value="0" required></td>
                            <td><button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    `);
                })
            // end add property

            // remove property
                $(document).on('click','.btn_remove', function () {
                    $(this).parent().parent().remove();
                })
            // end remove property

            // change category
                $('select[name="category"]').on('change', function () {
                    printers_properties(this.value);
                })
            // end change category

            // Number To Words

            // when document was ready
            let system_price = number2Word.numberToWords($('#system_price').val()) + ' ریال '
            $('#system_price_words').text(system_price)

            let partner_price_tehran = number2Word.numberToWords($('#partner_price_tehran').val()) + ' ریال '
            $('#partner_price_tehran_words').text(partner_price_tehran)

            let partner_price_other = number2Word.numberToWords($('#partner_price_other').val()) + ' ریال '
            $('#partner_price_other_words').text(partner_price_other)

            let single_price = number2Word.numberToWords($('#single_price').val()) + ' ریال '
            $('#single_price_words').text(single_price)

            // when change the inputs
            $(document).on('keyup','#system_price', function () {
                let price = number2Word.numberToWords(this.value) + ' ریال '
                $('#system_price_words').text(price)
            })

            $(document).on('keyup','#partner_price_tehran', function () {
                let price = number2Word.numberToWords(this.value) + ' ریال '
                $('#partner_price_tehran_words').text(price)
            })

            $(document).on('keyup','#partner_price_other', function () {
                let price = number2Word.numberToWords(this.value) + ' ریال '
                $('#partner_price_other_words').text(price)
            })

            $(document).on('keyup','#single_price', function () {
                let price = number2Word.numberToWords(this.value) + ' ریال '
                $('#single_price_words').text(price)
            })
            // end Number To Words

        })

        function printers_properties(value){
            if (value != printer_category_id){
                $('#printer_properties').addClass('d-none')
                $('#compatible_printers_sec').addClass('d-none')
            }else{
                $('#printer_properties').removeClass('d-none')
                $('#compatible_printers_sec').removeClass('d-none')
            }
        }
    </script>
@endsection

