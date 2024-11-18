<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{asset('assets/css/login.css')}}">
    {{--    @vite(['resources/css/login.css', 'resources/js/app.js'])--}}
</head>

<body>
<img src="{{ asset('images/logo.png') }}" class="mx-auto" width="200px" alt="The Boudoir Album">
<div class="login-container flex-row">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->token }}">
        <div class="form-group">
            <label for="email">Email address:</label>
            <input type="email" id="email" name="email" value="{{$request->email}}" required autofocus autocomplete="username">
            @if ($errors->has('email'))
                <span class="error">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                @if ($errors->has('password'))
                    <span class="error">{{ $errors->first('password') }}</span>
                @endif
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                @if ($errors->has('password_confirmation'))
                    <span class="error">{{ $errors->first('password') }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit">Reset Password</button>
        </div>
    </form>
</div>
<script src="{{asset('assets/js/app.js')}}"></script>
</body>

</html>

