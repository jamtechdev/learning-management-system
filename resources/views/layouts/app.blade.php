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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('/assets/css/global.css') }}">

    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#edf2fe] dark:bg-gray-900">
    <div class="flex min-h-screen">


        <!-- Sidebar (Static) -->
        <div class="hidden w-64 bg-white border-r dark:bg-gray-800 md:block">
            @include('layouts.sidebar')
        </div>
        <!-- Main Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto">
            <!-- Top Navigation Bar -->
            <div class="sticky top-0 bg-white shadow dark:bg-gray-800">
                @include('layouts.navigation')
            </div>
            <!-- Page Content -->
            <main class="flex-1 p-4">
                @include('layouts._toasts') <!-- Include toastr notifications -->
                @if (session('message'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                        class="relative p-4 mb-4 text-green-700 bg-green-100 rounded">
                        <p>{{ session('message') }}</p>
                        <button @click="show = false"
                            class="absolute text-green-700 top-2 right-2 hover:text-green-900">
                            &times;
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                        class="relative p-4 mb-4 text-red-700 bg-red-100 rounded">
                        <p>{{ session('error') }}</p>
                        <button @click="show = false" class="absolute text-red-700 top-2 right-2 hover:text-red-900">
                            &times;
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>
            <!-- Footer -->
            <footer class="bg-white border-t dark:bg-gray-800">
                @include('layouts.footer')
            </footer>
        </div>
    </div>
</body>
@stack('scripts')

</html>
