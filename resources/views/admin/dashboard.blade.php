@extends('layouts.app')

@section('content')
    <div class="dashboard">
        <h1>Dashboard</h1>
        @if(Auth::user()->role_id == 1)
            <div class="stats row">
                <div class="col-md-6 col-lg-4 col-xl">
                    <div class="stat">
                        <div class="stat-top">
                            <img src="{{ asset('icons/orders-pending.png') }}" alt="Ready for Print">
                            <div class="stat-info">
                                <h3>{{ $readyForPrintOrdersCount }}</h3>
                                <p>Ready for Print</p>
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
                                <h3>{{ $readyForPrintOrdersCount }}</h3>
                                <p>Ready for Print</p>
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
            </div>
        @endif
        @if(Auth::user()->role_id != 1)
            <div>
                <div id="current_order" class="text-center mb-4">
                    @if ($orderLog)
                        <h1>Working on Order #{{ $orderLog->order_id }}</h1>
                        <div id="timer" class="mt-4">
                            <h2>Time Worked: <span id="clock">00:00:00</span></h2>
                        </div>
                        <button class="btn create-btn" onclick="endOrderPhase()">Complete</button>
                    @else
                        <p class="p20 text-center">No order in progress.</p>
                    @endif
                </div>
            </div>
        @endif
        <div class="orders" id="orders_table_db">
            <div class="orders-top">
                <h2>List of Orders</h2>
                <div class="orders-filter">
                    <div class="orders-search">
                        <input type="text" id="searchInput" placeholder="Search">
                        <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
                    </div>
                    <div class="sort-dropdown">
                        <select class="sort-select" id="order-sort">
                            <option value="" disabled selected>Sort By</option>
                            <option {{$filter_date && $filter_date =='oldest'?'selected':''}} value="oldest">Oldest</option>
                            <option {{$filter_date && $filter_date =='newest'?'selected':''}} value="newest">Newest</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="orders_table_container">
                <table id="dashboardOrdersTable" class="tablesorter">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Priority</th>
                        <th>Phase</th>
                        <th>Team Member</th>
                        <th>Date Started</th>
                        <th>Time in Production</th>
                        <th>Late</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody id="ordersBody">
                    @if(isset($orders) && count($orders))
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>
                                    @if($order->is_rush)
                                        <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                  <span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">
                                      @if(isset($order->last_log->sub_status))
                                          {{$order->last_log?->sub_status?->name ?? null}}
                                      @elseif(isset($order->last_log->status))
                                          {{$order->last_log?->status?->status_name ?? null}}
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
                                <td>{{ $order->date_started }}</td>
                                <td>
                                    @php
                                        $dateStarted = \Carbon\Carbon::parse($order->created_at);
                                        $now = \Carbon\Carbon::now();
                                        $workingTime = calculateWorkingTime($dateStarted, $now);
                                    @endphp

                                    {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                                    {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                                    {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                                    {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
                                </td>
                                <td>
                                    @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                                        <img src="{{asset('icons/exclaimatio.svg')}}" alt="">
                                    @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                                        <span class="fw-bold text-danger">{{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hours left</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(Auth::user()->role_id == 1)
                                        <button class="edit-btn" onclick="editStatus(this)" data-id="{{$order->id}}" data-status="{{$order->status_id}}" data-workstation="{{$order->workstation_id}}">
                                            <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon">
                                        </button>
                                    @endif
                                    <button class="edit-btn" onclick="viewDetails('{{ $order->id }}','{{ $order->order_id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                        </svg>
                                    </button>
                                    @if($orderLog && $orderLog->order_id == $order->order_id)
                                        In Progress
                                    @else
                                        <button data-id="{{$order->order_id}}" type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">
                                            Start order
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="row justify-content-end" id="order_paginations">
                <div class="col-6 text-start">
                    @if($orders->count())
                        <p class="py-4 mb-0">
                            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}
                        </p>
                    @endif
                </div>
                <div class="col-6 text-end">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
        @if(Auth::user()->role_id == 1)
            <div class="team-workstations">
                <div class="team">
                    <div class="team-top">
                        <h2>Team</h2>
                        <div class="sort-dropdown">
                            <select class="sort-select" id="team-sort">
                                <option value="week" selected>Week</option>
                                <option value="day">Day</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
                            </select>
                        </div>
                    </div>
                    <table id="dashboardTeamTable" class="tablesorter">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th># of Orders</th>
                            <th>Time Spent</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($teamMembers as $teamMember)
                            <tr onclick="loadTeamDetails({{ $teamMember['id'] }},'{{$teamMember['user_name']}}')">
                                <td>{{ $teamMember['user_name'] }}</td>
                                <td>{{ $teamMember['order_count'] }}</td>
                                <td>{{ $teamMember['total_time'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="workstations">
                    <div class="workstations-top">
                        <h2>Areas</h2>
                        <div class="sort-dropdown">
                            <select class="sort-select" id="workstation-sort">
                                <option value="week" selected>Week</option>
                                <option value="day">Day</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option>
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
                                <td>{{ $workstation->orders_count() }}</td>
                                <td>{{ round($workstation->time_spent,2) }}</td>
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
                    <input type="text" id="searchOrders" placeholder="Search" class="form-control" />
                    <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
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
                                @foreach($order->children as $child_order)
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
                        @foreach($order->comments as $comment)
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
@section('footer_scripts')
    <script>
        $('#order-sort').on('change',function() {
            window.location.href = '{{route('dashboard')}}?filter_date='+ $(this).val();
        })
        $(document).ready(function() {
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

            $('#searchInput').on('keyup', function() {
                var query = $(this).val();
                performSearch(query);
                if(query != ''){
                    $('#order_paginations').hide();
                }else{
                    $('#order_paginations').show();
                }
            });
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

        function viewDetails(orderId, title) {
            activeOrder = orderId;
            show_loader();

            let data  = new FormData();
            data.append('_token','{{@csrf_token()}}');
            data.append('id',orderId);
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
                    console.log(response);
                    if(response.status == 200){
                        let status = response.status_log;
                        let logs = response.order.logs;
                        let commentsHtml = response.comments_vew;
                        let child_orders = response.order.children;

                        $('#order_logs').empty();
                        $.each(logs, function(index, value) {
                            let html = `<li class="log-entry">
                                    <span class="log-desc">${value.user.name} updated the status to <span class="fw-bold">${value?.status?.status_name}</span>`;
                            if(value.sub_status){
                                html += ` because of ${value.sub_status.name}`;
                            }
                            if(value.notes){
                                html += `<br><b>Notes: </b><i>${value.notes}</i>`;
                            }
                            html += `</span>
                                    <span class="log-date">${value.time_started}</span>
                                </li>`;
                            $('#order_logs').append(html);
                        });

                        if(child_orders && child_orders.length > 0){
                            $('#child_order').show();
                            $('#child_order table tbody').empty();
                            $.each(child_orders, function(index, value) {
                                let html = `<tr>
                                        <td>${value.order_id}</td>
                                        <td>${value.status.status_name}</td>`;
                                if(value.status.status_name === 'Completed'){
                                    html += `<td><img src="{{ asset('icons/green-checkmark.png') }}" alt="Completed" class="status-icon"></td>`;
                                } else {
                                    html += `<td><img src="{{ asset('icons/grey-checkmark.png') }}" alt="Incomplete" class="status-icon"></td>`;
                                }
                                html += `</tr>`;
                                $('#child_order table tbody').append(html);
                            });
                        } else {
                            $('#child_order').hide();
                        }

                        $('#comments_container').html(commentsHtml);

                        if(status){
                            if(status.sub_status){
                                $('#modal_status_text').empty().html(status.sub_status.name).css('background-color',status.status.status_color);
                            }else{
                                $('#modal_status_text').empty().html(status?.status?.status_name).css('background-color',status?.status?.status_color);
                            }
                        }else{
                            $('#modal_status_text').empty().html(response.order.status.status_name).css('background-color',response.order.status.status_color);
                        }

                        $('#orderModal').show();
                        $('#orderModal > div > h2').text('Order #' + title);
                    } else {
                        show_toast(response.message,'error');
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

        function loadTeamDetails(teamMemberId,title) {
            // Make an AJAX request to fetch team details
            $.ajax({
                url: '/team/' + teamMemberId,
                method: 'GET',
                success: function(response) {
                    // Populate the modal with the response
                    $('#workstationOrders').html(response.ordersHtml);
                    $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);

                    // Show the modal
                    $('#workstationModal').show();
                    $('#workstationModal .modal-content > h2').text(title);
                },
                error: function() {
                    $('#workstationOrders').html('<tr><td colspan="3">Failed to load team details.</td></tr>');
                }
            });
        }

        function loadWorkstationDetails(workstationId,title) {

            // Make an AJAX request to fetch workstation details
            $.ajax({
                url: '/workstations/' + workstationId,
                method: 'GET',
                success: function(response) {
                    // Populate the modal with the response
                    $('#workstationOrders').html(response.ordersHtml);
                    $('#orderCount').text('Showing 1-' + response.orderCount + ' of ' + response.orderCount);

                    // Show the modal
                    $('#workstationModal').show();
                    $('#workstationModal .modal-content > h2').text(title);
                },
                error: function() {
                    $('#workstationOrders').html('<tr><td colspan="3">Failed to load workstation details.</td></tr>');
                }
            });
        }

        $('#searchOrders').on('keyup', function() {
            var searchValue = $(this).val().toLowerCase();
            $('#workstationOrders tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });
        $('#download-pdf').on('click', function() {
            window.location.href = '/orders/' + activeOrder + '/download-pdf';
        });







        const timeStarted = new Date("{{ $orderLog?->time_started }}").getTime();
        const serverTime = new Date("{{ \Illuminate\Support\Carbon::now()->format('Y-m-d H:i:s') }}").getTime();

        $(document).ready(function() {
            startTimer(timeStarted, serverTime);
        });

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



        function endOrderPhase() {
            const id = @json($orderLog?->id ?? '');
            const orderNumber = @json($orderLog?->order_id ?? '');

            let data  = new FormData();
            data.append('_token','{{@csrf_token()}}');
            data.append('id',id);
            data.append('order_id',orderNumber);

            $.ajax({
                url: '{{route('order.end_log')}}',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend() {
                    show_loader();
                },
                success: function(response) {
                    if (response.status == 200) {
                        $('#current_order').append(`<p class="p14 text-success">${response.message}</p>`);
                        location.reload();
                    } else {
                        $('#current_order').append(`<p class="error p14 text-danger">${response.message}</p>`);
                    }
                    hide_loader();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    hide_loader();
                }
            });
        }
    </script>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#dashboardOrdersTable").tablesorter();
            $("#dashboardTeamTable").tablesorter();
            $("#dashboardWorkstationsTable").tablesorter();
        });
    </script>
@endpush
