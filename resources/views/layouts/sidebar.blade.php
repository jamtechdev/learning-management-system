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
    <nav class="flex-1 px-4 py-6 overflow-y-auto" aria-label="Main menu">
        <ul class="space-y-3 text-sm font-medium text-white dark:text-white">

            <!-- Dashboard -->
            <li>
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    📊
                    <span>Dashboard</span>
                </x-nav-link>
            </li>

            <!-- Question Management -->
            <li>
                <x-nav-link :href="route('admin.levels.index')" :active="request()->routeIs('admin.levels.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-5 h-5 " fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span>Levels</span>
                </x-nav-link>
            </li>
            <li>
                <x-nav-link :href="route('admin.subjects.index')" :active="request()->routeIs('admin.subjects.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    📑
                    <span>Subjects</span>
                </x-nav-link>
            </li>
            <li>
                <x-nav-link :href="route('admin.topics.index')" :active="request()->routeIs('admin.topics.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    🗂️
                    <span>Topics</span>
                </x-nav-link>
            </li>
            <li>
                <x-nav-link :href="route('admin.questions.index')" :active="request()->routeIs('admin.questions.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-5 h-5 " fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                    <span>Questions</span>
                </x-nav-link>
            </li>

            <!-- Add Parents -->
            <li>
                <x-nav-link href="{{ route('admin.parents.index') }}" :active="request()->routeIs('admin.parents.*') || request()->routeIs('admin.student.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    👪
                    <span>Add Parents</span>
                </x-nav-link>
            </li>

            <!-- Subscription Plans -->
            <li>
                <x-nav-link href="{{ route('admin.subscriptions.index') }}" :active="request()->routeIs('admin.subscriptions.*') ||
                    request()->routeIs('admin.subscriptionplan.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    💳
                    <span>Subscription Plans</span>
                </x-nav-link>
            </li>

            <!-- Assignments -->
            <li>
                <x-nav-link href="{{ route('admin.assignments.index') }}" :active="request()->routeIs('admin.assignments.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    🗂️
                    <span>Assignments</span>
                </x-nav-link>
            </li>

             <!-- feedback -->
            <li>
                <x-nav-link href="{{ route('admin.feedback.index') }}" :active="request()->routeIs('admin.feedback.*')"
                    class="flex items-center w-full gap-3 px-4 py-3 transition-colors duration-200 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800">
                    💬
                    <span>Feedback</span>
                </x-nav-link>
            </li>

        </ul>
    </nav>
</aside>
