@extends('panel.layouts.master')
@section('title', 'ویرایش ورودی')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش ورودی</h6>
                <button class="btn btn-outline-success" type="button" id="btn_add"><i class="fa fa-plus mr-2"></i> افزودن کالا</button>
            </div>
            <form action="{{ route('inventory-reports.update', $inventoryReport->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="{{ request()->type }}">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12">
                        <div class="form-group">
                            <label for="person"> طرف حساب <span class="text-danger">*</span></label>
                            <input type="text" name="person" class="form-control" id="person" value="{{ $inventoryReport->person }}">
                            @error('person')
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
                                    @foreach($inventoryReport->in_outs as $item)
                                        <tr>
                                            <td>
                                                <select class="form-control" name="inventory_id[]">
                                                    @foreach(\App\Models\Inventory::all(['id','title']) as $inventory)
                                                        <option value="{{ $inventory->id }}" {{ $item->inventory_id == $inventory->id ? 'selected' : '' }}>{{ $inventory->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="counts[]" class="form-control" min="0" value="{{ $item->count }}" required>
                                            </td>
                                            <td>
                                                <button class="btn btn-danger btn-floating btn_remove" type="button"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

        @foreach(\App\Models\Inventory::all('id','title') as $item)
        inventory.push({
            "id": "{{ $item->id }}",
            "title": "{{ $item->title }}",
        })
        @endforeach

        $.each(inventory, function (i, item) {
            options_html += `<option value="${item.id}">${item.title}</option>`
        })

        $(document).ready(function () {
            // add property
            $('#btn_add').on('click', function () {
                $('#properties_table tbody').append(`
                <tr>
                    <td>
                        <select class="form-control" name="inventory_id[]">${options_html}</select>
                    </td>
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
        })
    </script>
@endsection

