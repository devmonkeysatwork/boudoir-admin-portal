@extends('layouts.app')

@section('content')
<div class="workstations">
  <h1>Notifications</h1>
  <table id="notification_list">
    <tbody>
        @foreach($notifications as $notification)
          <tr>
              <td>
                  <p class="p14 position-relative fw-normal mb-0">
                      @if($notification->type == \App\Models\Notifications::typeComment)
                           {{$notification->comment?->user?->name}} added a comment on order id <span class="fw-bold">{{$notification->comment?->order?->order_id}}</span>
                          @php
                            $order_id = $notification->comment?->order?->order_id
                          @endphp
                      @else
                          {{$notification->log?->user?->name}} updated a status to <span class="fw-bold">{{$notification->log?->status?->status_name}}</span>
                          @if(isset($notification->log?->sub_status_id))
                            -> {{$notification->log?->sub_status?->name}}
                          @endif
                          for order id <span class="fw-bold">{{$notification->log?->order_id}}</span>
                          @php
                              $order_id = $notification->log?->order_id
                          @endphp
                      @endif
                          <br>
                          <a href="{{route('admin.orders')}}?order_id={{$order_id}}&tab=open">View Details</a>
                          <span class="notification_time">{{$notification->created_at}}</span>
                  </p>
              </td>
          </tr>
        @endforeach
    </tbody>
  </table>

  <x-modal id="workstationModal" title="Team">
    <div class="workstation-details">
      <div class="search-container">
        <input type="text" class="search-input" placeholder="Search">
        <img src="{{ asset('icons/search.png') }}" alt="Search Icon" class="search-icon">
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Order #</th>
            <th>Time in Production</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Order #00021</td>
            <td>3h59m</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Order #00025</td>
            <td>5h07m</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Order #00030</td>
            <td>2h01m</td>
          </tr>
          <tr>
            <td>4</td>
            <td>Order #00032</td>
            <td>6h53m</td>
          </tr>
          <tr>
            <td>5</td>
            <td>Order #00045</td>
            <td>1h16m</td>
          </tr>
          <tr>
            <td>6</td>
            <td>Order #00049</td>
            <td>0h50m</td>
          </tr>
          <tr>
            <td>7</td>
            <td>Order #00068</td>
            <td>5h40m</td>
          </tr>
          <tr>
            <td>8</td>
            <td>Order #00072</td>
            <td>8h58m</td>
          </tr>
        </tbody>
      </table>
      <div class="pagination">
        Showing 1-04 of 04
      </div>
    </div>
    <x-slot name="footer">
      <button class="btn cancel-btn" onclick="document.getElementById('workstationModal').style.display='none'">Cancel</button>
    </x-slot>
  </x-modal>
</div>
@endsection
