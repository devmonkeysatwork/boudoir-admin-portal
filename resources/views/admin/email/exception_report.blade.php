<!DOCTYPE html>
<html>
    <head>
        <title>Exception Occurred</title>
    </head>
    <body class="mail-template" style="font-family: Arial, sans-serif; font-size: 16px; color: #333;">
        <h2 style="text-align: center; line-height: normal; margin-bottom: 20px;">An exception occurred in the application:</h2>
        <p><strong>Message:</strong> {{ $exceptionMessage }}</p>
        <p><strong>Trace:</strong></p>
        <pre>{{ $exceptionTrace }}</pre>
    </body>
</html>
