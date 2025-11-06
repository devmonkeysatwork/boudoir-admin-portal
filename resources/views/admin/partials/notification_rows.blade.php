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
