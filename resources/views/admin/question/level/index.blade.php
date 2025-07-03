<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">

                <!-- Header -->
                <div class="flex flex-col items-center justify-between mb-8 md:flex-row">
                    <h2
                        class="text-3xl font-extrabold tracking-tight text-center text-gray-900 dark:text-white md:text-left">
                        Manage All Question Levels
                    </h2>
                    <a href="{{ route('admin.levels.create') }}"
                        class="inline-block px-6 py-3 mt-4 text-sm font-semibold text-white transition add-btn">
                        + Add Level
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    #
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Level ID
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">
                                    Level Name
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">
                                    Education Type
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-900 dark:divide-gray-800">
                            @forelse ($levels as $index => $level)
                                <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-left text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ ($levels->currentPage() - 1) * $levels->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                        {{ $level->id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                        {{ $level->name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm text-center text-gray-700 capitalize dark:text-gray-300">
                                        {{ $level->education_type }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                        <a href="{{ route('admin.levels.edit', $level->id) }}"
                                            class="inline-block px-3 py-1 mr-2 text-sm transition text-white-600 add-btn">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.levels.destroy', $level->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this level?')"
                                                class="inline-block px-3 py-1 text-sm text-white transition bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No levels found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $levels->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
