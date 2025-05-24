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
    <body class="font-sans antialiased text-gray-900 ">
        <div class="flex items-center min-h-screen pt-6 bg-[#edf2fe] sm:justify-between  sm:pt-0 dark:bg-gray-900 ">
            <div class="xl:w-[50%] md:w-[100%] w-[100%] ">
                <a href="/" class="text-center">
                    <x-application-logo class="w-[100%] max-w-[500px] mx-auto text-gray-500 fill-current" />
                </a>
            </div>
            <div class="xl:w-[45%] md:w-[100%] w-[100%] h-screen bg-white flex-col p-[15px] flex items-center justify-center">

                <div class="px-6 py-4 mt-6 w-[100%] max-w-[650px] overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
