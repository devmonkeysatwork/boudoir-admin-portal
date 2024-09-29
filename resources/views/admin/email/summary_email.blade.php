<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
</head>

<body>
<div class="mail-template" style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    <h1 style="text-align: center; line-height: normal; margin-bottom: 20px;">{{ $title }}</h1>

    @if(isset($rush_orders) && count($rush_orders))
        <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
            <thead>
            <tr>
                <td colspan="5" style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: center; font-weight: bold;">
                    Orders in rush: {{ count($rush_orders) }}
                </td>
            </tr>
            <tr>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Order #</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Status</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Team Member</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Time In production</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rush_orders as $order)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->order_id}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->status?->status_name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->station?->worker?->name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{\Illuminate\Support\Carbon::parse($order->date_started)->diffForHumans()}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                            <br>Order is late
                        @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                            <br><span>Order is due in {{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hrs</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br><br><br>
    @endif


    @if(isset($production_order) && count($production_order))
        <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
            <thead>
            <tr>
                <td colspan="5" style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: center; font-weight: bold;">
                    Orders in production: {{ count($production_order) }}
                </td>
            </tr>
            <tr>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Order #</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Status</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Team Member</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Time In production</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($production_order as $order)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->order_id}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->status?->status_name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->station?->worker?->name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{\Illuminate\Support\Carbon::parse($order->date_started)->diffForHumans()}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                            <br>Order is late
                        @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                            <br><span>Order is due in {{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hrs</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br><br><br>
    @endif
    @if(isset($orders_on_hold) && count($orders_on_hold))
        <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
            <thead>
            <tr>
                <td colspan="5" style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: center; font-weight: bold;">
                    Orders on hold : {{ count($orders_on_hold) }}
                </td>
            </tr>
            <tr>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Order #</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Status</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Team Member</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Time In production</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders_on_hold as $order)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->order_id}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->status?->status_name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->station?->worker?->name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{\Illuminate\Support\Carbon::parse($order->date_started)->diffForHumans()}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                            <br>Order is late
                        @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                            <br><span>Order is due in {{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hrs</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br><br><br>
    @endif
    @if(isset($order_with_issues) && count($order_with_issues))
        <table style="border-collapse: collapse; width: 100%; border: 1px solid #ddd;">
            <thead>
            <tr>
                <td colspan="5" style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: center; font-weight: bold;">
                    Orders with issues : {{ count($order_with_issues) }}
                </td>
            </tr>
            <tr>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Order #</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Status</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Team Member</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Time In production</th>
                <th style="padding: 10px; border-bottom: 1px solid #ddd; background-color: #f4f4f4; text-align: left;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order_with_issues as $order)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->order_id}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->status?->status_name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{$order->station?->worker?->name}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{\Illuminate\Support\Carbon::parse($order->date_started)->diffForHumans()}}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                        @if(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)))
                            <br>Order is late
                        @elseif(isset($order->deadline) && \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($order->deadline)->subDays(2)))
                            <br><span>Order is due in {{round(\Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($order->deadline)),0)}} hrs</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br><br><br>
    @endif
</div>
<div style="padding: 26px 40px 0;">
    <div
        style="font-family: Trade Gothic LT Pro; color: rgba(0, 0, 0, 1);padding: 17px 0 8px; font-style: normal; font-weight: 400; font-size: 14px; line-height: 21px;    text-align: center;">
        © Copyright {{\Illuminate\Support\Carbon::now()->format('Y')}} All rights reserved.
    </div>
</div>
</body>
</html>
