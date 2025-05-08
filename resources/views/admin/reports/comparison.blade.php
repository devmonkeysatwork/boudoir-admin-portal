@extends('layouts.app')
<link rel="stylesheet" href="{{asset('assets/css/date-range.css')}}">
@section('content')

    <div class="row report_page_links my-3 justify-content-center align-items-center">
        <div class="col-lg-6">
            <h1 class="fs-32">Production Reports</h1>
        </div>
        <div class="col-lg-6">
            <div class="d-flex gap-3 justify-content-end">
                <a href="{{route('dashboard.reports')}}" class="create-btn {{ request()->routeIs('dashboard.reports') ? 'active_url' : '' }}">Order Reports</a>
                <a href="{{route('dashboard.compare')}}" class="create-btn {{ request()->routeIs('dashboard.compare') ? 'active_url' : '' }}">Production Reports</a>
            </div>
        </div>
    </div>

    <div class="rounded-5 w-100">
        <div class="row justify-content-between">
{{--            <div class="col-6 mb-3">--}}
{{--                <h2 class="fs-32">Production Reports</h2>--}}
{{--            </div>--}}
{{--            <div class="col-6 mb-3">--}}
{{--                <div class="d-flex gap-3 flex-row justify-content-end align-items-center">--}}
{{--                    <button class="btn">--}}
{{--                        <img src="{{asset('icons/print.svg')}}" alt="">--}}
{{--                        Print--}}
{{--                    </button>--}}
{{--                    <button class="btn">--}}
{{--                        <img src="{{asset('icons/export.svg')}}" alt="">--}}
{{--                        Export--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="col-12 my-3">
                <form action="{{route('dashboard.compare')}}" method="GET" id="report_filter_form">
                    <div class="row justify-content-between">
                        <div class="col-5">
                            <div class="row">
                                <div class="col-6">
                                    <label for="team_member_1">Team Member 1</label>
                                    <select class="form-select" aria-label="Default select example" name="team_member_1" id="team_member_1">
                                        <option selected value="">All</option>
                                        @foreach($team_members as $team_member)
                                            <option {{isset($teamMember1) && $teamMember1 == $team_member->id?'Selected':''}} value="{{$team_member->id}}">{{$team_member->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="team_member_2">Team Member 2</label>
                                    <select class="form-select" aria-label="Default select example" name="team_member_2" id="team_member_2">
                                        <option selected value="">All</option>
                                        @foreach($team_members as $team_member)
                                            <option {{isset($teamMember2) && $teamMember2 == $team_member->id?'Selected':''}} value="{{$team_member->id}}">{{$team_member->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-4">
                                    <label for="date_select">Date Range:</label>
                                    <input type="text" id="date-range" class="form-control" name="date_range">
                                </div>
                                <div class="col-4">
                                    <label for="group_select">Group By:</label>
                                    <select class="form-select" aria-label="Default select example" id="group_select" name="group">
                                        <option {{isset($group_by) && $group_by == 'daily'?'Selected':''}} value="daily">Daily</option>
                                        <option {{isset($group_by) && $group_by == 'weekly'?'Selected':''}} value="weekly">Weekly</option>
                                        <option {{isset($group_by) && $group_by == 'monthly'?'Selected':''}} value="monthly">Monthly</option>
                                        <option {{isset($group_by) && $group_by == 'yearly'?'Selected':''}} value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label for="compare_based">Compare</label>
                                    <select class="form-select" aria-label="Default select example" id="compare_based" name="comparison">
                                        <option selected>Productivity</option>
                                        <option>Efficiency</option>
                                        <option>Quality</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row my-5">

        <div class="col-12">
            <div class="bg-white rounded-5 px-4">
                <h2 class="p12 pt-4 fw-bold d-block text-capitalize">{{$performanceData['group_by']??'Day'}} orders completed by Employees</h2>
                <div class="">
                    <div id="chart"></div>
                </div>
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

    </div>




@endsection
@section('footer_scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{asset('assets/js/date-range.js')}}"></script>
    <script src="{{asset('assets/js/apexCharts.js')}}"></script>
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
        const comparison_data = @json($performanceData??[]);
        var options = {
            series: [{
                name: '{{$employees[0]??'All'}}',
                data: comparison_data.user1.counts
            }, {
                name: '{{$employees[1]??'All'}}',
                data: comparison_data.user2.counts
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '80%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['transparent']
            },
            xaxis: {
                title:{
                    text: comparison_data.group_by + ' comparison'
                },
                categories: comparison_data.dates,
            },
            yaxis: {
                title: {
                    text: 'Orders completed'
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



        const timeData = @json($timeData??[]);
        var options2 = {
            series: [{
                name: '{{$employees[0]??'All'}}',
                data: timeData.user1.avg_time
            }, {
                name: '{{$employees[1]??'All'}}',
                data: timeData.user2.avg_time
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            title: {
                text: 'Avg Time Spent(minutes)',
                align: 'left'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: timeData.dates
            },
            colors: ['#8D87CE', '#BD7F7F']
        };

        var chart2 = new ApexCharts(document.querySelector("#avgTimeChart"), options2);
        chart2.render();

        var options3 = {
            series: [{
                name: '{{$employees[0]??'All'}}',
                data: timeData.user1.total_time
            }, {
                name: '{{$employees[1]??'All'}}',
                data: timeData.user2.total_time
            }],
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
                text: 'Total Time Spent(minutes)',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'Transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: timeData.dates,
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


        var options4 = {
            series: [{
                data: [400, 430, 480, 420]
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
                categories: ['Missing Materials', 'Resent for Printing', 'Gilding Issues', 'Binding Issues',
                ],

            },
            title: {
                text: 'Error Bar Chart',
                align: 'left'
            }
        };

        var chart4 = new ApexCharts(document.querySelector("#rightLineChart"), options4);
        chart4.render();

        var options5 = {
            series: [{
                name: 'Donatello Johnson',
                data: [45, 32, 34, 52, 41,31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'Jason Price',
                data: [11, 32, 45, 32, 34, 52, 41,65,45, 32, 34, 52]
            }, {
                name: 'Duane Dean',
                data: [41,45, 32, 34, 52, 41,52,32,43,45,23,11]
            }, {
                name: 'Jonathan Barker',
                data: [67, 45, 65,45, 32, 34, 52, 41, 23, 12, 67, 34]
            }, {
                name: 'Raphael Margerriti',
                data: [34, 65,25,45, 32, 34, 52, 21, 43, 12, 90, 56]
            }],
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
                categories: ['Jan','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep','Oct','Nov','Dec']
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


