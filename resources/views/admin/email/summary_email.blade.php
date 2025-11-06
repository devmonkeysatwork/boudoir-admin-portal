<!DOCTYPE html>
<html>

<head>
    <title>{{ $title }}</title>
</head>

<body>
<div class="mail-template" style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Report Date: {{ $date }}</p>
    </div>

    <div class="summary-box">
        <h2>Summary Overview</h2>
        <div class="summary-item">
            <span class="label">Rush Orders:</span>
            <span class="value">{{ $data['rush_orders_count']??0 }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Production Orders:</span>
            <span class="value">{{ $data['production_orders_count']??0 }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Orders on Hold:</span>
            <span class="value">{{ $data['orders_on_hold_count']??0 }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Late Orders:</span>
            <span class="value">{{ $data['late_orders_count']??0 }}</span>
        </div>
        <h3>📎 Excel Report Attached</h3>
        <p>Please find the detailed daily summary report attached as an Excel file. The report contains multiple sheets.</p>
    </div>
</div>
<div style="padding: 26px 40px 0;">
    <div
        style="font-family: Trade Gothic LT Pro; color: rgba(0, 0, 0, 1);padding: 17px 0 8px; font-style: normal; font-weight: 400; font-size: 14px; line-height: 21px;    text-align: center;">
        © Copyright {{\Illuminate\Support\Carbon::now()->format('Y')}} All rights reserved.
    </div>
</div>
</body>
</html>
