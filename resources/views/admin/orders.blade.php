@extends('layouts.app')

@section('content')
<div class="orders">
  <h1>Order List</h1>

  <div class="filters">
      <form class="filter-bar" action="">
          <div class="filter-item">
              <button class="filter-btn">
                  <img src="{{ asset('icons/filter.png') }}" alt="Filter Icon">
                  <span>Filter By</span>
              </button>
          </div>
          <div class="filter-item">
              <select class="sort-select" id="filter-date" name="filter_date">
                  <option value="" disabled selected>Date</option>
                  <option value="oldest" {{$filter_date && $filter_date == 'oldest'?'Selected':''}}>Oldest</option>
                  <option value="newest" {{$filter_date && $filter_date == 'newest'?'Selected':''}}>Newest</option>
              </select>
          </div>
          <div class="filter-item">
              <select class="sort-select" id="filter-product" name="filter_product">
                  <option value="" disabled selected>Product</option>
                  @foreach($products as $product)
                      <option value="{{ $product->product_name }}" {{$filter_product && $filter_product == $product->product_name?'Selected':''}}>{{ $product->product_name }}</option>
                  @endforeach
              </select>
          </div>
          <div class="filter-item">
              <select class="sort-select" id="filter-status" name="filter_status">
                  <option value="" disabled selected>Order Status</option>
                  @foreach($statuses??[] as $status)
                      <option value="{{$status->id}}" {{$filter_status && $filter_status == $status->id?'Selected':''}}>{{$status->status_name}}</option>
                  @endforeach
              </select>
          </div>
          <div class="filter-item">
              <select class="sort-select" id="filter-priority" name="filter_priority">
                  <option value="" disabled selected>Priority</option>
                  <option value="2" {{$filter_priority && $filter_priority == '2'?'Selected':''}}>Normal</option>
                  <option value="1" {{$filter_priority && $filter_priority == '1'?'Selected':''}}>Rush</option>
              </select>
          </div>
          <div class="filter-item">
              <button class="reset-btn" type="button">
                  <img src="{{ asset('icons/reset.png') }}" alt="Reset">Reset Filter
              </button>
          </div>
      </form>
    <div class="orders-search">
      <input type="text" id="searchInput" placeholder="Search">
      <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
    </div>
  </div>


  <table id="ordersTable" class="tablesorter">
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
            <td>{{$order->date_started}}</td>
            <td>
                @php
                    $dateStarted = \Carbon\Carbon::parse($order->created_at);
                    $now = \Carbon\Carbon::now();
                    $timeSpent = $dateStarted->diff($now);
                @endphp
                {{ $timeSpent->m > 0 ? $timeSpent->m . 'm ' : '' }}
                {{ $timeSpent->d > 0 ? $timeSpent->d . 'd ' : '' }}
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
            @if(isset($order) && isset($order->children) && count($order->children))
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
                            @foreach($order?->children as $child_order)
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
                                        {{ $timeSpent->m > 0 ? $timeSpent->m . 'm ' : '' }}
                                        {{ $timeSpent->d > 0 ? $timeSpent->d . 'd ' : '' }}
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


            $('#filter-date,#filter-product,#filter-status,#filter-priority').on('change',function () {
                $('.filter-bar').submit();
            })

            $('.reset-btn').on('click', function(){
                window.location.href = '{{route('admin.orders')}}';
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

            $('#searchInput').on('keyup', function() {
                var query = $(this).val();
                performSearch(query);
            });

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

@push('scripts')
<script>
    $(document).ready(function() {
        $("#ordersTable").tablesorter();
    });
</script>
@endpush
