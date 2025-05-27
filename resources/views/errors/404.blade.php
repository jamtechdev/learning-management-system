<x-guest-layout>
    <div class="flex items-center justify-center min-h-[80vh] text-gray-800">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-600">404</h1>
            <p class="mt-4 text-xl font-semibold">Oops! Page not found</p>
            <p class="mt-2 text-gray-500">The page you’re looking for doesn’t exist or was moved.</p>
            <a href="{{ url('/') }}" class="inline-block px-5 py-3 mt-6 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Go Home
            </a>
        </div>
    </div>
</x-guest-layout>
