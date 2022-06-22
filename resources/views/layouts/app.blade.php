<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{asset('images/icon.png')}}" type="image/png">

        <title>@yield('title') - Dominican Shuttles</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords')">

        <meta property="og:description" content="@yield('description')" />
        <meta property="og:title" content="@yield('title') - Dominican Shuttles" />
        <meta property="og:url" content="https://admin.dominicanshuttles.com" />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="{{ app()->getLocale() }}" />
        <meta property="og:locale:alternate" content="es_ES" />
        <meta property="og:site_name" content="Dominican Shuttles" />
        <meta property="og:image" content="@yield('og-image')" />
        <meta property="og:image:url" content="@yield('og-image-url')" />

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        {{-- @wireUiScripts --}}
        <wireui:scripts />
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />
        <x-notifications />
        <x-dialog />

        <div class="min-h-screen">
            
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        
    <footer class="max-w-7xl mx-auto ">
        <div class="container px-2 py-4 mx-auto">
            <div class="">
                <div class="w-full">
                    <div class="">
                        {{-- <div class="flex justify-center">
                            <a href="#" class="text-xl font-bold text-gray-800 hover:text-gray-700 ">
                                <x-jet-application-logo />
                            </a>
                        </div> --}}
                        
                        {{-- <p class="max-w-md mt-2 text-gray-500">{{ now()->year }} - Dominican Shuttles</p> --}}
                    </div>
                </div>
                <div class="mt-6 mb-6 border-b"></div>
                <div class="mt-6 lg:mt-0 lg:flex-1">
                    <div class="flex justify-center gap-6">
                        <div>
                            <h3 class="text-gray-700 uppercase ">{{ __('About') }}</h3>
                            <a href="#" class="block mt-2 text-sm text-gray-600 hover:underline">{{ __('Company') }}</a>
                            <a href="#" class="block mt-2 text-sm text-gray-600 hover:underline">{{ __('Privacy Policy') }}</a>
                            {{-- <a href="#" class="block mt-2 text-sm text-gray-600 hover:underline">Careers</a> --}}
                        </div>

                        <div>
                            <h3 class="text-gray-700 uppercase">{{ __('Contact') }}</h3>
                            <span class="block mt-2 text-sm text-gray-600 hover:underline">+1 829 820 5200</span>
                            <span class="block mt-2 text-sm text-gray-600 hover:underline">info@dominicanshuttles.com</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <hr class="h-px my-6 bg-gray-300 border-none "> --}}
            <div class="mt-6 mb-6 border-b"></div>

            <div>
                <p class="text-center text-gray-500 ">{{ __('This company belongs to:') }}</p>
                <div class="flex justify-center">
                    <div class="text-center"><a class="text-blue-600" href="http://luefty.com" target="_blank"></a><img class="h-12" src="{{ asset('images/luefty-logo.svg') }}" alt="Luefty Logo"></div>
                </div>
            </div>
        </div>
    </footer>

        @stack('modals')

        @livewireScripts
    </body>
</html>
