@extends('layouts.app')

@section('content')

    <div class="bg-white rounded-5 py-3 px-4 w-100">
        <div class="row justify-content-between">
            <div class="col-3 mb-3">
                <h2 class="fs-32">Production Reports</h2>
            </div>
            <div class="col-6 mb-3">
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
            <div class="col-12 mb-3">
                <div class="row justify-content-between">
                    <div class="col-5">
                        <div class="row">
                            <div class="col-6">
                                <label for="team_member_1">Team Member 1</label>
                                <select class="form-select" aria-label="Default select example" id="team_member_1">
                                    <option selected>All</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="team_member_2">Team Member 2</label>
                                <select class="form-select" aria-label="Default select example" id="team_member_2">
                                    <option selected>All</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4">
                                <label for="date_select">Date Range:</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-4">
                                <label for="group_select">Group By:</label>
                                <select class="form-select" aria-label="Default select example" id="group_select">
                                    <option selected>All</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="compare_based">Compare</label>
                                <select class="form-select" aria-label="Default select example" id="compare_based">
                                    <option selected>Productivity</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5">

        <div class="col-12 mt-5">
            <div class="bg-white rounded-5 px-4">
                <h2 class="p12 pt-4 fw-bold d-block">Total Orders Completed per Day by Employees</h2>
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

        <div class="col-12 col-md-6 mt-5">
            <div class="bg-white rounded-5 px-3 pt-3">
                <div id="rightLineChart"></div>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes mt-5">
            <div class="bg-white rounded-5">
                <h2 class="text-center">25</h2>
                <p class="text-center">Total Items Resent for Printing</p>
            </div>
        </div>
        <div class="col-6 col-md-3 report_small_boxes mt-5">
            <div class="bg-white rounded-5">
                <h2 class="text-center">62</h2>
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
    <script>
        var options = {
            series: [{
                name: 'Employee One',
                data: [4,6,2,8,6,9,1]
            }, {
                name: 'Employee two',
                data: [5,10,6,3,2,7,5]
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
                    text: 'Day of the Week'
                },
                categories: ['Mon','Tue','Wed','Thur','Fri','Sat','Sun'],
            },
            yaxis: {
                title: {
                    text: '# of Albums'
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

        var options2 = {
            series: [{
                name: 'All Orders',
                data: [31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'Order with Guilding',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
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
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
            colors: ['#8D87CE', '#BD7F7F']
        };

        var chart2 = new ApexCharts(document.querySelector("#avgTimeChart"), options2);
        chart2.render();

        var options3 = {
            series: [{
                name: 'All orders',
                data: [760, 850, 1010, 980, 870, 1050, 910, 1140, 940,210,970,400]
            },{
                name: 'Order with Guilding',
                data: [210,670,400,440, 550, 570, 560, 610, 580, 630, 600, 660]
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
                categories: ['Jan','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep','Oct','Nov','Dec'],
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


