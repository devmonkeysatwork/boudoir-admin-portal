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
        <th></th>
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
              <td>
                  <button class="edit-btn" onclick="viewDetails('{{$log->order->id}}','{{$log->order->order_id}}')">
                      <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="12" cy="12" r="3.5" stroke="#222222"/>
                          <path d="M21 12C21 12 20 4 12 4C4 4 3 12 3 12" stroke="#222222"/>
                      </svg>
                  </button>
                  @if($orderLog && $orderLog->order_id == $log->order_id)
                      In Progress
                  @else
                      <button data-id="{{$log->order_id}}" type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">
                          Start order
                      </button>
                  @endif
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

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="comments">
                <h3>Comments</h3>
                <div id="comments_container">

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

        let activeOrder = 0;
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
                        let commentsHtml = response.comments_vew; // This is an HTML string now
                        let child_orders = response.order.children;

                        // Populate the Activity Log
                        $('#order_logs').empty();
                        $.each(logs, function(index, value) {
                            if(value.status){
                                let html = `<li class="log-entry">
                                <span class="log-desc">${value.user.name} updated the status to <span class="fw-bold">${value.status.status_name}</span>`;
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
                            }
                        });

                        // Populate the Sub-orders Table
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
                        // Directly append the HTML string to the comments container
                        $('#comments_container').empty().html(commentsHtml);
                        if(status){
                            if(status.sub_status){
                                $('#modal_status_text').empty().html(status.sub_status.name).css('background-color',status.status.status_color);
                            }else{
                                $('#modal_status_text').empty().html(status.status.status_name).css('background-color',status.status.status_color);
                            }
                        }else{
                            $('#modal_status_text').empty().html(response.order.status.status_name).css('background-color',response.order.status.status_color);
                        }

                        // Show the modal
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
                        $('#comment_input').val(''); // Clear the input
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
        $('#download-pdf').on('click', function() {
            window.location.href = '/orders/' + activeOrder + '/download-pdf';
        });
    </script>
@endsection
