<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin - Djajan</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['"Plus Jakarta Sans"', 'sans-serif'],
                        'body': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'djajan-primary': '#0F623F',
                        'djajan-secondary': '#4CAF50',
                        'djajan-tertiary': '#F59E0B',
                        'djajan-neutral': '#111827',
                        'djajan-light': '#F8FAFC',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-djajan-light antialiased text-djajan-neutral font-body min-h-screen flex flex-col justify-center">
    {{ $slot }}
    @stack('scripts')
</body>
</html>
