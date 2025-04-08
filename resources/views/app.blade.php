
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Dynamic Color Variables -->
        @php
        $colorScheme = \App\Models\WebConfig::getColorScheme();
        @endphp
        <style>
            :root {
                --color-primary: {{ $colorScheme['primary']['DEFAULT'] }};
                --color-primary-hover: {{ $colorScheme['primary']['hover'] }};
                --color-primary-text: {{ $colorScheme['primary']['text'] }};
                --color-secondary: {{ $colorScheme['secondary']['DEFAULT'] }};
                --color-secondary-hover: {{ $colorScheme['secondary']['hover'] }};
                --color-secondary-text: {{ $colorScheme['secondary']['text'] }};
                --color-header-bg: {{ $colorScheme['header_background'] }};
                --color-footer-bg: {{ $colorScheme['footer_background'] }};
                --color-content-bg: {{ $colorScheme['content_background'] }};
                --color-dark: {{ $colorScheme['dark']['DEFAULT'] }};
                --color-dark-lighter: {{ $colorScheme['dark']['lighter'] }};
                --color-dark-card: {{ $colorScheme['dark']['card'] }};
                --color-dark-sidebar: {{ $colorScheme['dark']['sidebar'] }};
            }
        </style>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</html>
