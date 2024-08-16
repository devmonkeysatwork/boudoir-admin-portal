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
    <!-- vite -->
    <link rel="stylesheet" href="{{ asset('assets/css/notyf.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
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
</body>

</html>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
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
</script>
<script src="{{asset('assets/js/app.js')}}"></script>
<script src="{{asset('assets/js/jquery.js')}}"></script>
<script>
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
            let html = `<div class="d-flex flex-column gap-1 notification">
                    <p class="m-0">${comment.user.name} added a comment on order id ${data.message.order_id}</p>
                    <p class="p12 m-0">on ${date}</p>
                </div>`;
            $('#notifications').append(`${html}`);
            playNotifications();
            addNotification();
        }
        else if(data.message.log){
            let log = data.message.log;
            let date = formatDate(log.created_at);
            let html = `<div class="d-flex flex-column gap-1 notification">
                    <p class="m-0">${log.updated_by.name} changed the status to ${log.status.status_name} for order id ${log.order_id}</p>
                    <p class="p12 m-0">on ${date}</p>
                </div>`;
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
</script>
@yield('footer_scripts')
