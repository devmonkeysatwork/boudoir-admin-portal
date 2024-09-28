<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000; /* Black border for table cells */
            padding: 10px; /* Space inside the cells */
            text-align: left; /* Align text to the left */
        }

        th {
            background-color: #f2f2f2; /* Light grey background for header */
            font-weight: bold; /* Bold text for headers */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Light grey background for even rows */
        }

        tr:hover {
            background-color: #e0e0e0; /* Highlight row on hover */
        }

        caption {
            font-size: 1.5em; /* Larger font for caption */
            margin: 10px; /* Margin around the caption */
        }

    </style>
</head>
<body>
    @if(isset($orders) && count($orders))
    <table class="table table-responsive">
        <thead>
        <tr>
            <th>Order #</th>
            <th>Priority</th>
            <th>Phase</th>
            <th>Team Member</th>
            <th>Date Started</th>
            <th>Time in Production</th>
            <th>Late</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>
                    @if($order->is_rush)
                        Rush
                    @else
                        -
                    @endif
                </td>
                <td>
              <span class="status" style="background-color: {{$order->status?->status_color ?? 'transparent'}};color: #FFF;padding: 8px 14px;">
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
                        Late
                    @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                        <span class="fw-bold text-danger">{{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hours left</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
