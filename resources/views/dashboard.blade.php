<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Cards -->
        <div class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-3 sm:px-6 lg:px-8">
            <!-- Total Users -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Users (Include Admin)</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $totalUsers }}</p>
                </div>
            </div>

            <!-- Total Parents -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Parents</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $parentCount }}</p>
                </div>
            </div>

            <!-- Total Children -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Children</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $childCount }}</p>
                </div>
            </div>

            <!-- Total Levels -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Question Levels</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $levelCount }}</p>
                </div>
            </div>

            <!-- Total Subjects -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Question Subjects</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $subjectCount }}</p>
                </div>
            </div>

            <!-- Total Questions -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div>
                    <h3 class="p-3 border-b py-2 text-xl font-bold text-black dark:text-white">Total Questions</h3>
                    <p class="p-3 mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalQuestions }}</p>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="max-w-full mx-auto mt-10 sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                <canvas id="dashboardChart" height="100"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('dashboardChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Users', 'Parents', 'Children', 'Levels', 'Subjects', 'Questions'],
                    datasets: [{
                        label: 'Dashboard Stats',
                        data: [
                            {{ $totalUsers }},
                            {{ $parentCount }},
                            {{ $childCount }},
                            {{ $levelCount }},
                            {{ $subjectCount }},
                            {{ $totalQuestions }}
                        ],
                        backgroundColor: [
                            'rgba(0, 128, 128, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(75, 192, 192, 0.7)'
                        ],
                        borderColor: [
                            'rgba(0, 128, 128, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Dashboard Statistics Overview',
                            color: '#111',
                            font: { size: 18 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
