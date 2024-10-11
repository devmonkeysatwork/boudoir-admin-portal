@extends('layouts.app')

@section('content')
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
<div class="orders">
  <h1>Order List</h1>
  <table id="" class="tablesorter">
    <thead>
      <tr>
        <th>Order #</th>
        <th>Phase</th>
        <th>Time Started</th>
        <th>Time Ended</th>
        <th>Time Took</th>
      </tr>
    </thead>
    <tbody id="ordersBody">
        @foreach($myOrders as $log)
          <tr>
              <td> {{$log->order->order_id}} </td>
              <td>
                  <span class="status" style="background-color: {{$log->status?->status_color ?? 'transparent'}}">
                      {{$log->status?->status_name ?? null}}
                  </span>
              </td>
              <td> {{$log->time_started}} </td>
              <td> {{$log->time_end}} </td>
              <td>
                  @php
                      $dateStarted = \Carbon\Carbon::parse($log->time_started);
                      $now = \Carbon\Carbon::parse($log->time_end);
                      $workingTime = calculateWorkingTime($dateStarted, $now);
                  @endphp

                  {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                  {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                  {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                  {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
              </td>
          </tr>
        @endforeach
    </tbody>
  </table>
    <div class="row justify-content-end" id="order_paginations">
        <div class="col-6 text-start">
            @if($myOrders->count())
                <p class="py-4 mb-0">
                    Showing {{ $myOrders->firstItem() }} to {{ $myOrders->lastItem() }} of {{ $myOrders->total() }}
                </p>
            @endif
        </div>
        <div class="col-6 text-end">
            {{ $myOrders->links() }}
        </div>
    </div>
</div>
@endsection
@section('footer_scripts')
    <script>
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
            const id = {{ $orderLog?->id??'' }};
            const orderNumber = {{ $orderLog?->order_id??'' }};
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
