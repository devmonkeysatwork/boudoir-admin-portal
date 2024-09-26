<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barcode Scanner</title>
    <!-- Include Bootstrap CSS for modal styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Barcode Scanner</h2>
    <input type="text" id="qr-input" class="form-control" placeholder="Scan barcode here" autofocus>
    <p id="barcode_result"></p>
</div>


<!-- Include jQuery and Bootstrap JS for modal functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize QR Code Scanner
        var html5QrCode = new Html5Qrcode("qr-reader");

        // Start scanning
        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: 250
            },
            (decodedText, decodedResult) => {
                // Handle the result here
                $('#qr-input').val(decodedText);
                html5QrCode.stop(); // Stop scanning after successful read
            },
            (errorMessage) => {
                // Handle scanning error (optional)
                console.log(errorMessage);
            })
            .catch(err => {
                // Start failed, handle it
                console.log(`Unable to start scanning: ${err}`);
            });
    });


</script>
<div id="qr-reader" style="width: 300px; height: 300px;"></div>
</body>
</html>
