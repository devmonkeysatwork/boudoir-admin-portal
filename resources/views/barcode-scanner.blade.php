@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="container mt-5">
                    <h2>Barcode Scanner</h2>
                    <input type="text" id="barcode_input" class="form-control" placeholder="Scan barcode here">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" tabindex="-1">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#barcode_input').on('keypress', function(event) {
                if (event.keyCode === 13) { // Enter key pressed
                    var barcode = $(this).val();
                    $('#barcode_result').text(barcode);
                    $('#barcodeModal').modal('show');
                    $(this).val(''); // Clear the input field
                }
            });
        });
    </script>
@endsection
