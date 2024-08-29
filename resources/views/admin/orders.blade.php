@extends('layouts.app')

@section('content')
<div class="orders">
  <h1>Order List</h1>

  <div class="filters">
    <div class="filter-bar">
        <div class="filter-item">
        <button class="filter-btn">
            <img src="{{ asset('icons/filter.png') }}" alt="Filter Icon">
            <span>Filter By</span>
        </button>
        </div>
        <div class="filter-item">
        <select class="sort-select" id="filter-date">
            <option value="" disabled selected>Date</option>
            <option value="oldest">Oldest</option>
            <option value="newest">Newest</option>
        </select>
        </div>
        <div class="filter-item">
        <select class="sort-select" id="filter-product">
        <option value="" disabled selected>Product</option>
            @foreach($products as $product)
                <option value="{{ $product->product_name }}">{{ $product->product_name }}</option>
            @endforeach
        </select>
        </div>
        <div class="filter-item">
        <select class="sort-select" id="filter-status">
            <option value="" disabled selected>Order Status</option>
            @foreach($statuses??[] as $status)
                <option value="{{$status->id}}">{{$status->status_name}}</option>
            @endforeach
        </select>
        </div>
        <div class="filter-item">
        <select class="sort-select" id="filter-priority">
            <option value="" disabled selected>Priority</option>
            <option value="normal">Normal</option>
            <option value="rush">Rush</option>
        </select>
        </div>
        <div class="filter-item">
        <button class="reset-btn">
            <img src="{{ asset('icons/reset.png') }}" alt="Reset">Reset Filter
        </button>
        </div>
    </div>
    <div class="orders-search">
      <input type="text" id="searchInput" placeholder="Search">
      <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
    </div>
  </div>  
        

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
    <tbody>
        @foreach($orders as $order)
          <tr>
            <td>{{$order->order_id}}</td>
            <td>
                @if($order->is_rush == 1)
                    <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                @else
                    -
                @endif
            </td>
            <td><span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">
                    @if(isset($order->last_log->sub_status))
                        {{$order->last_log?->sub_status?->name ?? null}}
                    @else
                        {{$order->last_log?->status?->status_name ?? null}}
                    @endif
                </span>
            </td>
            <td>{{$order->station?->worker?->name ?? null}}</td>
            <td>{{$order->date_started}}</td>
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
                    <img src="{{asset('icons/exclaimatio.svg')}}" alt="">
                  @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                      <span class="fw-bold text-danger">{{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hours left</span>
                  @else
                      -
                  @endif
              </td>
              <td>
                  <button class="edit-btn" onclick="editStatus(this)" data-id="{{$order->id}}" data-status="{{$order->status_id}}" data-workstation="{{$order->workstation_id}}">
                      <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                  </button>
                  <button class="edit-btn" onclick="viewDetails('{{$order->id}}','{{$order->order_id}}')">
                      View details
                  </button>
                  @if(isset($order->children) && count($order->children))
                      <button class="edit-btn" onclick="viewChildren('children_{{$order->id}}')">
                          <img src="{{ asset('icons/chevron-down.svg') }}" alt="Expand Icon">
                      </button>
                  @endif
              </td>
          </tr>
            @if(isset($order->children) && count($order->children))
                <tr style="display: none;border: 1px solid #191919;" id="children_{{$order->id}}">
                    <td colspan="8" style="border: 1px solid #191919;">
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
                            <tbody>
                            @foreach($order->children as $child_order)
                                <tr>
                                    <td>{{$child_order->order_id}}</td>
                                    <td>
                                        @if($child_order->is_rush == 1)
                                            <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                                        @endif
                                    </td>
                                    <td><span class="status" style="background-color: {{$child_order->status?->status_color ?? 'transparent'}}">
                                        @if(isset($child_order->last_log->sub_status))
                                            {{$child_order->last_log?->sub_status?->name ?? null}}
                                        @else
                                            {{$child_order->last_log?->status?->status_name ?? null}}
                                        @endif
                                    </span>
                                    </td>
                                    <td>{{$child_order->station?->worker?->name ?? null}}</td>
                                    <td>{{$child_order->date_started}}</td>
                                    <td>
                                        @php
                                            $dateStarted = \Carbon\Carbon::parse($child_order->created_at);
                                            $now = \Carbon\Carbon::now();
                                            $timeSpent = $dateStarted->diff($now);
                                        @endphp
                                        {{ $timeSpent->days > 0 ? $timeSpent->days . 'd ' : '' }}
                                        {{ $timeSpent->h > 0 ? $timeSpent->h . 'h ' : '' }}
                                        {{ $timeSpent->i > 0 ? $timeSpent->i . 'm' : '' }}
                                    </td>
                                    <td>
                                        @if(isset($child_order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($child_order->deadline)))
                                            <img src="{{asset('icons/exclaimatio.svg')}}" alt="">
                                        @elseif(isset($child_order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($child_order->deadline)->subDays(2)))
                                            <span class="fw-bold text-danger">{{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($child_order->deadline)),0)}} hours left</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <button class="edit-btn" onclick="editStatus(this)" data-id="{{$child_order->id}}" data-status="{{$child_order->status_id}}" data-workstation="{{$child_order->workstation_id}}">
                                            <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                                        </button>
                                        <button class="edit-btn" onclick="viewDetails('{{$child_order->id}}','{{$child_order->order_id}}')">
                                            View details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
  </table>
    <div class="row justify-content-end d-flex">
        <div class="col-12 col-sm-3">
            {{ $orders->links() }}
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
        <button class="btn pdf-btn">
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

    <x-modal id="editStatusModal" title="Update Status">
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
</div>
@endsection
@section('footer_scripts')
    <script>
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
                        let comments = response.order.comments;
                        let child_orders = response.order.children;

                        $('#order_logs').empty();
                        $.each(logs, function(index, value) {
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

                        $('#comments_container').empty();
                        $.each(comments, function(index, value) {
                            var originalDate = value.created_at.trim();
                            var formattedDate = formatDate(originalDate);
                            var initial = value.user.name.charAt(0);
                            let html = `<div class="comment">
                                            <div class="comment-body">
                                                <div class="d-flex justify-content-between align-items-center flex-row">
                                                    <span class="comment-user" data-initial="${initial}">${value.user.name}</span>
                                                    <span class="">${formattedDate}</span>
                                                </div>
                                                <p class="comment-text">${value.comment}</p>
                                            </div>
                                            <div class="comment-footer">
                                                <button class="btn">Reply</button>
                                            </div>
                                        </div>`;
                            $('#comments_container').append(html);
                        });

                        if(status){
                            if(status.sub_status){
                                $('#modal_status_text').empty().html(status.sub_status.name).css('background-color',status.status.status_color);
                            }else{
                                $('#modal_status_text').empty().html(status.status.status_name).css('background-color',status.status.status_color);
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
                        let comment = response.comment;
                        var originalDate = comment.created_at.trim();
                        var formattedDate = formatDate(originalDate);
                        var initial = comment.user.name.charAt(0);
                        let html = `<div class="comment">
                                            <div class="comment-body">
                                                <span class="comment-user" data-initial="${initial}">${comment.user.name}</span>
                                                <span class="comment-date">${formattedDate}</span>
                                                <p class="comment-text">${comment.comment}</p>
                                            </div>
                                            <div class="comment-footer">
                                                <button class="btn">Reply</button>
                                            </div>
                                        </div>`;
                        $('#comments_container').append(html);
                    }else{
                        show_toast(response.message,'error');
                    }

                },
                error: function (response) {
                }
            })
        }


        $(document).ready(function() {
            function performSearch(query) {
                let productFilter = $('#product-filter').val(); 

                $.ajax({
                    url: '{{ route('search.orders') }}',
                    type: 'GET',
                    data: { 
                        query: query,
                        product: productFilter
                    },
                    success: function(response) {
                        $('#ordersBody').empty();

                        if (response.orders.length > 0) {
                            $.each(response.orders, function(index, order) {
                                var dateStarted = new Date(order.date_started);
                                var now = new Date();
                                var timeDiff = now - dateStarted;

                                var days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));

                                var timeSpentString = (days > 0 ? days + 'd ' : '') + (hours > 0 ? hours + 'h ' : '') + (minutes > 0 ? minutes + 'm' : '');

                                var row = '<tr>' +
                                    '<td>' + order.order_id + '</td>' +
                                    '<td><span class="status" style="background-color: '+order.status.status_color+'">' + (order.status ? order.status.status_name : '') + '</span></td>' +
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

            $('#searchInput, #product-filter').on('change keyup', function() {
                performSearch($('#searchInput').val());
            });
        });


    </script>
@endsection
