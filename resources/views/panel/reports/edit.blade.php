@extends('panel.layouts.master')
@section('title', 'ویرایش گزارش')
@section('styles')
    <style>
        .btn_remove{
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>ویرایش گزارش</h6>
            </div>
            <div class="form-row mb-5">
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <input type="text" name="date" class="form-control date-picker-shamsi-list" id="date" value="{{ verta($report->date)->format('Y/m/d') }}" form="form" required>
                    @error('date')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <input type="text" name="item" class="form-control" id="item" placeholder="تماس با مشتری...">
                    @error('item')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 mb-3">
                    <button class="btn btn-success" id="btn_add">
                        <i class="fa fa-plus mr-2"></i>
                        افزودن
                    </button>
                </div>
                <div class="col-12 mb-3">
                    <ol id="items">
                    </ol>
                </div>
            </div>
            <form action="{{ route('reports.update', $report->id) }}" method="post" id="form">
                @csrf
                @method('put')
                <input type="hidden" name="items" id="items_input">
                <button class="btn btn-primary" type="submit">ثبت فرم</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var items = [];

        @foreach(json_decode($report->items) as $item)
            add_item('{{$item}}')
        @endforeach

        $(document).ready(function () {
            $('#btn_add').on('click', function () {
                var item = $('#item').val()
                add_item(item)
            })

            $('#item').keypress(function (e) {
                var item = $('#item').val()
                var key = e.which;
                if(key == 13)  // the enter key code
                {
                    add_item(item)
                }
            });

            // remove item
            $(document).on('click','.btn_remove', function () {
                var text = $(this).siblings()[0].innerText;
                const index = items.indexOf(text);
                items.splice(index, 1);
                $(this).parent().remove()

                $('#items_input').val(items)
            })
        })

        // add item
        function add_item(item) {
            if (item !== '' && items.includes(item) !== true)
            {
                $('#items').append(`<li>
                        <span>${item}</span>
                        <i class="fa fa-times text-danger ml-2 btn_remove" title="حذف"></i>
                    </li>`)

                $('#item').val('')

                items.push(item)

                $('#items_input').val(items)
            }
        }
    </script>
@endsection
