@if(isset($orders) && count($orders))
    @foreach($orders as $order)
        <tr>
            <td>
                @if($order->is_rush)
                    <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                @endif
                <button class="edit-btn" onclick="viewDetails('{{$order->id}}','{{$order->order_id}}')">
                    {{ $order->order_id }}
                </button>
            </td>
            <td>
              <span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}}">
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
            <td>{{ $order->date_started }}</td>
            <td>
                @php
                    $dateStarted = \Carbon\Carbon::parse($order->created_at);
                    $now = \Carbon\Carbon::now();
                    $workingTime = calculateWorkingTime($dateStarted, $now);
                @endphp

                {{ $workingTime['months'] > 0 ? $workingTime['months'] . 'm ' : '' }}
                {{ $workingTime['days'] > 0 ? $workingTime['days'] . 'd ' : '' }}
                {{ $workingTime['hours'] > 0 ? $workingTime['hours'] . 'h ' : '' }}
                {{ $workingTime['minutes'] > 0 ? $workingTime['minutes'] . 'm' : '' }}
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
                @if(Auth::user()->role_id == 1)
                    <button class="edit-btn" onclick="editStatus(this)" data-id="{{$order->id}}" data-status="{{$order->status_id}}" data-workstation="{{$order->workstation_id}}">
                        <img src="{{ asset('icons/warning.svg') }}" alt="Edit Icon" width="20px">
                    </button>
                @endif

                @if(isset($orderLog) && $orderLog->order_id == $order->order_id)
                    <button type="button" class="btn bg-transparent ms-2" onclick="endOrderPhase()">
                        <img src="{{ asset('icons/complete_order.svg') }}" alt="Complete Icon" width="20px">
                    </button>
                @else
                    <button data-id="{{$order->order_id}}" type="button" class="btn btn-start-order" data-bs-toggle="modal" data-bs-target="#startWorkModel">
                        Start order
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
@endif
