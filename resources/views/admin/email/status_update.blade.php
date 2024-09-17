<!DOCTYPE html>
<html>

<head>
    <title>{{ $subject }}</title>
</head>

<body>
<div class="mail-template" style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    <h1 style="text-align: center; line-height: normal; margin-bottom: 20px;">{{ $subject }}</h1>

    <div style="padding: 20px; white-space: pre-line;">
        {!! $content !!}
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
