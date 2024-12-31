@extends('panel.layouts.master')
@switch(request()->website)
    @case('torob')
            @php
                $title = 'محصولات ترب';
            @endphp
        @break
    @case('digikala')
            @php
                $title = 'محصولات دیجیکالا';
            @endphp
        @break
    @case('emalls')
        @php
            $title = 'محصولات ایمالز';
        @endphp
    @break
@endswitch

@section('title', $title)
@section('content')
    @if(request()->website != 'emalls')
        {{--  price history Modal  --}}
        <div class="modal fade" id="priceHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="priceHistoryModalLabel">تاریخچه قیمت</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
        {{--  end price history Modal  --}}
    @endif
    @if(request()->website == 'torob' || request()->website == 'emalls')
        {{--  avg price Modal  --}}
        <div class="modal fade" id="avgPriceModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avgPriceModalLabel">میانگین قیمت(3 روز اخیر)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
                            <i class="ti-close"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
        {{--  avg price Modal  --}}
    @endif
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h6>{{ $title }}</h6>
                <a href="{{ route('off-site-products.create', request()->website) }}" class="btn btn-primary">
                    <i class="fa fa-plus mr-2"></i>
                    ایجاد محصول
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered dataTable dtr-inline text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان محصول</th>
                        <th>تاریخ ایجاد</th>
                        <th>مشاهده قیمت فروشندگان</th>
                        @if(request()->website == 'torob' || request()->website == 'emalls')
                            <th>میانگین قیمت</th>
                        @endif
                        <th>تاریخچه قیمت</th>
                        <th>ویرایش</th>
                        <th>حذف</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ verta($item->created_at)->format('H:i - Y/m/d') }}</td>
                            <td>
                                <a class="btn btn-info btn-floating" href="{{ route('off-site-products.show', $item->id) }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                            @if(request()->website == 'torob' || request()->website == 'emalls')
                                <td>
                                    <button class="btn btn-info btn-floating btn_avg_price" data-toggle="modal" data-target="#avgPriceModal" data-id="{{ $item->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            @endif
                            <td>
                                <button class="btn btn-info btn-floating btn_price_history" data-toggle="modal" data-target="#priceHistoryModal" data-id="{{ $item->id }}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                            <td>
                                <a class="btn btn-warning btn-floating" href="{{ route('off-site-products.edit', $item->id) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-floating trashRow" data-url="{{ route('off-site-products.destroy',$item->id) }}" data-id="{{ $item->id }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center">{{ $data->appends(request()->all())->links() }}</div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var website = '{{ request()->website }}';
        var data1;
        var data2;

        var labels;
        var id;

        $(document).ready(function () {
            $('.btn_price_history').on('click', function () {
                id = $(this).data('id');

                $('#priceHistoryModal .modal-body').html(`<div class="spinner-grow text-primary"></div>`)

                switch(website)
                {
                    case 'torob':
                        torobChart();
                        break;
                    case 'digikala':
                        digikalaChart()
                        break;
                    case 'emalls':
                        emallsChart();
                        break;
                }
            })

            $('.btn_avg_price').on('click', function () {
                id = $(this).data('id');

                $('#avgPriceModal .modal-body').html(`<div class="spinner-grow text-primary"></div>`)

                $.ajax({
                    url: `/panel/avg-price/${website}/${id}`,
                    type: 'get',
                    success: function (res) {
                        $('#avgPriceModal .modal-body').html(`<h4>${res}</h4>`)
                    }
                });
            })

            function torobChart() {
                $.ajax({
                    url: `/panel/off-site-product-history/${website}/${id}`,
                    type: 'get',
                    success: function (res) {
                        $('#priceHistoryModal .modal-body').html(`<canvas id="line_chart" style="width: auto"></canvas>`)
                        data1 = res.data.dataSets[0].entries.map(d => d['val']);
                        data2 = res.data.dataSets[1].entries.map(d => d['val']);
                        labels = res.data.labels;

                        // price history chart
                        var element1 = document.getElementById("line_chart");
                        element1.height = 146;
                        new Chart(element1, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: "میانگین قیمت",
                                        backgroundColor: '#00c852',
                                        data: data1,
                                        borderColor: '#00c852',
                                        fill: false,
                                        cubicInterpolationMode: 'monotone',
                                        tension: 0.4
                                    }, {
                                        label: "کمترین قیمت",
                                        backgroundColor: '#0091ea',
                                        data: data2,
                                        borderColor: '#0091ea',
                                        fill: false,
                                        cubicInterpolationMode: 'monotone',
                                        tension: 0.4
                                    }
                                ]
                            },

                            options: {
                                responsive: true,
                                scales: {
                                    xAxes: [{
                                        display: false
                                    }],
                                    yAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'تومان',
                                            fontSize: 18
                                        },
                                        ticks: {
                                            min: 0,
                                            fontSize: 15,
                                            fontColor: '#999',
                                            callback: function(value, index, values) {
                                                const options = { style: 'decimal', useGrouping: true };
                                                const formattedNumber = value.toLocaleString('en-US', options);
                                                return formattedNumber;
                                            }
                                        },
                                        gridLines: {
                                            color: '#e8e8e8',
                                        }
                                    }],
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                            var formattedValue = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                            return formattedValue + ' تومان ';
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                },
                                elements: {
                                    point:{
                                        radius: 1
                                    }
                                }
                            },
                        })
                        // end price history chart
                    }
                })
            }

            function digikalaChart() {
                $.ajax({
                    url: `/panel/off-site-product-history/${website}/${id}`,
                    type: 'get',
                    success: function (res) {
                        $('#priceHistoryModal .modal-body').html(`<canvas id="line_chart" style="width: auto"></canvas>`)
                        data1 = res.data.data.price_chart[0].history.map(d => d['selling_price']);
                        labels = res.data.data.price_chart[0].history.map(d => d['day']);

                        // price history chart
                        var element1 = document.getElementById("line_chart");
                        element1.height = 146;
                        new Chart(element1, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: "کمترین قیمت",
                                        backgroundColor: '#0091ea',
                                        data: data1,
                                        borderColor: '#0091ea',
                                        fill: false,
                                        cubicInterpolationMode: 'monotone',
                                        tension: 0.4
                                    }
                                ]
                            },

                            options: {
                                responsive: true,
                                legend: {
                                    display: false
                                },
                                scales: {
                                    xAxes: [{
                                        display: false
                                    }],
                                    yAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'تومان',
                                            fontSize: 18
                                        },
                                        ticks: {
                                            min: 0,
                                            fontSize: 15,
                                            fontColor: '#999',
                                            callback: function(value, index, values) {
                                                const options = { style: 'decimal', useGrouping: true };
                                                const formattedNumber = (value * 0.1).toLocaleString('en-US', options);
                                                return formattedNumber;
                                            }
                                        },
                                        gridLines: {
                                            color: '#e8e8e8',
                                        }
                                    }],
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                            var formattedValue = (value * 0.1).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                            return formattedValue + ' تومان ';
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                },
                                elements: {
                                    point:{
                                        radius: 1
                                    }
                                }
                            },
                        })
                        // end price history chart
                    }
                })
            }

            function emallsChart() {
                $.ajax({
                    url: `/panel/off-site-product-history/${website}/${id}`,
                    type: 'get',
                    success: function (res) {
                        window.open(`https://emalls.ir/chartshow.aspx?id=${res.data}`);
                    }
                })
            }
        })
    </script>
    @if (\Session::has('error'))
        <script>
            let timerInterval;
            Swal.fire({
                title: "{{Session::get('error')}}",
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("I was closed by the timer");
                }
            });
        </script>
    @endif

@endsection
