<aside class="flex-col hidden w-64 bg-white border-r dark:bg-gray-800 md:flex">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 border-b dark:border-gray-700">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="w-auto h-10 text-gray-800 fill-current dark:text-gray-200" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6"
        x-data="{
            open: {{ request()->routeIs('admin.levels.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.questions.*') ? 'true' : 'false' }}
        }"
    >
        <ul class="space-y-2 text-base font-medium text-gray-800 dark:text-gray-200">
            <!-- Dashboard -->
            <li class="w-full border-b border-gray-300 dark:border-gray-700">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="flex items-center w-full px-4 py-2 hover:bg-indigo-100 dark:hover:bg-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400">
                    Dashboard
                </x-nav-link>
            </li>

            <!-- Question Management (Parent) -->
            <li class="w-full border-b border-gray-300 dark:border-gray-700">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-2 text-left hover:bg-indigo-100 dark:hover:bg-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                    <span>Question Management</span>
                    <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform transform"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Submenu -->
                <div x-show="open" x-collapse>
                    <ul class="pl-6 mt-2 space-y-1">
                        <li>
                            <x-nav-link :href="route('admin.levels.index')" :active="request()->routeIs('admin.levels.*')"
                                class="flex items-center w-full px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                                Levels
                            </x-nav-link>
                        </li>
                        <li>
                            <x-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')"
                                class="flex items-center w-full px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                                Subjects
                            </x-nav-link>
                        </li>
                        <li>
                            <x-nav-link :href="route('admin.questions.index')" :active="request()->routeIs('admin.questions.*')"
                                class="flex items-center w-full px-3 py-2 hover:bg-indigo-50 dark:hover:bg-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400">
                                Questions
                            </x-nav-link>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</aside>
