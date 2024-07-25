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
          <tr data-open-modal="orderModal">
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
        <ul>
          <li class="log-entry">
            <span class="log-desc">Danielle scanned Order #00002 to Workstation #1</span>
            <span class="log-date">Apr 16 at 3:28 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Dora changed status of Order #00002 to Issue with Print</span>
            <span class="log-date">Apr 16 at 3:28 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Sarah added new print layout to Order #00002</span>
            <span class="log-date">Apr 17 at 9:45 am</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Emily moved Order #00002 to Quality Check</span>
            <span class="log-date">Apr 17 at 1:10 pm</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Danielle reassigned Order #00002 to Workstation #3 for reprinting</span>
            <span class="log-date">Apr 18 at 11:30 am</span>
          </li>
          <li class="log-entry">
            <span class="log-desc">Danielle reassigned Order #00002 to Workstation #3 for reprinting</span>
            <span class="log-date">Apr 18 at 11:30 am</span>
          </li>
        </ul>
      </div>
      <div class="comments">
        <h2>Comments</h2>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="D">Danielle</span>
            <span class="comment-date">Apr 16 at 3:29 pm</span>
            <p class="comment-text">I noticed a slight color mismatch in the print. I'll recheck the printer settings.</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="D">Dora</span>
            <span class="comment-date">Apr 16 at 3:29 pm</span>
            <p class="comment-text">The album cover material seems off. Need to confirm with the client.</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>
        <div class="comment">
          <div class="comment-body">
            <span class="comment-user" data-initial="S">Sarah</span>
            <span class="comment-date">Apr 17 at 9:45 am</span>
            <p class="comment-text">Added new layout design based on client's feedback. Looks good!</p>
          </div>
          <div class="comment-footer">
            <button class="btn">Reply
          </div>
        </div>

      </div>
      <x-slot name="footer">
        <button class="btn pdf-btn">
          <img src="{{ asset('icons/pdf.png') }}" alt="PDF">View Order
        </button>
        <div class="new-comment">
          <textarea placeholder="Write a message..."></textarea>
          <button class="msg-btn">
            <img src="{{ asset('icons/attach.png') }}" alt="Attach file">
          </button>
          <button class="msg-btn">
            <img src="{{ asset('icons/media.png') }}" alt="Media">
          </button>
          <button class="send-btn">
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
    </script>
@endsection
