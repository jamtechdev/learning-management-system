<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Manage All Subjects
                    </h2>
                    <a href="{{ route('admin.subjects.create') }}"
                        class="inline-block px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">
                        + Add Subject
                    </a>
                </div>

                <div class="overflow-x-auto rounded shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="w-1/5 px-4 py-3 text-sm font-medium text-center text-gray-700 uppercase dark:text-gray-300">
                                    #
                                </th>
                                <th
                                    class="w-1/5 px-4 py-3 text-sm font-medium text-center text-gray-700 uppercase dark:text-gray-300">
                                    Level
                                </th>
                                <th
                                    class="w-1/5 px-4 py-3 text-sm font-medium text-center text-gray-700 uppercase dark:text-gray-300">
                                    Type
                                </th>
                                <th
                                    class="w-1/5 px-4 py-3 text-sm font-medium text-center text-gray-700 uppercase dark:text-gray-300">
                                    Subject Name
                                </th>
                                <th
                                    class="w-1/5 px-4 py-3 text-sm font-medium text-center text-gray-700 uppercase dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 dark:divide-gray-800 dark:bg-gray-900">
                            @foreach ($subjects as $index => $subject)
                                <tr>
                                    <td class="w-1/5 px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="w-1/5 px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        {{ $subject->level->name ?? '—' }}
                                    </td>
                                    <td class="w-1/5 px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        {{ $subject->education_type ?? '—' }}
                                    </td>
                                    <td class="w-1/5 px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        {{ $subject->name }}
                                    </td>
                                    <td class="w-1/5 px-4 py-3 text-sm text-center text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('admin.subjects.edit', $subject->id) }}"
                                            class="inline-block px-3 py-1 mr-2 text-sm text-blue-600 transition bg-blue-100 rounded hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                                            Edit
                                        </a>
                                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-block px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700"
                                                    onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($subjects->isEmpty())
                                <tr>
                                    <td colspan="5"
                                        class="px-4 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No subjects found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
