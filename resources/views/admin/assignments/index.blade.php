<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">

                <!-- Header -->
                <div class="flex flex-col items-center justify-between mb-8 md:flex-row">
                    <h2
                        class="text-3xl font-extrabold tracking-tight text-center text-gray-900 dark:text-white md:text-left">
                        Manage All Question Assessments
                    </h2>
                    <a href="{{ route('admin.assignments.create') }}"
                        class="inline-block px-6 py-3 mt-4 text-sm font-semibold text-white transition add-btn">
                        + Add Assessments
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    ID</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Title</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    User</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Weekly?</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status</th>
                                <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Created At</th>

                                     <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions</th>
                                     <th
                                    class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Questions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($assessments as $assessment)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $assessment->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $assessment->title }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $assessment->user->first_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $assessment->is_weekly ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 capitalize">
                                        {{ str_replace('_', ' ', $assessment->status) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $assessment->created_at->format('d M Y') }}</td>

                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        <a href="{{route('admin.assignments.edit', $assessment->id)}}"
                                            class="inline-block px-3 py-1 mr-2 text-sm text-white-600 add-btn">
                                            Edit
                                        </a>
                                         <form action="{{ route('admin.assignments.delete', $assessment->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this assessment?')"
                                                class="inline-block px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{route('admin.assignments.question', $assessment->id)}}"
                                            class="inline-block px-3 py-1 mr-2 text-sm text-white-600  bg-green-400">
                                            Questions
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-sm text-center text-gray-500">No
                                        assessments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $assessments->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
