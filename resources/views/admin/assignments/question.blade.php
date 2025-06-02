<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div
                class="flex flex-col p-6 space-y-6 bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">

                <!-- Header -->
                <div
                    class="flex flex-col items-center justify-between space-y-4 md:flex-row md:space-y-0">
                    <h2
                        class="text-3xl font-extrabold tracking-tight text-center text-gray-900 dark:text-white md:text-left">
                        Manage All Question Assessments
                    </h2>

                    <a href="{{ route('admin.assignments.questioncreate', $assessment_id) }}"
                        class="inline-block px-6 py-3 text-sm font-semibold text-white transition bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        + Add Assessment Question
                    </a>
                </div>

                <!-- Table Container -->
                <div
                    class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    Assessment Name
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    Question
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                            @forelse ($questions as $question)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{ $question->assessment->title }}
                                    </td>
                                    <td class="max-w-xl px-6 py-4 text-sm text-gray-700 whitespace-normal dark:text-gray-300">
                                        {!! $question->question->content ?? '<span class="italic text-gray-400">N/A</span>' !!}
                                    </td>
                                    <td class="px-6 py-4 space-x-2 text-sm font-medium whitespace-nowrap">
                                        <a href="{{ route('admin.assignments.questionedit', $question->id) }}"
                                            class="inline-block px-4 py-1 text-white transition bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.assignments.questiondelete', $question->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this assessment question?')"
                                                class="inline-block px-4 py-1 text-white transition bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-4 italic text-center text-gray-500 dark:text-gray-400">
                                        No questions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-6">
                    {{ $questions->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
