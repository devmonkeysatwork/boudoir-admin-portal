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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</script>