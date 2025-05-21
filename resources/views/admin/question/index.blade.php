<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        {{ __('Manage Questions') }}
                    </h2>
                    <a href="{{ route('admin.questions.create') }}"
                        class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Add Question
                    </a>
                </div>

                <!-- Question Management Table -->
                <div class="overflow-x-auto rounded-lg shadow-md bg-gray-50 dark:bg-gray-900">
                    <table class="min-w-full text-left table-auto">
                        <thead>
                            <tr class="text-gray-700 bg-gray-200 border-b dark:bg-gray-700 dark:text-gray-300">
                                <th class="px-6 py-4 text-sm font-semibold">#</th>
                                <th class="px-6 py-4 text-sm font-semibold">Content</th>
                                <th class="px-6 py-4 text-sm font-semibold">Type</th>
                                <th class="px-6 py-4 text-sm font-semibold">Explanation</th>
                                <th class="px-6 py-4 text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-300">
                            @foreach ($questions as $question)
                                <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 text-sm font-medium">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $question->content }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $question->type }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $question->explanation }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex space-x-2">
                                            <button
                                                class="px-4 py-2 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                                Edit
                                            </button>
                                            <button
                                                class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
