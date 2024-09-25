@extends('panel.layouts.master')
@section('title', 'لیست قیمت ها')

@php
    $products = \App\Models\Product::all(['id', 'title', 'system_price', 'partner_price_tehran', 'partner_price_other', 'single_price', 'market_price', 'domestic_price']);
@endphp

@section('styles')
    <style>
        #price_table td:hover {
            background-color: #e3daff !important;
        }
        #price_table .item {
            text-align: center;
            background: transparent;
            border: 0;
        }
        #price_table .item:focus {
            border-bottom: 2px solid #5d4a9c;
        }
        #btn_save {
            width: 100%;
            justify-content: center;
            border-radius: 0;
            padding: .8rem;
            font-size: larger;
        }
        #price_table {
            box-shadow: 0 5px 5px 0 lightgray;
        }
        .tableFixHead {
            overflow: auto !important;
            height: 800px !important;
        }
        .tableFixHead thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 1 !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        th, td {
            padding: 8px 16px !important;
        }
        .tableFixHead thead th {
            background: #fff !important;
            border: 1px solid #dee2e6 !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h3 class="text-center mb-4">لیست قیمت ها - (ریال)</h3>
            <div class="table-responsive tableFixHead">
                <table class="table table-striped table-bordered dtr-inline text-center" id="price_table">
                    <thead>
                    <tr>
                        <th class="bg-primary"></th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'system_price']) }}"><i class="fa fa-download text-info"></i></a>
                            سامانه
                        </th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'partner_price_tehran']) }}"><i class="fa fa-download text-info"></i></a>
                            همکار - تهران
                        </th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'partner_price_other']) }}"><i class="fa fa-download text-info"></i></a>
                            همکار - شهرستان
                        </th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'single_price']) }}"><i class="fa fa-download text-info"></i></a>
                            تک فروشی
                        </th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'market_price']) }}"><i class="fa fa-download text-info"></i></a>
                            قیمت بازار
                        </th>
                        <th>
                            <a href="{{ route('prices-list-pdf', ['type' => 'domestic_price']) }}"><i class="fa fa-download text-info"></i></a>
                            قیمت داخلی
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <th>{{ $product->title }}</th>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="system_price" value="{{ number_format($product->system_price) }}">
                            </td>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="partner_price_tehran" value="{{ number_format($product->partner_price_tehran) }}">
                            </td>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="partner_price_other" value="{{ number_format($product->partner_price_other) }}">
                            </td>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="single_price" value="{{ number_format($product->single_price) }}">
                            </td>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="market_price" value="{{ number_format($product->market_price) }}">
                            </td>
                            <td>
                                <input type="text" class="item" data-id="{{ $product->id }}" data-field="domestic_price" value="{{ number_format($product->domestic_price) }}">
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @can('price-list-mandegar')
                <button class="btn btn-primary my-3 mx-1" id="btn_save">
                    <i class="fa fa-check mr-2"></i>
                    <span>ذخیره</span>
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Save button click event
            $('#btn_save').on('click', function () {
                $(this).attr('disabled', 'disabled');
                $('#btn_save span').text('درحال ذخیره سازی...');
                let items = [];

                $.each($('#price_table .item'), function (i, item) {
                    items.push({
                        'id': $(item).data('id'),
                        'field': $(item).data('field'),
                        'price': $(item).val().replace(/,/g, '')
                    });
                });

                $.ajax({
                    url: "{{ route('updatePrice2') }}",
                    type: 'post',
                    data: {
                        items: JSON.stringify(items),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        $('#btn_save').removeAttr('disabled');
                        $('#btn_save span').text('ذخیره');

                        Swal.fire({
                            title: 'با موفقیت ذخیره شد',
                            icon: 'success',
                            showConfirmButton: false,
                            toast: true,
                            timer: 2000,
                            timerProgressBar: true,
                            position: 'top-start',
                            customClass: {
                                popup: 'my-toast',
                                icon: 'icon-center',
                                title: 'left-gap',
                                content: 'left-gap',
                            }
                        });
                    }
                });
            });

            // Input field change event for formatting
            $(document).on('keyup', '.item', function () {
                $(this).val(addCommas($(this).val()));
            });

            function addCommas(nStr) {
                nStr = nStr.replace(/,/g, '');
                let x = nStr.split('.');
                let x1 = x[0];
                let x2 = x.length > 1 ? '.' + x[1] : '';
                let rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                return x1 + x2;
            }
        });
    </script>
@endsection
