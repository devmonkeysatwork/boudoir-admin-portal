@extends('layouts.app')

@section('content')
<div class="dashboard">
  <h1>Dashboard</h1>
  <div class="stats">
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-pending.png') }}" alt="Pending">
        <div class="stat-info">
          <h3>N/A</h3>
          <p>Pending</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">8.5%</span> Up from yesterday</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-in-production.png') }}" alt="in Production">
        <div class="stat-info">
          <h3>{{ $inProductionOrdersCount }}</h3>
          <p>In Production</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">1.3%</span> Up from past week</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-on-hold.png') }}" alt="on Hold">
        <div class="stat-info">
          <h3>{{ $onHoldOrdersCount }}</h3>
          <p>On Hold</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-down.png') }}" alt="Stat Down">
        <p><span class="stat-down">4.3%</span> Down from yesterday</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-ready.png') }}" alt="Ready">
        <div class="stat-info">
          <h3>{{ $readyToShipOrdersCount }}</h3>
          <p>Ready to Ship</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">1.8%</span> Up from yesterday</p>
      </div>
    </div>
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/quality-control.png') }}" alt="Quality Control">
        <div class="stat-info">
          <h3>{{ $qualityControlOrdersCount }}</h3>
          <p>in Quality Control</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">0%</span> Up from yesterday</p>
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
          <select class="sort-select" id="order-sort">
            <option value="" disabled selected>Sort By</option>
            <option value="oldest">Oldest</option>
            <option value="newest">Newest</option>
          </select>
        </div>
      </div>
    </div>
    <div id="orders_table_container">
        <table>
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
                              <span class="status" style="background-color: {{ $order->status?->status_color ?? 'transparent' }}">
                                  @if(isset($order->last_log->sub_status))
                                      {{ $order->last_log?->sub_status?->name ?? null }}
                                  @else
                                      {{ $order->last_log?->status?->status_name ?? null }}
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
                                  $timeSpent = $dateStarted->diff($now);
                              @endphp
                              {{ $timeSpent->days > 0 ? $timeSpent->days . 'd ' : '' }}
                              {{ $timeSpent->h > 0 ? $timeSpent->h . 'h ' : '' }}
                              {{ $timeSpent->i > 0 ? $timeSpent->i . 'm' : '' }}
                          </td>
                          <td>
                              @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                                  <img src="{{asset('icons/exclaimatio.svg')}}" alt="Late">
                              @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                                  <span class="fw-bold text-danger">{{ round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)), 0) }} hours left</span>
                              @else
                                  -
                              @endif
                          </td>
                          <td>
                              <button class="edit-btn" onclick="editStatus(this)" data-id="{{ $order->id }}" data-status="{{ $order->status_id }}" data-workstation="{{ $order->workstation_id }}">
                                  <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                              </button>
                              <button class="edit-btn" onclick="viewDetails('{{ $order->id }}','{{ $order->order_id }}')">
                                  View details
                              </button>
                          </td>
                      </tr>
                  @endforeach
              @endif
          </tbody>
        </table>
    </div>
      <div class="row justify-content-end d-flex">
          <div class="col-12 col-sm-3">
              {{ $orders->links() }}
          </div>
      </div>
  </div>
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
      <table>
          <thead>
              <tr>
                  <th>Name</th>
                  <th># of Orders</th>
                  <th>Time Spent</th>
              </tr>
          </thead>
          <tbody>
              @foreach($teamMembers as $teamMember)
                  <tr>
                    <td>{{ $teamMember->name }}</td>
                    <td>{{ $teamMember->total_orders }}</td>
                    <td>{{ $teamMember->time_spent }}</td>
                  </tr>
              @endforeach
          </tbody>
      </table>

    </div>
    <div class="workstations">
      <div class="workstations-top">
        <h2>Workstations</h2>
        <div class="sort-dropdown">
          <select class="sort-select" id="workstation-sort">
            <option value="week" selected>Week</option>
            <option value="day">Day</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
          </select>
        </div>
      </div>
      <table>
          <thead>
              <tr>
                  <th>Workstation Name</th>
                  <th># of Orders</th>
                  <th>Time in Production</th>
              </tr>
          </thead>
          <tbody>
              @foreach($workstations as $workstation)
                  <tr>
                      <td>{{ $workstation->workstation_name }}</td>
                      <td>{{ $workstation->num_orders }}</td>
                      <td>{{ $workstation->time_in_production }}</td>
                  </tr>
              @endforeach
          </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('footer_scripts')
    <script>
        $(document).ready(function() {
            function performSearch(query) {
              $.ajax({
                  url: '{{ route('search.orders') }}',
                  type: 'GET',
                  data: { query: query },
                  success: function(response) {
                      $('#ordersBody').empty();

                      if (response.orders.length > 0) {
                          $.each(response.orders, function(index, order) {
                              console.log(order);
                              var dateStarted = new Date(order.date_started);
                              var now = new Date();
                              var timeDiff = now - dateStarted;

                              var days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                              var hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                              var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));

                              var timeSpentString = (days > 0 ? days + 'd ' : '') + (hours > 0 ? hours + 'h ' : '') + (minutes > 0 ? minutes + 'm' : '');

                              var priority = order.priority === 'rush' ? '<img src="{{asset('icons/rush.svg')}}" alt="Rush">' : '-';

                              var late = '';
                              if (order.deadline) {
                                  var deadline = new Date(order.deadline);
                                  if (now >= deadline) {
                                      late = '<img src="{{asset('icons/exclaimatio.svg')}}" alt="Late">';
                                  } else if (now >= deadline.setDate(deadline.getDate() - 2)) {
                                      late = '<span class="fw-bold text-danger">' + Math.round((deadline - now) / (1000 * 60 * 60)) + ' hours left</span>';
                                  } else {
                                      late = '-';
                                  }
                              } else {
                                  late = '-';
                              }

                              var row = '<tr>' +
                                  '<td>' + order.order_id + '</td>' +
                                  '<td>' + priority + '</td>' +
                                  '<td><span class="status" style="background-color: '+order.status.status_color+'">' + (order.status ? order.status.status_name : '') + '</span></td>' +
                                  '<td>' + (order.station ? order.station.worker.name : '') + '</td>' +
                                  '<td>' + order.date_started + '</td>' +
                                  '<td>' + timeSpentString + '</td>' +
                                  '<td>' + late + '</td>' +
                                  '<td>' +
                                      '<button class="edit-btn" onclick="editStatus(this)" data-id="' + order.id + '" data-status="' + order.status_id + '" data-workstation="' + order.workstation_id + '">' +
                                          '<img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">' +
                                      '</button>' +
                                      '<button class="edit-btn" onclick="viewDetails(\'' + order.id + '\',\'' + order.order_id + '\')">View details</button>' +
                                  '</td>' +
                                  '</tr>';

                              $('#ordersBody').append(row);
                          });
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
            });
        });
    </script>
@endsection
