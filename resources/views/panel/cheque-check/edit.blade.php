@extends('panel.layouts.master')
@section('title', 'ویرایش درخواست بررسی چک')
@section('styles')
    <style>
        table tbody tr td input{
            text-align: center;
            width: fit-content !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center mb-4">
                <h6>ویرایش درخواست بررسی چک</h6>
            </div>
            <form action="{{ route('cheque.update', $cheque->id) }}" method="post">
                @csrf
                @method('put')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان</th>
                                <th>شناسه صیادی چک</th>
                                <th>وضعیت چک</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($cheque->items) as $item)
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td class="d-flex justify-content-center"><input type="text" class="form-control" name="stats[]" value="{{ isset($item->stats) ? $item->stats : null }}" required></td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <button class="btn btn-primary mt-5" type="submit">ثبت فرم</button>
                <a href="{{route('cheque.index')}}" class="btn btn-primary mt-5" type="button">بازگشت</a>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
{{--    <script>--}}
{{--        // item changed--}}
{{--        $(document).on('keyup','input[name="prices[]"]', function () {--}}
{{--            $(this).val(addCommas($(this).val()))--}}
{{--        })--}}

{{--        function funcReverseString(str) {--}}
{{--            return str.split('').reverse().join('');--}}
{{--        }--}}

{{--        // for thousands grouping--}}
{{--        function addCommas(nStr) {--}}
{{--            // event handlers--}}
{{--            let thisElementValue = nStr--}}
{{--            thisElementValue = thisElementValue.replace(/,/g, "");--}}

{{--            let seperatedNumber = thisElementValue.toString();--}}
{{--            seperatedNumber = funcReverseString(seperatedNumber);--}}
{{--            seperatedNumber = seperatedNumber.split("");--}}

{{--            let tmpSeperatedNumber = "";--}}

{{--            j = 0;--}}
{{--            for (let i = 0; i < seperatedNumber.length; i++) {--}}
{{--                tmpSeperatedNumber += seperatedNumber[i];--}}
{{--                j++;--}}
{{--                if (j == 3) {--}}
{{--                    tmpSeperatedNumber += ",";--}}
{{--                    j = 0;--}}
{{--                }--}}
{{--            }--}}

{{--            seperatedNumber = funcReverseString(tmpSeperatedNumber);--}}
{{--            if(seperatedNumber[0] === ",") seperatedNumber = seperatedNumber.replace("," , "");--}}
{{--            return seperatedNumber;--}}
{{--        }--}}
{{--    </script>--}}
@endsection

