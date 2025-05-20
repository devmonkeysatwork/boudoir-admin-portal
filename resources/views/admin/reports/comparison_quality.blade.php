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
                <form action="{{route('dashboard.quality')}}" method="GET" id="report_filter_form">
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
                                        <option>Productivity</option>
                                        <option selected>Efficiency</option>
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
            var startDate = moment('{{$start_date}}', 'MM/DD/YYYY');
            var endDate = moment('{{$end_date}}', 'MM/DD/YYYY');
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

        const issues_count = @json($issues_count ?? []);
        const issues = @json($issues ?? []);
        const series = structureIssuesData(issues, issues_count);
        console.log(series);
        var options = {
            series: series.datasets,
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10,
                    borderRadiusApplication: 'end', // 'around', 'end'
                    borderRadiusWhenStacked: 'last', // 'all', 'last'
                    dataLabels: {
                        total: {
                            enabled: true,
                            style: {
                                fontSize: '13px',
                                fontWeight: 900
                            }
                        }
                    }
                },
            },
            xaxis: {
                type: 'category',
                categories: series.labels,
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();




        function structureIssuesData(issues, issues_per_station) {
            const issueLabels = Object.values(issues);
            const stations = Object.keys(issues_per_station);
            const datasets = issueLabels.map((label) => ({
                name: label,
                data: [],
            }));
            stations.forEach((station) => {
                const stationIssues = issues_per_station[station];

                issueLabels.forEach((label, index) => {
                    const count = stationIssues[label] || 0;
                    datasets[index].data.push(count);
                });
            });

            return {
                labels: stations,
                datasets: datasets
            };
        }
    </script>
@endsection


