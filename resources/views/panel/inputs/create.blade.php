@extends('panel.layouts.master')
@section('title', 'ثبت ورودی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ثبت ورودی</h6>
                <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
            </div>
            <form action="{{ route('inventory-reports.store') }}" method="post">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <input type="hidden" name="type" value="{{ request()->type }}">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="person"> تحویل دهنده <span class="text-danger">*</span></label>
                            <input type="text" name="person" class="form-control" id="person" value="{{ old('person') }}">
                            @error('person')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="input_date"> تاریخ ورود <span class="text-danger">*</span></label>
                            <input type="text" name="input_date" class="form-control date-picker-shamsi-list" id="input_date" value="{{ old('input_date') ?? verta()->format('Y/m/d') }}">
                            @error('input_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center" id="properties_table">
                                <thead>
                                <tr>
                                    <th>کالا</th>
                                    <th>تعداد</th>
                                    <th>حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($errors->any())
                                        @foreach(old('inventory_id') as $key => $inventory_id)
                                            <tr>
                                                <td>
                                                    <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                                        @foreach(\App\Models\Inventory::where('warehouse_id',$warehouse_id)->get(['id','title','type']) as $item)
                                                            <option value="{{ $item->id }}" {{ $inventory_id == $item->id ? 'selected' : '' }}>{{ \App\Models\Inventory::TYPE[$item->type].' - '.$item->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="counts[]" class="form-control" min="1" value="{{ old('counts')[$key] }}" required>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select class="js-example-basic-single select2-hidden-accessible" name="inventory_id[]">
                                                    @foreach(\App\Models\Inventory::where('warehouse_id',$warehouse_id)->get(['id','title','type']) as $item)
                                                        <option value="{{ $item->id }}">{{ \App\Models\Inventory::TYPE[$item->type].' - '.$item->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="counts[]" class="form-control" min="1" value="1" required>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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
        var inventory = [];

        var options_html;

        @foreach(\App\Models\Inventory::where('warehouse_id',$warehouse_id)->get(['id','title','type']) as $item)
            inventory.push({
                "id": "{{ $item->id }}",
                "title": "{{ $item->title }}",
                "type": "{{ \App\Models\Inventory::TYPE[$item->type] }}",
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
        })
    </script>
@endsection

