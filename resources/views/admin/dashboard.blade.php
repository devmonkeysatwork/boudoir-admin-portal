@extends('layouts.app')

@section('content')
    <div class="dashboard">
        <div class="d-flex flex-row justify-content-between align-items-center">
            <h1>Dashboard</h1>
        </div>
        @if(Auth::user()->role_id == 1)
            <div class="stats row">
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-pending.png') }}" alt="Ready for Print">
                            <div class="stat-info">
                                <h3>{{ $readyForPrintOrdersCount }}</h3>
                                <p>Ready for Production</p>
                            </div>
                        </div>
                        <div class="stat-btm">
                            @isset($percentageChange)
                                @if($percentageChange['readyForPrint'] > 0)
                                    <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
                                    <p><span class="stat-up">{{$percentageChange['readyForPrint']}}%</span> Up from yesterday</p>
                                @elseif($percentageChange['readyForPrint'] < 0)
                                    <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
                                    <p><span class="stat-down">{{abs($percentageChange['readyForPrint'])}}%</span> Down from yesterday</p>
                                @endif
                            @endisset
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-in-production.png') }}" alt="in Production">
                            <div class="stat-info">
                                <h3>{{ $inProductionOrdersCount }}</h3>
                                <p>In Production</p>
                            </div>
                        </div>
                        <div class="stat-btm">
                            @isset($percentageChange)
                                @if($percentageChange['production'] > 0)
                                    <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
                                    <p><span class="stat-up">{{$percentageChange['production']}}%</span> Up from yesterday</p>
                                @elseif($percentageChange['production'] < 0)
                                    <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
                                    <p><span class="stat-down">{{abs($percentageChange['production'])}}%</span> Down from yesterday</p>
                                @endif
                            @endisset
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-on-hold.png') }}" alt="on Hold">
                            <div class="stat-info">
                                <h3>{{ $onHoldOrdersCount }}</h3>
                                <p>On Hold</p>
                            </div>
                        </div>
                        <div class="stat-btm">
                            @isset($percentageChange)
                                @if($percentageChange['onHold'] > 0)
                                    <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
                                    <p><span class="stat-up">{{$percentageChange['onHold']}}%</span> Up from yesterday</p>
                                @elseif($percentageChange['onHold'] < 0)
                                    <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
                                    <p><span class="stat-down">{{abs($percentageChange['onHold'])}}%</span> Down from yesterday</p>
                                @endif
                            @endisset
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-ready.png') }}" alt="Ready">
                            <div class="stat-info">
                                <h3>{{ $readyToShipOrdersCount }}</h3>
                                <p>Ready to Ship</p>
                            </div>
                        </div>
                        <div class="stat-btm">
                            @if($percentageChange['readyToShip'] > 0)
                                <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
                                <p><span class="stat-up">{{$percentageChange['readyToShip']}}%</span> Up from yesterday</p>
                            @elseif($percentageChange['readyToShip'] < 0)
                                <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
                                <p><span class="stat-down">{{abs($percentageChange['readyToShip'])}}%</span> Down from yesterday</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/quality-control.png') }}" alt="Quality Control">
                            <div class="stat-info">
                                <h3>{{ $qualityControlOrdersCount }}</h3>
                                <p>in Quality Control</p>
                            </div>
                        </div>
                        <div class="stat-btm">
                            @if($percentageChange['qualityControl'] > 0)
                                <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
                                <p><span class="stat-up">{{$percentageChange['qualityControl']}}%</span> Up from yesterday</p>
                            @elseif($percentageChange['qualityControl'] < 0)
                                <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
                                <p><span class="stat-down">{{abs($percentageChange['qualityControl'])}}%</span> Down from yesterday</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="stats row">
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-pending.png') }}" alt="Ready for Print">
                            <div class="stat-info">
                                <h3>{{$ordersInQueue}}</h3>
                                <p>Orders in queue</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-in-production.png') }}" alt="in Production">
                            <div class="stat-info">
                                <h3>0</h3>
                                <p>Issues/On Hold</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-on-hold.png') }}" alt="on Hold">
                            <div class="stat-info">
                                <h3>0</h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card rounded-5 mt-3" id="active-orders-container">
            <div class="card-header">
                <h2 class="h24">My Orders In Progress</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(isset($orderLog) && $orderLog->isNotEmpty())
                        @foreach($orderLog as $activeOrder)
                            @if(isset($activeOrder->order->id))
                                <div class="col-12 border-bottom mb-2 pb-2">
                                    <div class="row">
                                        <div class="col-6">
                                            <button class="p12 fw-bold my-3 edit-btn" onclick="viewDetails('{{ $activeOrder->order->id }}','{{ $activeOrder->order_id }}')">
                                                Order #{{ $activeOrder->order_id }} —
                                                @php
                                                    $dateStarted = \Carbon\Carbon::parse($activeOrder->time_started);
                                                    $now = \Carbon\Carbon::now();
                                                    $workingTime = calculateWorkingTime($dateStarted, $now);
                                                @endphp

                                                {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                                                {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                                                {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                                                {{ $workingTime['minutes'] . 'm' }}
                                            </button>
                                        </div>
                                        <div class="col-6 text-end">
                                            @if(Auth::user()->role_id == 1)
                                                <button class="edit-btn" {{$activeOrder->status_id == $waitingId ? 'disabled':''}} onclick="editStatus(this)" data-id="{{$activeOrder->order->id}}" data-status="{{$activeOrder->order->status_id}}" data-workstation="{{$activeOrder->order->workstation_id}}">
                                                    <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon" width="20px">
                                                </button>
                                            @endif
                                            @if($activeOrder->status_id == $waitingId)
                                                <button class="btn btn-primary create-btn ms-2 btn-complete-phase" onclick="endOrderPhase({{ $activeOrder->id }},'{{ $activeOrder->order_id }}')">
                                                    End Waiting
                                                </button>
                                            @else
                                                <button class="btn btn-primary create-btn ms-2 btn-complete-phase" onclick="endOrderPhase({{ $activeOrder->id }},'{{ $activeOrder->order_id }}')">
                                                    Current Station Complete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="col-12">
                            <p class="p14 text-center">There are no orders currently in progress</p>
                        </div>
                    @endif
                    <div class="col-12 text-center">
                        <button type="button" class="btn text-white create-btn" id="startRandomOrder">
                            Start Another Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="orders" id="orders_table_db">
            <div class="orders-top">
                <h2>List of Orders</h2>
                <div class="orders-filter">
                    <div class="orders-search">
                        <input type="text" id="searchInput" placeholder="Search">
                        <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
                    </div>
                    <div class="sort-dropdown">
                        <select class="sort-select form-select pe-5" id="order_sort">
                            <option value="" disabled selected>Sort By</option>
                            <option {{$filter_date && $filter_date =='oldest'?'selected':''}} value="oldest">Oldest</option>
                            <option {{$filter_date && $filter_date =='newest'?'selected':''}} value="newest">Newest</option>
                        </select>
                        @if(Auth::user()->role_id == 1)
                            <select class="sort-select form-select pe-5" id="order_filter">
                                <option value="" disabled selected>Filter By</option>
                                <option {{$filter_by_time && $filter_by_time =='day'?'selected':''}} value="day">This Day</option>
                                <option {{$filter_by_time && $filter_by_time =='week'?'selected':''}} value="week">This Week</option>
                                <option {{$filter_by_time && $filter_by_time =='month'?'selected':''}} value="week">This Month</option>
                                <option {{$filter_by_time && $filter_by_time =='year'?'selected':''}} value="year">This Year</option>
                            </select>
                        @endif
                    </div>
                </div>
            </div>
            <div id="orders_table_container">
                <table id="dashboardOrdersTable" class="tablesorter">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Phase</th>
                        <th>Team Member</th>
                        <th>Date Added</th>
                        <th>Date Started</th>
                        <th>Time in Production</th>
                        <th>Late</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody id="ordersBody">
                    @php
                        $all_active_ids = $orderLog->pluck('order_id')->toArray();
                    @endphp


                    @if(isset($orders) && count($orders))
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    @if($order->is_rush)
                                        <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                                    @endif
                                    <button class="edit-btn" onclick="viewDetails('{{ $order->id }}','{{ $order->order_id }}')">
                                        {{ $order->order_id }}
                                    </button>
                                </td>
                                <td>
                                  <span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">
                                     @if($order->last_log?->sub_status)
                                          {{$order->last_log->sub_status->name}}
                                      @elseif($order->last_log?->status && $order->last_log->status_id === $order->status_id)
                                          {{$order->last_log->status->status_name}}
                                      @else
                                          {{$order->status?->status_name ?? null}}
                                      @endif
                                  </span>
                                </td>
                                <td>
                                    @if(isset($order->last_log->user))
                                        {{$order->last_log?->user?->name ?? null}}
                                    @else
                                        {{$order->station?->worker?->name ?? null}}
                                    @endif
                                </td>
                                <td>{{$order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('M d, Y') : '-'}}</td>
                                <td>{{$order->date_started ? \Carbon\Carbon::parse($order->date_started)->format('M d, Y') : '-'}}</td>                                <td>
                                    @php
                                        $dateStarted = \Carbon\Carbon::parse($order->date_started);
                                        $now = \Carbon\Carbon::now();
                                        $workingTime = calculateWorkingTime($dateStarted, $now,$order->order_id, $waitingId ?? null,$order->has_remake);
                                    @endphp

                                    {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                                    {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                                    {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                                    {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
                                </td>
                                <td data-order="@if(isset($order->deadline)) {{\Carbon\Carbon::parse($order->deadline)->timestamp}} @else 0 @endif">
                                    @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                                        @php
                                            $workingHoursLate = calculateWorkingHoursLate($order->deadline);
                                        @endphp
                                        <span class="fw-bold text-danger">{{ $workingHoursLate }} hours late</span>
                                    @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                                        @php
                                            $workingHoursLeft = calculateWorkingHoursLate(\Carbon\Carbon::now(), $order->deadline);
                                        @endphp
                                        <span class="fw-bold text-warning">{{ $workingHoursLeft }} hours left</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->role_id == 1)
                                        <button class="edit-btn" {{isset($order->last_log) && !isset($order->last_log->time_end) && $order->last_log->status_id == $waitingId ? 'disabled':''}} onclick="editStatus(this)" data-id="{{$order->id}}" data-status="{{$order->status_id}}" data-workstation="{{$order->workstation_id}}">
                                            <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon" width="20px">
                                        </button>
                                    @endif

{{--                                    @if(isset($all_active_ids) && is_array($all_active_ids) && in_array($order->order_id,$all_active_ids))--}}
{{--                                        <button type="button" class="btn bg-transparent ms-2" onclick="endOrderPhase()">--}}
{{--                                            <img src="{{ asset('icons/complete_order.svg') }}" alt="Complete Icon" width="20px">--}}
{{--                                        </button>--}}
{{--                                    @else--}}
                                        <button data-id="{{$order->order_id}}" {{isset($order->last_log) && !isset($order->last_log->time_end) && $order->last_log->status_id == $waitingId ? 'disabled':''}} type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">
                                            Start order
                                        </button>
{{--                                    @endif--}}
                                        @if($order->has_remake)
                                            <img src="{{ asset('icons/redo.svg') }}" alt="Remake Icon" width="20px">
                                        @endif
                                </td>
                            </tr>
{{--                            @if(isset($order) && isset($order->activeChildren) && count($order->activeChildren))--}}
{{--                                @foreach($order?->activeChildren as $child_order)--}}
{{--                                    <tr id="children_{{$order->id}}">--}}
{{--                                        <td>--}}
{{--                                            @if($child_order->is_rush == 1)--}}
{{--                                                <img src="{{asset('icons/rush.svg')}}" alt="Rush">--}}
{{--                                            @endif--}}
{{--                                            <button class="edit-btn" onclick="viewDetails('{{$child_order->id}}','{{$child_order->order_id}}')">--}}
{{--                                                {{ $child_order->order_id }}--}}
{{--                                            </button>--}}
{{--                                        </td>--}}
{{--                                        <td><span class="status" style="background-color: {{$child_order->status?->status_color ?? 'transparent'}}">--}}
{{--                                        @if(isset($child_order->last_log->sub_status))--}}
{{--                                                    {{$child_order->last_log?->sub_status?->name ?? null}}--}}
{{--                                                @elseif(isset($child_order->last_log->status))--}}
{{--                                                    {{$child_order->last_log?->status?->status_name ?? null}}--}}
{{--                                                @else--}}
{{--                                                    {{$child_order->status?->status_name ?? null}}--}}
{{--                                                @endif--}}
{{--                                            </span>--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            @if(isset($child_order->last_log->user))--}}
{{--                                                {{$child_order->last_log?->user?->name ?? null}}--}}
{{--                                            @else--}}
{{--                                                {{$child_order->station?->worker?->name ?? null}}--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
{{--                                        <td>{{$child_order->date_started}}</td>--}}
{{--                                        <td>--}}
{{--                                            @php--}}
{{--                                                $dateStarted = \Carbon\Carbon::parse($order->created_at);--}}
{{--                                                $now = \Carbon\Carbon::now();--}}
{{--                                                $workingTime = calculateWorkingTime($dateStarted, $now,$child_order->order_id, $waitingId ?? null,$child_order->has_remake);;--}}
{{--                                            @endphp--}}

{{--                                            {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}--}}
{{--                                            {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}--}}
{{--                                            {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}--}}
{{--                                            {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            @if(isset($child_order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($child_order->deadline)))--}}
{{--                                                <img src="{{asset('icons/exclaimatio.svg')}}" alt="">--}}
{{--                                            @elseif(isset($child_order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($child_order->deadline)->subDays(2)))--}}
{{--                                                <span class="fw-bold text-warning">{{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($child_order->deadline)),0)}} hours left</span>--}}
{{--                                            @else--}}
{{--                                                ---}}
{{--                                            @endif--}}
{{--                                        </td>--}}
{{--                                        <td>--}}
{{--                                            @if(Auth::user()->role_id == 1)--}}
{{--                                                <button class="edit-btn" onclick="editStatus(this)" data-id="{{$child_order->id}}" data-status="{{$child_order->status_id}}" data-workstation="{{$child_order->workstation_id}}">--}}
{{--                                                    <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon" width="20px">--}}
{{--                                                </button>--}}
{{--                                            @endif--}}
{{--                                            <button data-id="{{$child_order->order_id}}" {{isset($child_order->last_log) && !isset($child_order->last_log->time_end) && $child_order->last_log->status_id == $waitingId ? 'disabled':''}} type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">--}}
{{--                                                Start order--}}
{{--                                            </button>--}}
{{--                                            @if($child_order->has_remake)--}}
{{--                                                <img src="{{ asset('icons/redo.svg') }}" alt="Remake Icon" width="20px">--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
{{--            <div class="row justify-content-end" id="order_paginations">--}}
{{--                <div class="col-6 text-start">--}}
{{--                    @if($orders->count())--}}
{{--                        <p class="py-4 mb-0">--}}
{{--                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}--}}
{{--                        </p>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="col-6 text-end">--}}
{{--                    {{ $orders->appends([--}}
{{--                        'filter_date' => request()->input('filter_date'),--}}
{{--                    ])->links()--}}
{{--                    }}--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        @if(Auth::user()->role_id == 1)
            <div class="team-workstations">
                <div class="team">
                    <div class="team-top">
                        <h2>Team</h2>
                        <div class="sort-dropdown">
                            <select class="sort-select form-select pe-5" name="team_sort" id="team-sort">
                                <option>Sort</option>
                                <option {{$team_sort && $team_sort =='day'?'selected':''}} value="day">Day</option>
                                <option {{$team_sort && $team_sort =='week'?'selected':''}} value="week">Week</option>
                                <option {{$team_sort && $team_sort =='month'?'selected':''}} value="month">Month</option>
                                <option {{$team_sort && $team_sort =='year'?'selected':''}} value="year">Year</option>
                            </select>
                        </div>
                    </div>
                    <table id="dashboardTeamTable" class="tablesorter">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th># of Orders</th>
                            <th>Time Spent(hours)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($teamMembers as $teamMember)
                            <tr onclick="loadTeamDetails({{ $teamMember['id'] }},'{{$teamMember['user_name']}}')">
                                <td>{{ $teamMember['user_name'] }}</td>
                                <td>{{ $teamMember['order_count'] }}</td>
                                <td>
                                    @php
                                        $hours = floor($teamMember['total_minutes'] / 60);
                                        $mins = $teamMember['total_minutes'] % 60;
                                        echo $hours > 0 ? $hours . 'h ' : '';
                                        echo $mins > 0 ? $mins . 'm' : ($hours == 0 ? '0m' : '');
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="workstations">
                    <div class="workstations-top">
                        <h2>Areas</h2>
                        <div class="sort-dropdown">
                            <select class="sort-select form-select pe-5" name="workstation_sort" id="workstation-sort">
                                <option>Sort</option>
                                <option {{$workstation_sort && $workstation_sort =='day'?'selected':''}} value="day">Day</option>
                                <option {{$workstation_sort && $workstation_sort =='week'?'selected':''}} value="week">Week</option>
                                <option {{$workstation_sort && $workstation_sort =='month'?'selected':''}} value="month">Month</option>
                                <option {{$workstation_sort && $workstation_sort =='year'?'selected':''}} value="year">Year</option>
                            </select>
                        </div>
                    </div>
                    <table id="dashboardWorkstationsTable" class="tablesorter">
                        <thead>
                        <tr>
                            <th>Area Name</th>
                            <th># of Orders</th>
                            <th>Total time(hours)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($workstations as $workstation)
                            <tr onclick="loadWorkstationDetails({{ $workstation->id }},'{{$workstation->status_name}}')">
                                <td>{{ $workstation->status_name }}</td>
                                <td>{{ $workstation->unique_orders_count }}</td>
                                <td>
                                    @php
                                        $hours = floor($workstation->time_spent_minutes / 60);
                                        $mins = $workstation->time_spent_minutes % 60;
                                        echo $hours > 0 ? $hours . 'h ' : '';
                                        echo $mins > 0 ? $mins . 'm' : ($hours == 0 ? '0m' : '');
                                    @endphp
                                </td>
                                {{--                            <td>{{ round(\Carbon\Carbon::parse($workstation->first_log?->time_started)->diffInUTCHours(\Carbon\Carbon::parse($workstation->last_log?->time_end)),2) }}</td>--}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-modal id="workstationModal" title="Workstation">
            <div class="modal-body">
                <div class="search-bar orders-search mb-3">
                    <input type="text" id="searchOrdersInModal" placeholder="Search" class="form-control" />
                    <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
                </div>

                <div>
                    <table class="table tablesorter"  id="workers_area_modal">
                        <thead>
                        <tr>
{{--                            <th>#</th>--}}
                            <th>Order #</th>
                            <th>Time spent(hours)</th>
                        </tr>
                        </thead>
                        <tbody id="workstationOrders">
                        </tbody>
                    </table>
                </div>
            </div>
            <x-slot name="footer">
                <button class="btn btn-secondary" onclick="document.getElementById('workstationModal').style.display='none'">Cancel</button>
            </x-slot>
            </x-modal>
            <x-modal id="editStatusModal" title="Edit Status">
                <form id="editStatusForm" action="javascript:void(0);" method="post" class="d-block">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input type="hidden" id="edit_id" name="id">
                                <label for="status-name">Status</label>
                                <select name="edit_status" class="form-select" id="edit_status">
                                    <option value="0">Select One</option>
                                    @foreach($edit_statuses as $status)
                                        <option value="{{$status->id}}">{{$status->status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6" id="sub_status_div" style="display: none;">
                            <div class="form-group">
                                <label for="">Sub Status</label>
                                <select name="edit_sub_status" class="form-select" id="edit_sub_status">
                                    <option value="">Select One</option>
                                    @foreach($sub_statuses as $sub_status)
                                        <option value="{{$sub_status->id}}" data-parent="{{$sub_status->status_id}}" style="display: none;">
                                            {{$sub_status->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <textarea name="notes"class="form-control" placeholder="Notes..."></textarea>
                        </div>
                    </div>
                </form>
                <x-slot name="footer">
                    <div class="form-group buttons">
                        <button type="submit" class="btn save-btn" onclick="updateStatus()">Update Status</button>
                        <button type="button" class="btn cancel-btn" onclick="document.getElementById('editStatusModal').style.display='none'">Cancel</button>
                    </div>
                </x-slot>
            </x-modal>
            <x-modal id="adminOverrideFormModal" title="Edit Status">
                <form id="adminOverrideForm" action="javascript:void(0);" method="post" class="d-block">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="override_status">Status</label>
                                <select name="override_status" class="form-select" id="override_status">
                                    <option value="0">Select One</option>
                                    @foreach($statuses as $status)
                                        <option value="{{$status->id}}">{{$status->status_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <x-slot name="footer">
                    <div class="form-group buttons">
                        <button type="submit" class="btn btn-dark" onclick="adminOverrideStatus()">Override</button>
                        <button type="button" class="btn cancel-btn" onclick="document.getElementById('adminOverrideFormModal').style.display='none'">Cancel</button>
                    </div>
                </x-slot>
            </x-modal>
        @endif


        <x-modal id="orderModal" title="Order #00001">
            <span class="status" id="modal_status_text">Completed</span>
            <div class="orderModal-flex">
                <div class="activity-logs">
                    <div class="activity-log">
                        <h3>Activity Log</h3>
                        <ul id="order_logs">
                        </ul>
                    </div>
                    <div class="sub-orders">
                        <div id="child_order">
                            <h3>Sub-Orders</h3>
                            <table class="border-1 border-dark">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Status</th>
                                    <th>Completion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->children ?? [] as $child_order)
                                    <tr>
                                        <td>{{ $child_order->order_id }}</td>
                                        <td>{{ $child_order->status->status_name }}</td>
                                        <td>
                                            @if($child_order->status->status_name === 'Completed')
                                                <img src="{{ asset('icons/green-checkmark.png') }}" alt="Completed" class="status-icon">
                                            @else
                                                <img src="{{ asset('icons/grey-checkmark.png') }}" alt="Incomplete" class="status-icon">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="comments">
                    <h3>Comments</h3>
                    <div id="comments_container">
                        @foreach($order->comments ?? [] as $comment)
                            <div class="comment">
                                <div class="comment-body">
                                    <span class="comment-user" data-initial="{{ $comment->user->name[0] }}">{{ $comment->user->name }}</span>
                                    <span class="comment-date">{{ \Carbon\Carbon::parse($comment->created_at)->format('M d \a\t g:i a') }}</span>
                                    <p class="comment-text">{{ $comment->comment }}</p>
                                </div>
                                <div class="comment-footer">
                                    <button class="btn">Reply</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <x-slot name="footer">
                    <button class="btn pdf-btn" id="download-pdf">
                        <img src="{{ asset('icons/pdf.png') }}" alt="PDF">View Order
                    </button>
                    @if(Auth::user()->role_id == 1)
                        <button class="btn pdf-btn override-btn bg-warning fw-bolder text-white" onclick="document.getElementById('adminOverrideFormModal').style.display='block'">
                            Override
                        </button>
                        <button class="btn btn-danger fw-bold ms-2" onclick="confirmDeleteOrder()">
                            Delete Order
                        </button>
                    @endif
                    <div class="new-comment">
                        <textarea placeholder="Write a message..." id="comment_input"></textarea>
                        {{-- <button class="msg-btn">
                          <img src="{{ asset('icons/attach.png') }}" alt="Attach file">
                        </button>
                        <button class="msg-btn">
                          <img src="{{ asset('icons/media.png') }}" alt="Media">
                        </button> --}}
                        <button class="send-btn" onclick="addComment()">
                            Send
                            <img src="{{ asset('icons/send.png') }}" alt="Send">
                        </button>
                    </div>
                </x-slot>
            </div>
        </x-modal>
    </div>
@endsection
<style>
    .log-entry--remake {
        background: #fff8e1;
        border-left: 3px solid #f59e0b;
        padding-left: 8px;
    }
    .log-divider {
        list-style: none;
        text-align: center;
        padding: 6px 0;
        margin: 6px 0;
    }
    .divider-label {
        background: #f59e0b;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
    }
    .log-timing {
        display: flex;
        flex-direction: column;
        font-size: 11px;
        color: #6b7280;
        margin-top: 2px;
    }
    .log-time {
        display: block;
    }
</style>
@section('footer_scripts')
    <script>
        function updateUrlParameter(param, value) {
            var currentUrl = window.location.href;
            var baseUrl = currentUrl.split('?')[0];
            var queryString = currentUrl.split('?')[1] || '';
            var params = new URLSearchParams(queryString);

            // Update or append the parameter
            if (value === 'Sort') {
                params.delete(param);
            } else {
                // Otherwise, update or add the parameter
                params.set(param, value);
            }

            // Create the new URL with updated parameters
            var newUrl = baseUrl + '?' + params.toString();
            window.location.href = newUrl;
        }

        $('#order_sort').on('change', function() {
            var filterDate = $(this).val();
            updateUrlParameter('filter_date', filterDate);
        });
        $('#order_filter').on('change', function() {
            var filterDate = $(this).val();
            updateUrlParameter('filter_by_time', filterDate);
        });

        $('#team-sort').on('change', function() {
            var teamSort = $(this).val();
            updateUrlParameter('team_sort', teamSort);
        });

        $('#workstation-sort').on('change', function() {
            var workstationSort = $(this).val();
            updateUrlParameter('workstation_sort', workstationSort);
        });

        $(document).ready(function() {
            let table = new DataTable('#dashboardOrdersTable', {
                autoWidth: false,
                columns: [
                    { title: "Order #", width: "150px" },
                    { title: "Phase", width: "200px" },
                    { title: "Team Member", width: "150px" },
                    { title: "Date Added", width: "200px" },
                    { title: "Date Started", width: "200px" },
                    { title: "Time in Production", width: "200px" },
                    { title: "Late", width: "200px" },
                    { title: "Actions", width: "280px", defaultContent: "" }
                ],
                columnDefs: [
                    {
                        targets: "_all",
                        orderSequence: ["asc", "desc"] // remove default state
                    },
                    {
                        targets: -1,         // -1 targets the last column
                        orderable: false     // Disable sorting on it
                    }
                ]
            });

            document.querySelector('#searchInput').addEventListener('keyup', function () {
                table.search(this.value).draw();
            });
            function performSearch(query) {
                $.ajax({
                    url: '{{ route('search.orders') }}',
                    type: 'GET',
                    data: { query: query },
                    success: function(response) {
                        $('#ordersBody').empty();
                        if (response.status == 200) {
                            $('#ordersBody').append(response.orders_view);
                        } else {
                            $('#ordersBody').append('<tr><td colspan="8">No results found</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // $('#searchInput').on('keyup', function() {
            //     var query = $(this).val();
            //     performSearch(query);
            //     if(query != ''){
            //         $('#order_paginations').hide();
            //     }else{
            //         $('#order_paginations').show();
            //     }
            // });
        });
        const order_id = '{{$order_id??null}}';
        $(document).ready(function() {
            var url = window.location.href;
            var urlObj = new URL(url);
            var orderId = urlObj.searchParams.get('order_id');
            var tab = urlObj.searchParams.get('tab');
            if (orderId && tab === 'open') {
                viewDetails(order_id,orderId);
            }


            $('#edit_status').on('change', function() {
                var selectedStatusId = $(this).val();
                var $subStatusOptions = $('#edit_sub_status option');
                var hasVisibleOptions = false;

                $subStatusOptions.each(function() {
                    if ($(this).data('parent') == selectedStatusId) {
                        $(this).show();
                        hasVisibleOptions = true;
                    } else {
                        $(this).hide();
                    }
                });
                $('#sub_status_div').toggle(hasVisibleOptions);
            });

            $('#edit_status').trigger('change');

        });


        function viewChildren(row_id){
            $('#'+row_id).toggle();
        }


        let activeOrder = 0;
        function editStatus(me){
            $('#edit_id').val($(me).data('id'));
            $('#edit_status').val($(me).data('status'));
            $('#edit_workstation').val($(me).data('workstation'));
            if(!$('#edit_status').val()){
                $('#edit_status').val('0');
            }
            $('#editStatusModal').show();
        }
        function updateStatus(){
            let data  = new FormData($('#editStatusForm')[0]);
            data.append('_token','{{@csrf_token()}}');
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('admin.update_order_status')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();

                },
                success: function (response) {
                    if(response.status == 200){
                        show_toast(response.message,'success');
                        window.location.reload();

                    }else{
                        show_toast(response.message,'error');
                    }

                },
                error: function (response) {
                }
            })
        }


        function formatLogDateTime(datetimeStr) {
            if (!datetimeStr) return null;
            const d = new Date(datetimeStr);
            return d.toLocaleString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric',
                hour: 'numeric', minute: '2-digit', hour12: true,
            });
        }

        function formatDuration(totalMinutes) {
            if (totalMinutes < 1) return '< 1m';
            const h = Math.floor(totalMinutes / 60);
            const m = totalMinutes % 60;
            if (h > 0 && m > 0) return `${h}h ${m}m`;
            if (h > 0) return `${h}h`;
            return `${m}m`;
        }

        const TIMED_STATUSES = [
            'Blocking', 'Gilding', 'ICE Cover', 'Imprinting',
            'Quality Control', 'Workstation #1', 'Workstation #2',
            'Workstation #3', 'Workstation #4', 'Printing', 'Engraving',
        ];

        const REMAKE_STATUS_NAMES = ['Issue with print', 'Remake + Reasons', 'Internal Reprint'];

        function viewDetails(orderId, title) {
            $('#child_order > h3').text('Sub-Orders');
            activeOrder = orderId;
            show_loader();

            let data = new FormData();
            data.append('_token', '{{@csrf_token()}}');
            data.append('id', orderId);

            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('admin.get_order_details')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();
                },
                success: function (response) {
                    if (response.status == 200) {

                        let status        = response.status_log;
                        let logs          = response.order.logs;
                        let commentsHtml  = response.comments_vew;
                        let child_orders  = response.order.children;
                        let parent_orders = response.order.parent;

                        if (child_orders && child_orders.length == 0 && parent_orders) {
                            child_orders = [parent_orders];
                            $('#child_order > h3').text('Main order');
                        }

                        // ── Activity log ──────────────────────────────────────────────────────

                        $('#order_logs').empty();

                        if (!logs || logs.length === 0) {
                            $('#order_logs').append('<li class="log-empty">No activity yet.</li>');
                        } else {
                            let remakeDividerDrawn = false;

                            $.each(logs, function (index, value) {
                                if (!value.status) return;

                                const statusName = value.status.status_name;
                                const isRemake   = REMAKE_STATUS_NAMES.includes(statusName);
                                const isTimed    = TIMED_STATUSES.includes(statusName);

                                // ── Remake divider ────────────────────────────────────────────
                                if (isRemake && !remakeDividerDrawn) {
                                    remakeDividerDrawn = true;
                                    $('#order_logs').append(`
                                <li class="log-divider" aria-hidden="true">
                                    <span class="divider-label">&#8635; Remake triggered — history above, restarted below</span>
                                </li>
                            `);
                                }

                                // ── Sub-status ────────────────────────────────────────────────
                                const subStatusHtml = value.sub_status
                                    ? `<span class="log-reason">Reason: ${value.sub_status.name}</span>`
                                    : '';

                                // ── Notes ─────────────────────────────────────────────────────
                                const notesHtml = value.notes
                                    ? `<div class="log-notes"><span class="log-notes-label">Notes: </span>${value.notes}</div>`
                                    : '';

                                // ── Timing block — station statuses only ──────────────────────
                                let timingHtml = '';
                                if (isTimed) {
                                    const startedAt   = formatLogDateTime(value.time_started);
                                    const completedAt = formatLogDateTime(value.time_end);

                                    let durationBadge = '';
                                    if (value.time_started && value.time_end) {
                                        const diffMins = Math.round(
                                            (new Date(value.time_end) - new Date(value.time_started)) / 60000
                                        );
                                        durationBadge = `<span class="log-badge log-badge--duration">${formatDuration(diffMins)}</span>`;
                                    } else if (value.time_started && !value.time_end) {
                                        durationBadge = `<span class="log-badge log-badge--inprogress">In progress</span>`;
                                    }

                                    timingHtml = `
                                <div class="log-timing">
                                    ${startedAt   ? `<div class="log-time-row">
                                                        <span class="log-time-label">Started</span>
                                                        <span class="log-time-value">${startedAt}</span>
                                                     </div>` : ''}
                                    ${completedAt ? `<div class="log-time-row log-time-row--completed">
                                                        <span class="log-time-label">Completed</span>
                                                        <span class="log-time-value">${completedAt}</span>
                                                     </div>` : ''}
                                    ${durationBadge}
                                </div>`;
                                } else {
                                    // ── Non-station statuses — just show when it was added ─────
                                    const addedAt = formatLogDateTime(value.time_started);
                                    timingHtml = addedAt
                                        ? `<div class="log-timing">
                                       <div class="log-time-row">
                                           <span class="log-time-label">Added</span>
                                           <span class="log-time-value">${addedAt}</span>
                                       </div>
                                   </div>`
                                        : '';
                                }

                                // ── Assemble entry ────────────────────────────────────────────
                                $('#order_logs').append(`
                            <li class="log-entry ${isRemake ? 'log-entry--remake' : ''}">
                                <div class="log-entry__header">
                                    <span class="log-entry__status">${statusName}</span>
                                </div>
                                <div class="log-entry__meta">
                                    <span class="log-entry__user">${value?.user?.name ?? 'Admin'}</span>
                                    ${subStatusHtml}
                                </div>
                                ${timingHtml}
                                ${notesHtml}
                            </li>
                        `);
                            });
                        }

                        // ── Sub-orders table ──────────────────────────────────────────────────

                        if (child_orders && child_orders.length > 0) {
                            $('#child_order').show();
                            $('#child_order table tbody').empty();
                            $.each(child_orders, function (index, value) {
                                let html = `<tr>
                            <td>${value.order_id}</td>
                            <td>${value.status.status_name}</td>`;
                                html += value.status.status_name === 'Completed'
                                    ? `<td><img src="{{ asset('icons/green-checkmark.png') }}" alt="Completed" class="status-icon"></td>`
                                    : `<td><img src="{{ asset('icons/grey-checkmark.png') }}" alt="Incomplete" class="status-icon"></td>`;
                                html += `</tr>`;
                                $('#child_order table tbody').append(html);
                            });
                        } else {
                            $('#child_order').hide();
                        }

                        // ── Comments ──────────────────────────────────────────────────────────

                        $('#comments_container').empty().html(commentsHtml);

                        // ── Status badge ──────────────────────────────────────────────────────

                        if (status) {
                            if (status.sub_status) {
                                $('#modal_status_text').empty()
                                    .html(status.sub_status.name)
                                    .css('background-color', status.status.status_color);
                            } else {
                                $('#modal_status_text').empty()
                                    .html(status.status.status_name)
                                    .css('background-color', status.status.status_color);
                            }
                        } else {
                            $('#modal_status_text').empty()
                                .html(response.order.status.status_name)
                                .css('background-color', response.order.status.status_color);
                        }

                        // ── Show modal ────────────────────────────────────────────────────────

                        $('#orderModal').show();
                        $('#orderModal > div > h2').text('Order #' + title);

                    } else {
                        show_toast(response.message, 'error');
                    }
                },
                error: function (response) {
                    console.error('Error:', response);
                }
            });
        }

        function addComment(){
            let data  = new FormData($('#editStatusForm')[0]);
            data.append('_token','{{@csrf_token()}}');
            data.append('comment',$('#comment_input').val());
            data.append('order_id',activeOrder);
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('order.add_comment')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();
                },
                success: function (response) {
                    if(response.status == 200){
                        show_toast(response.message,'success');
                        $('#comments_container').append(response.comment_view);
                        $('#comment_input').val('');
                    }else{
                        show_toast(response.message,'error');
                    }
                },
                error: function (response) {
                    console.error('Error:', response);
                }
            });
        }

        let commentId=0;
        $(document).on('click', '.reply-btn', function(){
            var $replyForm = $(this).closest('.comment').find('.reply-form');
            $replyForm.toggle();
            commentId = $(this).data('comment');
        });
        $(document).on('click', '.submit-reply', function(){
            var $comment = $(this).closest('.comment');
            var replyText = $(this).closest('.reply-form').find('input').val();
            let data  = new FormData($('#editStatusForm')[0]);
            data.append('_token','{{@csrf_token()}}');
            data.append('comment',replyText);
            data.append('order_id',activeOrder);
            data.append('reply_to',commentId);

            if (replyText !== '') {
                $.ajax({
                    url: '{{route('order.add_reply')}}',
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    success: function(reply) {
                        if(reply.status == 200){
                            $comment.replaceWith(reply.comment_view);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });

        function toggleReplies(id){
            $('.'+id).toggleClass('open');
        }

        function adminOverrideStatus(){
            let data  = new FormData($('#adminOverrideForm')[0]);
            data.append('order_id',activeOrder);
            data.append('_token','{{@csrf_token()}}');
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                cache: false,
                url: '{{route('admin.admin_override_status')}}',
                data: data,
                beforeSend() {
                    show_loader();
                },
                complete: function (response) {
                    hide_loader();

                },
                success: function (response) {
                    if(response.status == 200){
                        show_toast(response.message,'success');
                        window.location.reload();

                    }else{
                        show_toast(response.message,'error');
                    }

                },
                error: function (response) {
                }
            })
        }


        function initDatatable(){
            let table = new DataTable('#workers_area_modal', {
                autoWidth: false,
                pageLength: 10,
                lengthChange: false,
                columns: [
                    // {title: "#"},
                    {title: "Order #"},
                    {title: "Total time(hours)"}
                ]
            });
            document.querySelector('#searchOrdersInModal').addEventListener('keyup', function () {
                table.search(this.value).draw();
            });
        }

        function loadTeamDetails(teamMemberId, title) {
            // Get the current sort value
            var sortParam = $('#team-sort').val();

            // Make an AJAX request to fetch team details
            $.ajax({
                url: '/team/' + teamMemberId + '?sort=' + sortParam,
                method: 'GET',
                success: function(response) {
                    if ($.fn.DataTable.isDataTable('#workers_area_modal')) {
                        $('#workers_area_modal').DataTable().clear().destroy();
                    }
                    // Populate the modal with the response
                    $('#workstationOrders').html(response.ordersHtml);
                    $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);

                    // Show the modal
                    $('#workstationModal').show();
                    $('#workstationModal .modal-content > h2').text(title);
                    initDatatable();
                },
                error: function() {
                    $('#workstationOrders').html('<tr><td colspan="3">Failed to load team details.</td></tr>');
                }
            });
        }

        function loadWorkstationDetails(workstationId, title) {
            // Get the current sort value
            var sortParam = $('#workstation-sort').val();

            // Make an AJAX request to fetch workstation details
            $.ajax({
                url: '/workstations/' + workstationId + '?sort=' + sortParam,
                method: 'GET',
                success: function(response) {
                    if ($.fn.DataTable.isDataTable('#workers_area_modal')) {
                        $('#workers_area_modal').DataTable().clear().destroy();
                    }
                    // Populate the modal with the response
                    $('#workstationOrders').html(response.ordersHtml);
                    $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);

                    // Show the modal
                    $('#workstationModal').show();
                    $('#workstationModal .modal-content > h2').text(title);
                    initDatatable();
                },
                error: function() {
                    $('#workstationOrders').html('<tr><td colspan="3">Failed to load workstation details.</td></tr>');
                }
            });
        }
        // $('#searchOrders').on('keyup', function() {
        //     var searchValue = $(this).val().toLowerCase();
        //     $('#workstationOrders tr').filter(function() {
        //         $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
        //     });
        // });
        $('#download-pdf').on('click', function() {
            window.location.href = '/orders/' + activeOrder + '/download-pdf';
        });






        @if(isset($orderLog))
            {{--const timeStarted = new Date("{{ $orderLog?->time_started }}").getTime();--}}
            {{--const serverTime = new Date("{{ \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s') }}").getTime();--}}

            {{--$(document).ready(function() {--}}
            {{--    startTimer(timeStarted, serverTime);--}}
            {{--});--}}

            function startTimer(startTime, serverTime) {
                let currentTime = new Date(serverTime).getTime();
                const updateClock = () => {

                    const elapsedTime = currentTime - startTime;

                    // Calculate hours, minutes, seconds
                    const seconds = Math.floor((elapsedTime / 1000) % 60);
                    const minutes = Math.floor((elapsedTime / (1000 * 60)) % 60);
                    const hours = Math.floor((elapsedTime / (1000 * 60 * 60)) % 24);
                    const days = Math.floor(elapsedTime / (1000 * 60 * 60 * 24));

                    // Format the clock display
                    const display = `${days}d ${hours}h ${minutes}m ${seconds}s`;

                    // Update the clock on the page
                    $('#clock').text(display);
                    currentTime += 1000;
                };

                // Update the clock every second
                setInterval(updateClock, 1000);

                // Run it once to set the initial value
                updateClock();
            }
        @endif

        function confirmDeleteOrder() {
            if (!activeOrder) {
                show_toast('No order selected.', 'error');
                return;
            }

            Swal.fire({
                title: 'Delete Order?',
                text: 'This will permanently delete the order and all its related data. This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (!result.isConfirmed) return;

                let data = new FormData();
                data.append('_token', '{{ csrf_token() }}');
                data.append('id', activeOrder);

                $.ajax({
                    type: 'post',
                    processData: false,
                    contentType: false,
                    cache: false,
                    url: '{{ route("orders.delete") }}',
                    data: data,
                    beforeSend() { show_loader(); },
                    complete: function() { hide_loader(); },
                    success: function(response) {
                        if (response.status == 200) {
                            $('#orderModal').hide();
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#16a34a',
                                timer: 2000,
                                timerProgressBar: true,
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({ title: 'Error', text: response.message, icon: 'error' });
                        }
                    },
                    error: function() {
                        Swal.fire({ title: 'Error', text: 'Something went wrong. Please try again.', icon: 'error' });
                    }
                });
            });
        }
    </script>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // $("#dashboardOrdersTable").tablesorter();
            $("#dashboardTeamTable").tablesorter();
            $("#dashboardWorkstationsTable").tablesorter();
        });
    </script>
@endpush
