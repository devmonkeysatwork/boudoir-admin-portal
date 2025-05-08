@extends('layouts.app')
<link rel="stylesheet" href="{{asset('assets/css/date-range.css')}}">
@section('content')

    <div class="row report_page_links my-3 justify-content-center align-items-center">
        <div class="col-lg-6">
            <h1 class="fs-32">Order Reports <span class="fw-normal p12">- Show Insights from <b>This Month</b></span></h1>
        </div>
        <div class="col-lg-6">
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{route('dashboard.reports')}}" class="create-btn {{ request()->routeIs('dashboard.reports') ? 'active_url' : '' }}">Order Reports</a>
                <a href="{{route('dashboard.compare')}}" class="create-btn {{ request()->routeIs('dashboard.compare') ? 'active_url' : '' }}">Production Reports</a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-5 py-3 px-4 w-100">
        <form action="{{route('dashboard.reports')}}" method="GET" id="report_filter_form">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-3">
                            <label for="product_select">Product</label>
                            <select class="form-select" name="product_name" aria-label="Default select example" id="product_select">
                                <option selected value="0">All</option>
                                @foreach($products as $product)
                                    <option {{isset($productName) && $productName == $product->id?'Selected':''}} value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="status_select">Status</label>
                            <select class="form-select" name="status" aria-label="Default select example" id="status_select">
                                <option selected value="0">All</option>
                                @foreach($statuses as $status)
                                    <option {{isset($status_id) && $status_id == $status->id?'Selected':''}} value="{{$status->id}}">{{$status->status_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <div class="d-flex gap-3 flex-row justify-content-end align-items-center">
                                <button class="btn">
                                    <img src="{{asset('icons/print.svg')}}" alt="">
                                    Print
                                </button>
                                <button class="btn">
                                    <img src="{{asset('icons/export.svg')}}" alt="">
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col">
                            <label for="product_attribute_select">Product Option:</label>
                            <select class="form-select" name="product_attribute" aria-label="Default select example" id="product_attribute_select">
                                <option selected value="0">All</option>
                                @foreach($attributes as $attribut)
                                    <option {{isset($productAttribute) && $productAttribute ==$attribut->id ?'Selected':''}} value="{{$attribut->id}}">{{$attribut->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="attribute_select">Attributes:</label>
                            <select class="form-select" name="attribute" aria-label="Default select example" id="attribute_select">
                                <option selected value="0">All</option>
                                @foreach($attributesValues as $attributesValue)
                                    <option {{isset($attribute) && $attribute ==$attributesValue->id ?'Selected':''}} value="{{$attributesValue->id}}">{{$attributesValue->value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="team_select">Team Member:</label>
                            <select class="form-select" name="team_member" aria-label="Default select example" id="team_select">
                                <option selected value="0">All</option>
                                @foreach($team_members as $team_member)
                                    <option {{isset($teamMember) && $teamMember == $team_member->id?'Selected':''}} value="{{$team_member->id}}">{{$team_member->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label for="date_select">Date Range:</label>
                            <input type="text" id="date-range" name="date_range" class="form-control">
                        </div>
                        {{--                        <div class="col">--}}
                        {{--                            <label for="group_select">Group By:</label>--}}
                        {{--                            <select class="form-select" name="group_by" aria-label="Default select example" id="group_select">--}}
                        {{--                                <option selected>All</option>--}}
                        {{--                            </select>--}}
                        {{--                        </div>--}}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row my-5">
        <div class="col-6 col-md-3 report_small_boxes">
            <div class="bg-white rounded-5">
                <h2 class="text-center">{{$orders_completed}}</h2>
                <p class="text-center">Total Orders Completed</p>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes">
            <div class="bg-white rounded-5">
                <h2 class="text-center">{{$total_time_spent}} min</h2>
                <p class="text-center">Total Time Spent on Orders</p>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes">
            <div class="bg-white rounded-5">
                <div role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="80" style="--value: 80">80%</div>
                <p class="text-center mt-2">of All Gilded Orders</p>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes">
            <div class="bg-white rounded-5">
                <h2 class="text-center">{{round($avg_time_spent_on_order,2)}} min</h2>
                <p class="text-center">Average Time per Order</p>
            </div>
        </div>

        <div class="col-12 mt-5">
            <div class="bg-white rounded-5 px-4">
                <div id="chart"></div>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-5">
            <div class="bg-white rounded-5 p-4">
                <div id="lineChart"></div>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-5">
            <div class="bg-white rounded-5 p-4">
                <div id="avgTimeChart"></div>
            </div>
        </div>

        <div class="col-12 col-md-6 mt-5">
            <div class="bg-white rounded-5 px-3 pt-3">
                <div id="rightLineChart"></div>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes mt-5">
            <div class="bg-white rounded-5">
                <h2 class="text-center text-danger">{{$reprinting_orders[1]??0}}</h2>
                <p class="text-center">Total Items Resent for Printing</p>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes mt-5">
            <div class="bg-white rounded-5">
                <h2 class="text-center text-danger">{{$error_count_per_user['totalErrors']??0}}</h2>
                <p class="text-center">Total Orders with Errors</p>
            </div>
        </div>

        <div class="col-12 mt-5">
            <div class="bg-white rounded-5 p-4">
                <div id="errCountChart"></div>
            </div>
        </div>

    </div>


@endsection
@section('footer_scripts')
    <script src="{{asset('assets/js/apexCharts.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('assets/js/date-range.js')}}"></script>
    <script>
        $(document).ready(function() {
            var dateRangeStr = '{{$date_range??""}}';
            if (dateRangeStr) {
                var dates = dateRangeStr.split(' - ');
                var startDate = moment(dates[0], 'MM/DD/YYYY');
                var endDate = moment(dates[1], 'MM/DD/YYYY');
            } else {
                var startDate = moment().startOf('day');
                var endDate = moment().endOf('day');
            }
            $('#date-range').daterangepicker({
                opens: 'left',
                startDate: startDate,
                endDate: endDate,
            }, function(start, end, label) {
                $(this).closest('form').submit();
            });
        });
        $('#report_filter_form select').on('change',function (){
            $(this).closest('form').submit();
        })

        const all_orders_data = @json($orders_graph_monthly['total_orders'] ?? []);
        const album_orders_data = @json($orders_graph_monthly['orders_with_album'] ?? []);
        const months = @json($orders_graph_monthly['months'] ?? []);

        var options = {
            series: [{
                name: 'All orders',
                data: all_orders_data
            }, {
                name: 'Orders with {{$selected_product}}',
                data: album_orders_data
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: months,
            },
            yaxis: {
                title: {
                    text: 'orders'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            colors: ['#8D87CE', '#BD7F7F'],
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();


        const avg_time_spent = @json($time_graph_data['avg_time_spent'] ?? []);
        var options2 = {
            series: [{
                name: 'Avg time spent',
                data: avg_time_spent
            },
                // {
                //     name: 'Order with Guilding',
                //     data: [11, 32, 45, 32, 34, 52, 41]
                // }
            ],
            chart: {
                height: 350,
                type: 'area'
            },
            title: {
                text: 'Avg Time Spent',
                align: 'left'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'date',
                categories: months
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy'
                },
            },
            colors: ['#8D87CE', '#BD7F7F']
        };

        var chart2 = new ApexCharts(document.querySelector("#avgTimeChart"), options2);
        chart2.render();


        const total_time_spent = @json($time_graph_data['total_time_spent'] ?? []);
        var options3 = {
            series: [{
                name: 'Total time spent',
                data: total_time_spent
            },
                //     {
                //     name: 'Order with Guilding',
                //     data: [210,670,400,440, 550, 570, 560, 610, 580, 630, 600, 660]
                // }
            ],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Total Time Spent',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'Transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: months,
                lines: {
                    show: true // Enable x-axis lines
                }
            },
            yaxis: {
                lines: {
                    show: true // Enable y-axis lines
                }
            },
            colors: ['#8D87CE', '#BD7F7F']
        };

        var chart3 = new ApexCharts(document.querySelector("#lineChart"), options3);
        chart3.render();

        const order_with_issues = @json($reprinting_orders ?? []);

        var options4 = {
            series: [{
                name:'',
                data: order_with_issues
            }],
            chart: {
                type: 'bar',
                height: 160
            },
            plotOptions: {
                bar: {
                    borderRadius: 0,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: ['On Hold', 'Issues With Printing', 'Remake + Reasons'],

            },
            title: {
                text: 'Error Bar Chart',
                align: 'left'
            }
        };

        var chart4 = new ApexCharts(document.querySelector("#rightLineChart"), options4);
        chart4.render();

        const errorData = @json($error_count_per_user ?? []);
        const seriesData = (errorData.users || []).map((name, idx) => {
            return {
                name: name,
                data: errorData.data[idx] || []
            };
        });

        var options5 = {
            series: seriesData,
            chart: {
                height: 350,
                type: 'area'
            },
            title: {
                text: 'Error Count',
                align: 'left'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            xaxis: {
                categories: errorData.months
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
            colors: ['#8D87CE', '#BD7F7F','#8D87CE','#ABFB77','#BD7F7F']
        };

        var chart5 = new ApexCharts(document.querySelector("#errCountChart"), options5);
        chart5.render();

    </script>
@endsection


