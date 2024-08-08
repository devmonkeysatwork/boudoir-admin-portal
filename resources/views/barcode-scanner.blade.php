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
    <input type="text" id="barcode_input" class="form-control" placeholder="Scan barcode here" autofocus>
    <p id="barcode_result"></p>
</div>


<!-- Include jQuery and Bootstrap JS for modal functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function addOrUpdateStatusRow() {
            $('#barcode_input').on('change', function() {
                var inputValue = $(this).val().trim();
                if (inputValue) {
                    $.ajax({
                        url: "{{route('order.add_log')}}",
                        type: 'POST',
                        data: {
                            input: inputValue
                        },
                        success: function(response) {
                            console.log('Success:', response);
                            $('#barcode_result').empty().html(response.message);
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr.responseText);
                            $('#barcode_result').empty().html(xhr.responseText);
                        }
                    });
                }
            });
        };

        addOrUpdateStatusRow();
    });


</script>
</body>
</html>
