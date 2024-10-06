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
                <button class="edit-btn" onclick="editStatus(this)" data-id="{{ $order->id }}" data-status="{{ $order->status_id }}" data-workstation="{{ $order->workstation_id }}">
                    <img src="{{ asset('icons/edit.png') }}" alt="Edit Icon">
                </button>
                <button class="edit-btn" onclick="viewDetails('{{ $order->id }}','{{ $order->order_id }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                    </svg>
                </button>
            </td>
        </tr>
    @endforeach
@endif
