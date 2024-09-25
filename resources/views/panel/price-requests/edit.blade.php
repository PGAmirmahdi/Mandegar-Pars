@extends('panel.layouts.master')
@section('title', 'ثبت قیمت')
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
                <h6>ثبت قیمت</h6>
            </div>
            <form action="{{ route('price-requests.update', $priceRequest->id) }}" method="post">
                @csrf
                @method('put')
                <div class="form-row">
                    <div class="col-12 mb-3">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="bg-primary">
                            <tr>
                                <th>عنوان کالا</th>
                                <th>تعداد</th>
                                <th>قیمت (تومان)</th>
                                <th>شامل ارزش افزوده</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($priceRequest->items) as $index => $item)
                                <tr>
                                    <td>{{ $item->product }}</td>
                                    <td>{{ $item->count }}</td>
                                    <td class="d-flex justify-content-center">
                                        <input type="text" class="form-control" name="prices[{{ $index }}]" value="{{ isset($item->price) ? number_format($item->price) : 0 }}" required>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="vat_included[{{ $index }}]" {{ isset($item->vat_included) && $item->vat_included ? 'checked' : '' }}>
                                    </td>
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
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // item changed
        $(document).on('keyup','input[name="prices[]"]', function () {
            $(this).val(addCommas($(this).val()))
        })

        function funcReverseString(str) {
            return str.split('').reverse().join('');
        }

        // for thousands grouping
        function addCommas(nStr) {
            // event handlers
            let thisElementValue = nStr
            thisElementValue = thisElementValue.replace(/,/g, "");

            let seperatedNumber = thisElementValue.toString();
            seperatedNumber = funcReverseString(seperatedNumber);
            seperatedNumber = seperatedNumber.split("");

            let tmpSeperatedNumber = "";

            j = 0;
            for (let i = 0; i < seperatedNumber.length; i++) {
                tmpSeperatedNumber += seperatedNumber[i];
                j++;
                if (j == 3) {
                    tmpSeperatedNumber += ",";
                    j = 0;
                }
            }

            seperatedNumber = funcReverseString(tmpSeperatedNumber);
            if(seperatedNumber[0] === ",") seperatedNumber = seperatedNumber.replace("," , "");
            return seperatedNumber;
        }
    </script>
@endsection
