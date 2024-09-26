<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barcode Scanner</title>
    <!-- Include Bootstrap CSS for modal styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('assets/js/qrCode.js')}}"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Barcode Scanner</h2>
    <input type="text" id="qr-input" class="form-control" placeholder="Scan barcode here" autofocus>
    <button type="button" id="restart-button">Restart Scan</button>
    <p id="barcode_result"></p>
</div>

<script>
    $(document).ready(function() {
        const html5QrCode = new Html5Qrcode("qr-reader");

        // Function to start scanning
        function startScanning() {
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
                }
            ).catch(err => {
                // Start failed, handle it
                console.log(`Unable to start scanning: ${err}`);
            });
        }

        // Start scanning on page load
        startScanning();

        // Restart scanning when the button is clicked
        $('#restart-button').click(function() {
            $('#qr-input').val(''); // Clear the input field
            startScanning(); // Restart scanning
        });
    });


</script>
<div id="qr-reader" style="width: 300px; height: 300px;"></div>
</body>
</html>
