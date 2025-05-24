<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <!-- Header -->
                <div class="flex flex-col items-center justify-between mb-8 md:flex-row">
                    <h2 class="mb-6 text-3xl font-extrabold tracking-tight text-center md:text-left">
                        @isset($parent)
                            Students of {{ $parent->first_name }} {{ $parent->last_name }}
                        @else
                            Manage All Students
                        @endisset
                    </h2>

                    <a href="{{ route('admin.student.create', $parent->id) }}"
                        class="inline-block px-6 py-3 mt-4 text-sm font-semibold text-white add-btn">
                        + Add Student
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                    {{-- @dd($students); --}}
                    <table class="min-w-full divide-y divide-gray-200 table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Avatar
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Phone</th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Lock Code
                                </th>
                                <th class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($students as $index => $student)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 text-sm font-medium text-left whitespace-nowrap">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <img src="{{ asset('/storage/' . $student->avatar) ?? asset('/images/logo/default-avatar.png') }}"
                                            onerror="this.onerror=null;this.src='{{ asset('/images/logo/default-avatar.png') }}';"
                                            alt="{{ $student?->first_name ?? 'User' }}"
                                            class="w-8 h-8 border border-gray-300 rounded-full cursor-pointer dark:border-gray-700"
                                            @click="showImage = true">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700">
                                        {{ $student->email }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700">
                                        {{ $student->phone }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center text-gray-700">
                                        {{ $student->lock_code ?? 'N/A' }}

                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                        <a href="{{ route('admin.student.edit', $student->id) }}"
                                            class="inline-block px-3 py-1 mr-2 text-sm text-white-600 add-btn">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.student.destroy', $student->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this student?')"
                                                class="inline-block px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $students->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
