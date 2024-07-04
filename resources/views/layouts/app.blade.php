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
    <!-- tinymce -->
    <script src="https://cdn.tiny.cloud/1/67ldg66kokeebemvaix2hwu9iu3gou1btkdl63h3wxexo5ki/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <!-- pickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
    <!-- vite -->
    <link rel="stylesheet" href="{{asset('assets/css/app.css')}}">
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

<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
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

        // Initialize Pickr for edit status modal
        const editPickr = Pickr.create({
            el: '#edit-status-color-picker',
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

        editPickr.on('change', (color, instance) => {
            const colorValue = color.toHEXA().toString();
            document.querySelector('#edit-status-color').value = colorValue;
            document.querySelector('#edit-preview').style.backgroundColor = colorValue;
        });

        editPickr.on('save', (color, instance) => {
            const colorValue = color.toHEXA().toString();
            document.querySelector('#edit-status-color').value = colorValue;
            editPickr.hide();
        });
    });
</script>



<script>
    tinymce.init({
        selector: 'textarea#email-content, textarea#edit-email-content',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                  alignleft aligncenter alignright alignjustify | \
                  bullist numlist outdent indent | removeformat',
        menubar: false,
        height: 300
    });
</script>
