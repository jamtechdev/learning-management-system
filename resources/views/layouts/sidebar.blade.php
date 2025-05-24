<aside
    class="sticky top-0 left-0 flex flex-col w-64 h-screen shadow-md bg-gradient-to-r from-blue-600 to-blue-800 dark:bg-gray-900 dark:border-gray-700"
    aria-label="Sidebar Navigation">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" aria-label="Dashboard" class="flex justify-center w-full">
            <x-application-logo class="w-auto h-10 text-indigo-600 dark:text-indigo-400" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 overflow-y-auto" x-data="{
        open: {{ request()->routeIs('admin.levels.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.questions.*') ? 'true' : 'false' }}
    }" aria-label="Main menu">
        <ul class="space-y-3 text-sm font-medium text-white dark:text-white">

            <!-- Dashboard -->
            <li>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                   📊
                    <span >Dashboard</span>
                </x-nav-link>
            </li>

            <!-- Question Management Parent -->
            <li>
                <button @click="open = !open" :aria-expanded="open.toString()" aria-controls="submenu-questions"
                    class="flex items-center w-full gap-2 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 hover:text-black dark:hover:text-black dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:bg-indigo-100 focus:text-black focus:ring-indigo-500">
                    📝
                    <span class="flex-1 font-semibold text-left">Question
                        Management</span>

                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-5 h-5 transition-transform duration-200 dark:text-indigo-400"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Submenu -->
                <div id="submenu-questions" x-show="open" x-collapse
                    class="mt-3 space-y-1 " style="display: none;">
                    <ul>
                        <li class="mb-[5px]">
                            <x-nav-link :href="route('admin.levels.index')" :active="request()->routeIs('admin.levels.*')"
                                class="flex items-center w-full gap-3 px-4 py-2 transition-colors duration-200 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="flex-shrink-0 w-5 h-5 " fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <span >Levels</span>
                            </x-nav-link>
                        </li>
                        <li class="mb-[5px]">
                            <x-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')"
                                class="flex items-center w-full gap-3 px-4 py-2 transition-colors duration-200 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="flex-shrink-0 w-5 h-5 " fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                                </svg>
                                <span >Subjects</span>
                            </x-nav-link>
                        </li>
                        <li class="mb-[5px]">
                            <x-nav-link :href="route('admin.questions.index')" :active="request()->routeIs('admin.questions.*')"
                                class="flex items-center w-full gap-3 px-4 py-2 transition-colors duration-200 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-700">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="flex-shrink-0 w-5 h-5 " fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                                <span >Questions</span>
                            </x-nav-link>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Add Parents -->
            <li>
                <x-nav-link href="{{ route('admin.parents.index') }}" :active="request()->routeIs('admin.parents.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                  👪
                    <span >Add Parents </span>
                </x-nav-link>
            </li>

        </ul>
    </nav>
</aside>
