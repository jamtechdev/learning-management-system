<x-app-layout>
    <div class="p-6 mx-auto mt-10 bg-white shadow-md max-w-7xl rounded-xl">
        <h2 class="pb-2 mb-2 text-3xl font-bold text-gray-800 border-b">Edit Question Level</h2>

        <!-- User Guide Note -->
        <div class="p-4 mb-6 text-yellow-700 border border-yellow-300 rounded bg-yellow-50">
            <p><strong>How to edit a question level:</strong></p>
            <ol class="space-y-1 text-sm list-decimal list-inside">
                <li>The <em>Education Type</em> is fixed and cannot be changed.</li>
                <li>Update the <em>Level Name</em> in the input field below as needed.</li>
                <li>Click the <em>Update</em> button to save your changes.</li>
            </ol>
        </div>

        <form method="POST" action="{{ route('admin.levels.update', $level->id) }}">
            @csrf

            <!-- Education Type (Read-only) -->
            <div class="mb-6">
                <label for="education_type" class="block font-medium text-gray-700">Education Type</label>
                <input
                    type="text"
                    id="education_type"
                    name="education_type"
                    value="{{ $level->education_type }}"
                    disabled
                    class="block w-full mt-1 bg-gray-100 border-gray-300 rounded-md shadow-sm cursor-not-allowed"
                />
                <input type="hidden" name="education_type" value="{{ $level->education_type }}" />
                <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('education_type')" />
            </div>

            <!-- Level Name -->
            <div class="mb-6">
                <label for="name" class="block font-medium text-gray-700">Level Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $level->name) }}"
                    class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm"
                    required
                />
                <input
                    type="hidden"
                    id="name"
                    name="level_id"
                    value="{{ old('level_id', $level->id) }}"
                    required
                />

                <x-input-error class="mt-2 text-sm text-red-500" :messages="$errors->get('name')" />
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                >
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
