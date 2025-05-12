<aside class="flex-col hidden w-64 bg-white border-r dark:bg-gray-800 md:flex">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 border-b dark:border-gray-700">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="w-auto h-10 text-gray-800 fill-current dark:text-gray-200" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6">
        <ul class="space-y-2">
            <li class="w-full border-b border-gray-300 dark:border-gray-700">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="block w-full px-4 py-2 text-lg font-semibold text-gray-800 dark:text-gray-200 hover:bg-indigo-100 dark:hover:bg-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400">
                    Dashboard
                </x-nav-link>
            </li>


            {{-- <li class="w-full border-b border-gray-300 dark:border-gray-700">
                <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')"
                            class="block w-full px-4 py-2 text-lg font-semibold text-gray-800 dark:text-gray-200 hover:bg-indigo-100 dark:hover:bg-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400">
                    Settings
                </x-nav-link>
            </li> --}}
        </ul>
    </nav>
</aside>
