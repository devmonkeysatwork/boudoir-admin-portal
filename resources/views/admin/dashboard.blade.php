@extends('layouts.app')

@section('content')
<div class="dashboard">
  <h1>Dashboard</h1>
  <div class="stats">
    <div class="stat">
      <div class="stat-top">
        <img src="{{ asset('icons/orders-pending.png') }}" alt="Pending">
        <div class="stat-info">
          <h3>40,689</h3>
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
          <h3>10,293</h3>
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
          <h3>89,000</h3>
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
          <h3>2,040</h3>
          <p>Ready to Ship</p>
        </div>
      </div>
      <div class="stat-btm">
        <img src="{{ asset('icons/stat-up.png') }}" alt="Stat Up">
        <p><span class="stat-up">1.8%</span> Up from yesterday</p>
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
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Phase</th>
          <th>Team Member</th>
          <th>Date Started</th>
          <th>Time in Production</th>
        </tr>
      </thead>
      <tbody id="ordersBody">
      @if(isset($orders) && count($orders))
          @foreach($orders as $order)
            <tr>
              <td>{{$order->order_id}}</td>
              <td><span class="status completed">{{$order->status?->status_name}}</span></td>
              <td>{{$order->station?->worker?->name}}</td>
              <td>{{$order->date_started}}</td>
              <td>
                @php
                    $dateStarted = new DateTime($order->date_started);
                    $now = new DateTime();
                    $timeSpent = $now->diff($dateStarted);

                    $days = $timeSpent->d;
                    $hours = $timeSpent->h;
                    $minutes = $timeSpent->i;

                    $timeSpentString = ($days > 0 ? $days . 'd ' : '') . ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
                @endphp
                {{ $timeSpentString ?? '' }}
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
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
          <tr>
            <td>Jason Price</td>
            <td>5</td>
            <td>3h30m</td>
          </tr>
          <tr>
            <td>Duane Dean</td>
            <td>7</td>
            <td>5h07m</td>
          </tr>
          <tr>
            <td>Jonathan Barker</td>
            <td>4</td>
            <td>2h49m</td>
          </tr>
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
            <th>Workstation #</th>
            <th># of Orders</th>
            <th>Time in Production</th>
            <th>Assigned To</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>5</td>
            <td>13h39m</td>
            <td>Jason Price</td>
          </tr>
          <tr>
            <td>2</td>
            <td>7</td>
            <td>20h27m</td>
            <td>Duane Dean</td>
          </tr>
          <tr>
            <td>3</td>
            <td>4</td>
            <td>11h15m</td>
            <td>Jonathan Barker</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('footer_scripts')
    <script>
        $(document).ready(function() {
            // Function to perform AJAX search
            function performSearch(query) {
                $.ajax({
                    url: '{{ route('search.orders') }}', // Replace with your search route
                    type: 'GET',
                    data: { query: query },
                    success: function(response) {
                        $('#ordersBody').empty(); // Clear previous results

                        // Append new search results to the table
                        if (response.orders.length > 0) {
                            $.each(response.orders, function(index, order) {
                                var dateStarted = new Date(order.date_started); // Assuming order.date_started is a valid date string or Date object
                                var now = new Date();
                                var timeDiff = now - dateStarted;

                                var days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));

                                var timeSpentString = (days > 0 ? days + 'd ' : '') + (hours > 0 ? hours + 'h ' : '') + (minutes > 0 ? minutes + 'm' : '');

                                var row = '<tr>' +
                                    '<td>' + order.order_id + '</td>' +
                                    '<td><span class="status completed">' + (order.status ? order.status.status_name : '') + '</span></td>' +
                                    '<td>' + (order.station ? order.station.worker.name : '') + '</td>' +
                                    '<td>' + order.date_started + '</td>' +
                                    '<td>' + timeSpentString + '</td>' +
                                    '</tr>';

                                $('#ordersBody').append(row);
                            });
                        } else {
                            $('#ordersBody').append('<tr><td colspan="5">No results found</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // Event listener for search input
            $('#searchInput').on('keyup', function() {
                var query = $(this).val();
                performSearch(query);
            });
        });
    </script>
@endsection
