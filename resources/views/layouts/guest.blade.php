<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-cover bg-center flex items-center justify-center"
            style="background-image: url('{{ asset('images/background.jpg') }}');">
        <div class="w-full max-w-md p-8 bg-white/80 rounded-xl shadow-2xl border border-beige-300 backdrop-blur">
            {{ $slot }}
        </div>
    </body>

</html>
