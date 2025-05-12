<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-3 sm:px-6 lg:px-8">
            <!-- Total Parents Card -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Parents</h3>
                    <p class="mt-2 text-3xl font-bold text-teal-600 dark:text-teal-400">500</p>
                </div>
            </div>

            <!-- Total Children Card -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Children</h3>
                    <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">150</p>
                </div>
            </div>

            <!-- Total Questions Card -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Questions</h3>
                    <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">1,587</p>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
