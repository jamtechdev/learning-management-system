<x-app-layout>
    <div class="min-h-screen py-10 bg-gray-50">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="p-8 bg-white shadow-lg rounded-2xl">
                <h2 class="pb-3 mb-6 text-3xl font-semibold text-gray-800 border-b">
                    📘 Add New Assessment
                </h2>

                <form action="{{ route('admin.assignments.store') }}" method="POST" class="space-y-6">
                    @csrf

                    @if (isset($user))
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                    @endif

                    <div class="grid grid-cols-1 gap- md:grid-cols-1">
                        <!-- Select Student -->
                        <div>
                            <label for="student_id" class="block mb-1 text-sm font-medium text-gray-700">
                                Select Student <span class="text-red-500">*</span>
                            </label>
                            <select name="student_id" id="student_id" required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="">— Select a Student —</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->first_name }} {{ $student->last_name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="title" class="block mb-1 text-sm font-medium text-gray-700">
                                Assessment Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                placeholder="e.g. Math Weekly Quiz">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
