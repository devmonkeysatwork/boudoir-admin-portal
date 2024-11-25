<!DOCTYPE html>
<html>

<head>
    <title>Welcome</title>
</head>

<body>
<div class="mail-template" style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
    <h1 style="text-align: center; line-height: normal; margin-bottom: 20px;">Please login to the portal using below credentials.</h1>

    <div style="padding: 20px; white-space: pre-line;">
        Reset your password <a href="{{$reset_link??''}}">here</a>. <br>
        OR <a href="{{$url??''}}" style="color: #000;text-decoration: none;">login here.</a> with below credentials.<br>
        <strong>Email</strong>: {{$email??''}}<br>
        <strong>Username</strong>: {{$username??''}}<br>
        <strong>Password</strong>: {{$pwd??''}}
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
