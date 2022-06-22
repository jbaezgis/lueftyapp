<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - Luefty</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords')">

        <meta property="og:description" content="@yield('description')" />
        <meta property="og:title" content="@yield('title') - Luefty" />
        <meta property="og:url" content="https://app.yoelbaez.com" />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="{{ app()->getLocale() }}" />
        <meta property="og:locale:alternate" content="es_ES" />
        <meta property="og:site_name" content="Luefty" />
        <meta property="og:image" content="@yield('og-image')" />
        <meta property="og:image:url" content="@yield('og-image-url')" />

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        @livewireStyles
    </head>
    <body>
        <div class="bg-blue-600 px-5 py-4 text-white fixed top-0 w-full">
            <x-jet-application-logo/>
        </div>
        <div class="font-sans text-gray-900 antialiased pt-10">
            {{ $slot }}
        </div>
        <x-mobile-menu/>
        @livewireScripts
    </body>
</html>
