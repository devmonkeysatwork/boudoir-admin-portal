<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Barcode') }}</title>
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    <!-- ckeditor -->
    <script src="https://cdn.ckeditor.com/4.17.0/standard/ckeditor.js"></script>
    <!-- pickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <!-- choices.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <!-- tablesorter CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/theme.default.min.css">
    <!-- notyf -->
    <link rel="stylesheet" href="{{ asset('assets/css/notyf.css')}}" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}">
    {{--    @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
</head>

<body>
<div class="wrapper">
    @include('partials.sidebar')
    <div class="main">
        @include('partials.header')
        <div class="content">
            @yield('content')
            @unless (Request::is('dashboard') || Request::is('settings*'))
                @include('partials.footer')
            @endunless
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- tablesorter JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Pusher JS -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<!-- Pickr JS -->
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<!-- notyf JS -->
<script src="{{asset('assets/js/notyf.js')}}"></script>
<!-- Custom JS -->
<script src="{{asset('assets/js/app.js')}}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Pickr for create status modal
        const createPickr = Pickr.create({
            el: '#create-status-color-picker',
            theme: 'classic', // or 'monolith', or 'nano'
            default: '#007BFF',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    hsva: true,
                    cmyk: true,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        });

        createPickr.on('change', (color, instance) => {
            const colorValue = color.toHEXA().toString();
            document.querySelector('#status-color').value = colorValue;
            document.querySelector('#create-preview').style.backgroundColor = colorValue;
        });

        createPickr.on('save', (color, instance) => {
            const colorValue = color.toHEXA().toString();
            document.querySelector('#status-color').value = colorValue;
            createPickr.hide();
        });
    });
</script>

<script src="https://cdn.ckeditor.com/4.17.0/standard/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        CKEDITOR.replace('email-content', {
            toolbar: [{
                name: 'paragraph',
                items: ['Format']
            },
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', 'Blockquote']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false,
        });

        CKEDITOR.replace('edit-email-content', {
            toolbar: [{
                name: 'paragraph',
                items: ['Format']
            },
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', 'Blockquote']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false,
        });
    });
</script>
<script src="{{asset('assets/js/qrCode.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="{{asset('assets/js/notyf.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderSort = new Choices('#order-sort', {
            searchEnabled: false,
            itemSelectText: '',
        });

        const teamSort = new Choices('#team-sort', {
            searchEnabled: false,
            itemSelectText: '',
        });

        const workstationSort = new Choices('#workstation-sort', {
            searchEnabled: false,
            itemSelectText: '',
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const filterDate = new Choices('#filter-date', {
            searchEnabled: false,
            itemSelectText: '',
        });
        const filterProduct = new Choices('#filter-product', {
            searchEnabled: false,
            itemSelectText: '',
        });
        const filterStatus = new Choices('#filter-status', {
            searchEnabled: false,
            itemSelectText: '',
        });
        const filterPriority = new Choices('#filter-priority', {
            searchEnabled: false,
            itemSelectText: '',
        });
    });
    function show_toast(message,type='warning'){

        var notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            },
            types: [
                {
                    type: 'warning',
                    background: '#f89406',
                    icon: {
                        className: 'fa fa-exclamation-circle',
                        tagName: 'i',
                        color: 'white'
                    }
                },
            ],
            duration: 5000,
            alertIcon: 'fa fa-exclamation-circle',
            confirmIcon: 'fa fa-check-circle'
        })

        notyf.open({
            type: type,
            message: message
        });

    }
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
        cluster: '{{env('PUSHER_APP_CLUSTER')}}',
        forceTLS: '{{env('PUSHER_FORSE_TLS')}}' // Set to false for local development
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('my-event', function(data) {
        if($('#notifications .no_notif').length){
            $('#notifications').empty();
            $('.notification_footer').removeClass('d-none');
        }
        if(data.message.comment){
            let comment = data.message.comment;
            let date = formatDate(comment.created_at);
            let html = `<a href="/orders?order_id=${data.message.order_id}&tab=open" class="d-flex flex-column gap-1 notification">
                    <p class="m-0">${comment.user.name} added a comment on order id ${data.message.order_id}</p>
                    <p class="p12 m-0">on ${date}</p>
                </a>`;
            $('#notifications').append(`${html}`);
            playNotifications();
            addNotification();
        }
        else if(data.message.log){
            let log = data.message.log;
            console.log(log);
            let date = formatDate(log.created_at);
            let html = `<a href="/orders?order_id=${log.order_id}&tab=open" class="d-flex flex-column gap-1 notification">
                    <p class="m-0">${log.user.name} changed the status to ${log.status.status_name} for order id ${log.order_id}</p>
                    <p class="p12 m-0">on ${date}</p>
                </a>`;
            $('#notifications').append(`${html}`);
            playNotifications();
            addNotification();
        }
    });

    function playNotifications(){
        var audio = new Audio('{{asset('assets/audio/notification.mp3')}}');
        audio.play();
    }
    function toggleNotifications(){
        $('#notifications_div').toggle();
    }
    function addNotification(){
        let counts = parseInt($('.notification_counter').text()) + 1;
        $('.notification_counter').empty().html(counts);
    }
    function formatDate(dateString) {
        var date = new Date(dateString);
        var options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
        };
        return new Intl.DateTimeFormat('en-US', options).format(date);
    }






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

        // Restart scanning when the button is clicked
        $('.btn-start-order').click(function() {
            $('#qr-input').val(''); // Clear the input field
            $('#order_number_title').text($(this).data('id'));
            startScanning(); // Restart scanning
        });
        $('#startWorkModel').on('hidden.bs.modal', function () {
            html5QrCode.stop();
        });
    });
    function updateOrderStatus() {
        const orderNumber = $('#order_number_title').text().trim(); // Get order number from modal
        const statusId = $('#status_update').val();
        const qrInput = $('#qr-input').val();

        // Verify that the scanned QR matches the order ID
        if (qrInput !== orderNumber) {
            alert('Scanned QR code does not match the order number.');
            return;
        }
        let data  = new FormData();
        data.append('_token','{{@csrf_token()}}');
        data.append('order_id',orderNumber);
        data.append('status_id',statusId);

        $.ajax({
            url: '{{route('order.add_log')}}', // Replace with your actual endpoint
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend() {
                show_loader();
            },
            success: function(response) {
                if (response.status == 200) {
                    $('#startWorkModel .error').text(response.message).removeClass('text-danger').addClass('text-success');
                    location.reload();
                } else {
                    $('#startWorkModel .error').text(response.message);
                }
                hide_loader();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                hide_loader();
            }
        });
    }

</script>
@yield('footer_scripts')
<div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" id="startWorkModel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content w-100">
            <div class="modal-header">
                <h5 class="modal-title mx-auto">Order #<span id="order_number_title"></span></h5>
                <button type="button" class="btn-close position-absolute end-30" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column gap-3">
                    <input type="hidden" id="qr-input" class="form-control" placeholder="Scan barcode here" autofocus>
                    <p id="barcode_result"></p>
                    <div id="qr-reader" style="width: 300px; height: fit-content;margin: 0px auto"></div>
                    <p class="p14 text-center">Scan the barcode on the order sheet PDF to proceed.</p>
                    <div class="form-group">
                        <input type="hidden" id="edit_id" name="id">
                        <label for="status-name">Status</label>
                        <select class="form-select" id="status_update">
                            <option value="0">Select One</option>
                            @foreach($statuses??[] as $status)
                                <option value="{{$status->id}}">{{$status->status_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="error text-danger p12"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary create-btn" onclick="updateOrderStatus()">Confirm</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
