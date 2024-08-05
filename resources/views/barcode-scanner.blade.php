<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <!-- Include Bootstrap CSS for modal styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Barcode Scanner</h2>
    <input type="text" id="barcode_input" class="form-control" placeholder="Scan barcode here">
</div>

<!-- Modal -->
<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeModalLabel">Scanned Barcode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="barcode_result"></p>
                <p id="scanner_id" class="text-muted"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and Bootstrap JS for modal functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#barcode_input').focus();
        $('#barcode_input').on('keypress', function(event) {
            if (event.keyCode === 13) { // Enter key pressed
                var barcode = $(this).val();
                var scannerId = extractScannerId(barcode); // Function to parse unique identifier

                $('#barcode_result').text(barcode);
                $('#scanner_id').text('Scanner ID: ' + scannerId);
                $('#barcodeModal').modal('show');
                $(this).val(''); // Clear the input field
            }
        });
    });

    // Function to extract the scanner ID from the barcode data
    function extractScannerId(barcode) {
        // Example logic: assuming the scanner ID is a fixed prefix in the barcode data
        var prefix = 'SCANID-'; // Example prefix
        if (barcode.startsWith(prefix)) {
            return barcode.substring(prefix.length);
        } else {
            return 'Unknown';
        }
    }
</script>
</body>
</html>
