<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO --}}
        <meta name="description" content="Reservasi lapangan bulutangkis online dengan mudah dan cepat.">
        <meta name="keywords" content="reservasi, lapangan, bulutangkis, badminton, online">
        <meta name="author" content="Reservasi Bulutangkis">

        {{-- Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ config('app.name', 'Reservasi Bulutangkis') }}">
        <meta property="og:description" content="Reservasi lapangan bulutangkis online dengan mudah dan cepat.">
        <meta property="og:locale" content="id_ID">

        {{-- Favicon --}}
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="alternate icon" href="/favicon.ico">

        {{-- Inertia Page Title (set via createInertiaApp) --}}
        <title inertia>{{ config('app.name', 'Reservasi Bulutangkis') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        {{-- Ziggy Routes (required for route() helper in Vue) --}}
        @routes

        {{-- Vite Assets --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Inertia Head --}}
        @inertiaHead
    </head>
    <body class="h-full font-sans antialiased bg-cream-bg text-cream-text">
        @inertia
    </body>
</html>
