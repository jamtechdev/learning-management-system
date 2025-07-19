<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased text-gray-900 ">
        <div
            class="flex items-center min-h-screen bg-[#2a3162] sm:justify-between sm:pt-0 ">
            <div
                class="xl:w-[30%] lg:w-[100%] w-[100%] lg:block hidden min-h-screen">
                <!-- <a href="/" class="text-center">
                <x-application-logo class="w-[100%] max-w-[350px] mx-auto " />

                <h1 class="mb-2 text-4xl font-bold text-center text-white">{{ config('app.name', 'LearnEdge') }}</h1>
                <p class="font-bold text-white">Empowering education through smart learning tools, tests, and
                    analytics.</p>
            </a> -->
                <div
                    class="flex flex-col justify-between min-h-screen relative">
                    <img class="w-full flex-1 object-cover"
                        src="{{ asset('/images/logo/auth-side-img.jpg') }}" />
                    <div
                        class="p-[48px] bg-[linear-gradient(0deg,_black,_transparent)] h-100 absolute bottom-0 flex flex-col justify-end text-left">
                        <div class="flex justify-start bg-white p-2 w-fit h-fit mb-[20px] rounded-xl">
                            <img src="{{ asset('/images/logo/logo-1.png') }}"
                                alt
                                class="w-auto h-20 text-indigo-600 dark:text-indigo-400">
                        </div>
                        <h2
                            class="text-5xl font-bold text-white mb-4">
                            Your Gateway to Knowledge</h2>
                        <p class="text-gray-200 text-xl">Explore, Practice, and
                            Master
                            Skills Anytime, Anywhere</p>
                    </div>
                </div>
            </div>
            <div
                class="xl:w-[70%] lg:w-[100%] w-[100%] h-screen bg-[#fff7ee] flex-col p-[15px] flex items-center justify-center">

                <div
                    class="px-2 py-4 w-[100%] max-w-[480px] overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>

</html>
