@extends('layouts.app')

@section('content')
<div class="orders">
  <h1>Order List</h1>
  <div class="filters">

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
      <button class="reset-btn">
        <img src="{{ asset('icons/reset.png') }}" alt="Reset">Reset Filter
      </button>
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
            <td><img src="{{asset('icons/rush.svg')}}" alt=""></td>
            <td><span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">{{$order->status?->status_name ?? null}}</span></td>
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
              </td>
          </tr>
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
      <div class="activity-log">
        <h2>Activity Log</h2>
        <ul id="order_logs">
        </ul>
      </div>
      <div class="comments">
        <h2>Comments</h2>
        <div id="comments_container">
            <div class="comment">
                <div class="comment-body">
                    <span class="comment-user" data-initial="D">Danielle</span>
                    <span class="comment-date">Apr 16 at 3:29 pm</span>
                    <p class="comment-text">I noticed a slight color mismatch in the print. I'll recheck the printer settings.</p>
                </div>
                <div class="comment-footer">
                    <button class="btn">Reply</button>
                </div>
            </div>
            <div class="comment">
                <div class="comment-body">
                    <span class="comment-user" data-initial="D">Dora</span>
                    <span class="comment-date">Apr 16 at 3:29 pm</span>
                    <p class="comment-text">The album cover material seems off. Need to confirm with the client.</p>
                </div>
                <div class="comment-footer">
                    <button class="btn">Reply</button>
                </div>
            </div>
            <div class="comment">
                <div class="comment-body">
                    <span class="comment-user" data-initial="S">Sarah</span>
                    <span class="comment-date">Apr 17 at 9:45 am</span>
                    <p class="comment-text">Added new layout design based on client's feedback. Looks good!</p>
                </div>
                <div class="comment-footer">
                    <button class="btn">Reply</button>
                </div>
        </div>
        </div>

      </div>
      <x-slot name="footer">
        <button class="btn pdf-btn">
          <img src="{{ asset('icons/pdf.png') }}" alt="PDF">View Order
        </button>
        <div class="new-comment">
          <textarea placeholder="Write a message..." id="comment_input"></textarea>
{{--          <button class="msg-btn">--}}
{{--            <img src="{{ asset('icons/attach.png') }}" alt="Attach file">--}}
{{--          </button>--}}
{{--          <button class="msg-btn">--}}
{{--            <img src="{{ asset('icons/media.png') }}" alt="Media">--}}
{{--          </button>--}}
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
                // Show or hide the sub-status div based on whether there are visible options
                $('#sub_status_div').toggle(hasVisibleOptions);
            });

            $('#edit_status').trigger('change');

        });
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

        function viewDetails(orderId,title) {
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
                        $('#order_logs').empty();
                        $('#comments_container').empty();
                        $.each(logs, function( index, value ) {
                            let html = `<li class="log-entry">
                                        <span class="log-desc">${value.user.name} update the status to <span class="fw-bold">${value.status.status_name}</span>`;
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
                        $.each(comments, function( index, value ) {
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


                    }else{
                        show_toast(response.message,'error');
                    }

                },
                error: function (response) {
                }
            })
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


    </script>
@endsection
