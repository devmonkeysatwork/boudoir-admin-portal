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
        <th>Phase</th>
        <th>Team Member</th>
        <th>Date Started</th>
        <th>Time in Production</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
          <tr>
            <td>{{$order->order_id}}</td>
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
    <span class="status completed">Completed</span>
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
                            @foreach($statuses as $status)
                                <option value="{{$status->id}}">{{$status->status_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="status-color">WorkStation</label>
                        <select name="edit_workstation" class="form-select" id="edit_workstation">
                            @foreach($workstations as $workstation)
                                <option value="{{$workstation->id}}">{{$workstation->workstation_number}}</option>
                            @endforeach
                        </select>
                    </div>
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
        let activeOrder = 0;
        function editStatus(me){
            $('#edit_id').val($(me).data('id'));
            $('#edit_status').val($(me).data('status'));
            $('#edit_workstation').val($(me).data('workstation'));
            console.log($(me).data('status'));
            console.log($(me).data('workstation'));
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
                        let logs = response.order.logs;
                        let comments = response.order.comments;
                        $('#order_logs').empty();
                        $('#comments_container').empty();
                        $.each(logs, function( index, value ) {
                            let html = `<li class="log-entry">
                                        <span class="log-desc">${value.user.name} update the status to ${value.status.status_name} for order ${value.order_id}</span>
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
                                                <span class="comment-user" data-initial="${initial}">${value.user.name}</span>
                                                <span class="comment-date">${formattedDate}</span>
                                                <p class="comment-text">${value.comment}</p>
                                            </div>
                                            <div class="comment-footer">
                                                <button class="btn">Reply</button>
                                            </div>
                                        </div>`;
                            $('#comments_container').append(html);
                        });


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
        function formatDate(dateString) {
            var date = new Date(dateString);
            var options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
            };
            return new Intl.DateTimeFormat('en-US', options).format(date);
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
