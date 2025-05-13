<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="flex min-h-screen">

        <!-- Sidebar (Static) -->
        <div class="hidden w-64 bg-white border-r dark:bg-gray-800 md:block">
            @include('layouts.sidebar')
        </div>
        <!-- Main Content Area -->
        <div class="flex flex-col flex-1">
            <!-- Top Navigation Bar -->
            <div class="bg-white shadow dark:bg-gray-800">
                @include('layouts.navigation')
            </div>
            <!-- Page Content -->
            <main class="flex-1 p-4">
                {{ $slot }}
            </main>
            <!-- Footer -->
            <footer class="bg-white border-t dark:bg-gray-800">
                @include('layouts.footer')
            </footer>
        </div>
    </div>
</body>

</html>
