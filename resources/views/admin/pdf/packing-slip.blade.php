<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Portal') }}</title>
</head>

<body>
    <div class="container">
        <table class="head container">
            <tr>
                <td>
                    <div class="order-number">Customer # - <b>{{ $order->customer_name }}</b></div>
                    <div class="order-number">Order # - <b>{{ $order->order_id }}</b></div>
                    <div class="order-date">Order Date - {{ \Illuminate\Support\Carbon::parse($order->date_started)->format('Y-m-d') }}</div>
                </td>
                <td>
                    @if ($order->signature_required)
                        <p><strong>Signature Required:</strong> {{ ucfirst($order->signature_required) }}</p>
                    @endif
                </td>
                <td class="shop-info" style="vertical-align: middle;">
                    <img src="" style="width:200px;">
                </td>
            </tr>
        </table>

        <table class="order-details">
            <tbody>
            <tr>
                @foreach ($order->items as $item)
                    <td>
                        <table>
                            <tr>
                                <td style='width: 50%;'>
                                    <img src="{{ asset('icons/green-checkmark.png') }}" />
                                </td>
                                <td style='width: 50%;'>
                                    <img src="{{ asset('icons/green-checkmark.png') }}" style="width: 100%; max-height: 150px;" />
                                </td>
                            </tr>
                        </table>
                        <div><b>Line item quantity: </b>{{ $item->quantity }}</div>
                        <br><br>
                        <div class="item-name">{{ $item->product_name }}</div>
                        <div><b>Selected Quantity: </b> {{ $item->quantity }}</div>
                        @foreach ($item->attributes as $attribute)
                            <div><b>{{ $attribute->type }}:</b> {{ $attribute->title}}</div>
                        @endforeach
                        <br><br>
                    </td>
                @endforeach
            </tr>
            </tbody>
        </table>
        <br /><br />
        <table class="order-data-addresses">
            <tr>
                <td class="address billing-address">
                    <h3>Billing Address:</h3>
                    <div>{{ $order['addresses'][0]->address_1 }}</div>
                    <div>Email: {{ $order['addresses'][0]->email }}</div>
                    <div>Phone: {{ $order['addresses'][0]->phone }}</div>
                </td>
                <td class="address shipping-address">
                    <h3>Shipping Address:</h3>
                    <div>{{ $order['addresses'][1]->address_1 }}</div>
                    <div>Email: {{ $order['addresses'][1]->email }}</div>
                    <div>Phone: {{ $order['addresses'][1]->phone }}</div>
                </td>
            </tr>
        </table>

        <div class="customer-notes">
            @if ($order->customer_note)
                <h3>Customer Notes</h3>
                <div>{{ $order->customer_note }}</div>
            @endif
        </div>
    </div>
</body>
</html>
