<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div class="p-6 bg-white rounded-lg shadow">
                <div class="flex flex-col mb-8 space-y-4 md:flex-row md:items-center md:justify-between md:space-y-0">
                    <h1 class="text-3xl font-extrabold text-gray-900">
                        Subscription Plans
                    </h1>
                    <a href="{{ route('admin.subscriptions.create') }}"
                        class="inline-flex items-center px-6 py-2 text-sm font-semibold text-white transition bg-indigo-600 rounded-md hover:bg-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Subscription Plan
                    </a>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Duration (Days)
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Description
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Subjects
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($plans as $plan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $plan->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">${{ number_format($plan->price, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $plan->duration_days }}</td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-600 truncate" title="{{ $plan->description }}">
                                        {{ $plan->description ?: '—' }}
                                    </td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-700 whitespace-normal">
                                        @if ($plan->subjects->count())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($plan->subjects as $subject)
                                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold text-white bg-indigo-600 rounded">
                                                        {{ $subject->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs italic text-gray-400">No subjects assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 space-x-2 text-sm font-medium text-center whitespace-nowrap">
                                        <a href="{{ route('admin.subscriptions.edit', $plan->id) }}"
                                            class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-blue-600 rounded-md hover:bg-blue-700">
                                            Edit
                                        </a>

                                        <a href="{{ route('admin.subscriptions.assignSubjects', $plan->id) }}"
                                            class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-green-600 rounded-md hover:bg-green-700">
                                            Assign Subjects
                                        </a>

                                        <form action="{{ route('admin.subscriptions.destroy', $plan->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this subscription plan?')"
                                                class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-red-600 rounded-md hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 italic text-center text-gray-500">
                                        No subscription plans found.
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
