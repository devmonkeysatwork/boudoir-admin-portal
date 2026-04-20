<table class="table table-bordered tablesorter">
    <thead>
    <tr>
        <th>Order ID</th>
        <th>Status</th>
        <th>Worker</th>
        <th>Started At</th>
        <th>Working Time</th>
        <th>Deadline</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->children as $child_order)
        <tr>
            <td>
                @if($child_order->is_rush)
                    <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                @endif
                <button class="edit-btn" onclick="viewDetails('{{ $child_order->id }}','{{ $child_order->order_id }}')">
                    {{ $child_order->order_id }}
                </button>
            </td>
            <td>
                    <span class="status" style="background-color: {{$child_order->status?->status_color ?? 'transparent'}}">
                        @if(isset($child_order->last_log->sub_status))
                            {{$child_order->last_log?->sub_status?->name ?? null}}
                        @elseif(isset($child_order->last_log->status))
                            {{$child_order->last_log?->status?->status_name ?? null}}
                        @else
                            {{$child_order->status?->status_name ?? null}}
                        @endif
                    </span>
            </td>
            <td>{{ $child_order->station?->worker?->name }}</td>
            <td>{{ $child_order->date_started }}</td>
            <td>
                @php
                    $dateStarted = \Carbon\Carbon::parse($order->created_at);
                    $now = \Carbon\Carbon::now();
                    if(isset($order->date_completed)){
                      $now = \Carbon\Carbon::parse($order->date_completed);
                    }
                    $workingTime = calculateWorkingTime($dateStarted, $now);
                @endphp
                {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
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
                @if(Auth::user()->role_id == 1)
                    <button class="edit-btn" onclick="editStatus(this)" data-id="{{$child_order->id}}" data-status="{{$child_order->status_id}}" data-workstation="{{$child_order->workstation_id}}">
                        <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon" width="20px">
                    </button>
                @endif
                @if(isset($orderLog) && $orderLog->order_id == $child_order->order_id)
                    <button type="button" class="btn bg-transparent ms-2" onclick="endOrderPhase()">
                        <img src="{{ asset('icons/complete_order.svg') }}" alt="Complete Icon" width="20px">
                    </button>
                @else
                    <button data-id="{{$child_order->order_id}}" type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">
                        Start order
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
