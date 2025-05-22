<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <!-- Header -->
                <div class="flex flex-col items-center justify-between mb-8 md:flex-row">
                    <h2
                        class="text-3xl font-extrabold tracking-tight text-center text-gray-900 dark:text-white md:text-left">
                        Manage All Parents
                    </h2>
                    <a href="{{ route('admin.parents.create') }}"
                        class="inline-block px-6 py-3 mt-4 text-sm font-semibold text-white transition bg-green-600 rounded-lg shadow md:mt-0 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        + Add Parent
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-300">
                                    #</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Avatar</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Phone</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Add Student</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-300">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-900 dark:divide-gray-800">
                            @forelse ($parents as $index => $parent)
                                <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-800">
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-left text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <img src="{{ asset('/storage/' . $parent->avatar) ?? asset('/images/logo/default-avatar.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('/images/logo/default-avatar.png') }}';"
                                            alt="{{ $parent?->first_name ?? 'User' }}"
                                            class="w-8 h-8 border border-gray-300 rounded-full cursor-pointer dark:border-gray-700">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                        {{ $parent->first_name }} {{ $parent->last_name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                        {{ $parent->email }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700 dark:text-gray-300">
                                        {{ $parent->phone }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-center">
                                        <a href="{{ route('admin.student.create') }}?parent_id={{ $parent->id }}"
                                            class="inline-block px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1">
                                            + Student
                                        </a>
                                    </td>

                                    <td
                                        class="flex justify-center gap-4 px-6 py-4 text-sm font-medium text-center whitespace-nowrap">

                                        <!-- Edit button -->
                                        <a href="{{ route('admin.parents.edit', $parent->id) }}"
                                            class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit fa-lg"></i>
                                        </a>

                                        <!-- View Students button -->
                                        <a href="{{ route('admin.parents.students', $parent->id) }}"
                                            class="text-indigo-600 hover:text-indigo-800" title="View Students">
                                            <i class="fas fa-users fa-lg"></i>
                                        </a>

                                        <!-- Delete button -->
                                        <form action="{{ route('admin.parents.destroy', $parent->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this parent?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800"
                                                title="Delete">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </button>
                                        </form>
                                    </td>


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No parents found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
