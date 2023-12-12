@extends('panel.layouts.master')
@section('title', 'ویرایش خروجی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش خروجی</h6>
                <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
            </div>
            <form action="{{ route('inventory-reports.update', $inventoryReport->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ $inventoryReport->type }}">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <label for="factor_id">فاکتور<span class="text-danger">*</span></label>
                        <select class="form-control" name="factor_id" id="factor_id" readonly style="pointer-events: none">
                            <option value="">انتخاب کنید...</option>
                            @if(\App\Models\Factor::count())
                                @foreach(\App\Models\Factor::all() as $factor)
                                    <option value="{{ $factor->id }}" {{ $inventoryReport->factor_id == $factor->id ? 'selected' : '' }}> {{ $factor->id }} - {{ $factor->invoice->customer->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled selected>فاکتوری موجود نیست!</option>
                            @endif
                        </select>
                        <span id="factor_link">
                            <a href="/panel/invoices/{{ $inventoryReport->factor ? $inventoryReport->factor->invoice_id : '' }}?type=factor" class="btn-link" target="_blank">نمایش فاکتور</a>
                        </span>
                        @error('factor_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-8"></div>
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="person"> تحویل گیرنده <span class="text-danger">*</span></label>
                            <input type="text" name="person" class="form-control" id="person" value="{{ $inventoryReport->person }}">
                            @error('person')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="output_date"> تاریخ خروج <span class="text-danger">*</span></label>
                            <input type="text" name="output_date" class="form-control date-picker-shamsi-list" id="output_date" value="{{ old('output_date') ?? verta()->format('Y/m/d') }}">
                            @error('output_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                <tr>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventoryReport->in_outs as $item)
                                        <tr>
                                            <td>
                                                <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                                    @foreach(\App\Models\Inventory::where('warehouse_id', $warehouse_id)->get(['id','title','type']) as $inventory)
                                                        <option value="{{ $inventory->id }}" {{ $inventory->id == $item->inventory_id ? 'selected' : '' }}>{{ \App\Models\Inventory::TYPE[$inventory->type].' - '.$inventory->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="counts[]" class="form-control" min="1" value="{{ $item->count }}" required>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="alert alert-warning d-none" id="alert_section">
                                <div>
                                    <div id="miss_products" class="d-none">
                                        <div>
                                            <strong><i class="fa fa-circle"></i></strong>
                                            <strong>توجه!</strong> برخی کالاهای فاکتور در انبار تعریف نشده اند
                                        </div>
                                        <small>ابتدا آنها را در انبار تعریف کنید</small>
                                        <br>
                                        <small>
                                            <span>کد کالاها:</span>
                                            <span id="codes"></span>
                                        </small>
                                    </div>
                                    <div id="other_products" class="d-none">
                                        <div>
                                            <strong><i class="fa fa-circle"></i></strong>
                                            <strong>توجه!</strong> محصولات دیگر در فاکتور باید بصورت دستی وارد شوند
                                            <ul id="items">
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @error('inventory_count')
                            <div class="alert alert-danger">
                                <p><strong>توجه!</strong> موجودی کالا در انبار جهت خروج کافی نمی باشد: </p>
                                <ul>
                                    @foreach(session('error_data') as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="description">توضیحات</label>
                            <textarea name="description" class="form-control" id="description" rows="5">{{ $inventoryReport->description }}</textarea>
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
        var inventory = [];

        var options_html;

        @foreach(\App\Models\Inventory::where('warehouse_id', $warehouse_id)->get(['id','title','code','type']) as $item)
        inventory.push({
            "id": "{{ $item->id }}",
            "code": "{{ $item->code }}",
            "type": "{{ \App\Models\Inventory::TYPE[$item->type] }}",
            "title": "{{ $item->title }}",
        })
        @endforeach

        $.each(inventory, function (i, item) {
            options_html += `<option value="${item.id}">${item.type} - ${item.title}</option>`
        })

        $(document).ready(function () {
            // add property
            $('#btn_add').on('click', function () {
                $('#properties_table tbody').append(`
                <tr>
                    <td>
                        <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">${options_html}</select>
                    </td>
                    <td><input type="number" name="counts[]" class="form-control" min="1" value="1" required></td>
                    <td><button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button></td>
                </tr>
            `);

            $('.js-example-basic-single').select2()
            })
            // end add property

            // remove property
            $(document).on('click','.btn_remove', function () {
                $(this).parent().parent().remove();
            })
            // end remove property

            $(document).on('change','#factor_id', function (){
                if (this.value !== '')
                {
                    let factor_id = this.value;
                    $.ajax({
                        type: 'post',
                        url: '/api/get-invoice-products',
                        data: {
                            factor_id
                        },
                        success: function (res) {
                            let url = `/panel/invoices/${res.invoice_id}?type=factor`
                            $('#factor_link a').attr('href', url)

                            if (res.missed){
                                $('#alert_section').removeClass('d-none')
                                $('#alert_section #miss_products').removeClass('d-none')
                                $('#alert_section #miss_products #codes').text(res.miss_products)
                            }else{
                                $('#alert_section').addClass('d-none')
                                $('#alert_section #miss_products').addClass('d-none')
                            }

                            if (res.other_products.length){
                                $('#alert_section').removeClass('d-none')
                                $('#alert_section #other_products').removeClass('d-none')
                                $('#alert_section #other_products #items').html('')

                                $.each(res.other_products, function (i, product) {
                                    $('#alert_section #other_products #items').append(`<li>${product.title}</li>`)
                                })
                            } else{
                                $('#alert_section').addClass('d-none')
                                $('#alert_section #other_products').addClass('d-none')
                            }

                            $('#properties_table tbody').html('')

                            $.each(res.data, function (i, product) {
                                var options_html2;

                                $.each(inventory, function (i, item) {
                                    options_html2 += `<option value="${item.id}" ${item.code === product.code ? 'selected' : ''}>${item.type} - ${item.title}</option>`
                                })

                                $('#properties_table tbody').append(`
                                    <tr>
                                        <td>
                                            <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">${options_html2}</select>
                                        </td>
                                        <td><input type="number" name="counts[]" class="form-control" min="1" value="${product.pivot.count}" required></td>
                                        <td><button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                `)
                            })

                            $('.js-example-basic-single').select2()
                        }
                    })
                }
            })
        })
    </script>
@endsection

