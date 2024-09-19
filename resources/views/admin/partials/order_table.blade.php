@if(isset($orders) && count($orders))
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->order_id }}</td>
            <td>
                @if($order->is_rush)
                    <img src="{{asset('icons/rush.svg')}}" alt="Rush">
                @else
                    -
                @endif
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
                <button class="edit-btn" onclick="editStatus(this)" data-id="{{ $order->id }}" data-status="{{ $order->status_id }}" data-workstation="{{ $order->workstation_id }}">
                    <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                </button>
                <button class="edit-btn" onclick="viewDetails('{{ $order->id }}','{{ $order->order_id }}')">
                    View details
                </button>
            </td>
        </tr>
    @endforeach
@endif
