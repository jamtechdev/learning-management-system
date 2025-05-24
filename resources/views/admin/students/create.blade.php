<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Add New Student
                    @if ($parent)
                        for {{ $parent->first_name }} {{ $parent->last_name }}
                    @endif
                </h2>
                @if ($errors->any())
                    <div class="p-4 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.student.store', $parent->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Pass parent_id as hidden --}}
                    @if (isset($parent))
                        <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                    @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- First Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Student Type -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Student Type</label>
                            <select name="student_type" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                                <option value="">Select type</option>
                                <option value="primary" {{ old('student_type') == 'primary' ? 'selected' : '' }}>Primary
                                </option>
                                <option value="secondary" {{ old('student_type') == 'secondary' ? 'selected' : '' }}>
                                    Secondary</option>
                            </select>
                        </div>

                        <!-- Avatar Upload -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Avatar (optional)</label>
                            <input type="file" name="avatar" accept="image/*"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>



                        <!-- Lock Code -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Lock Code</label>
                            <input type="text" name="lock_code" id="lock-code-field" readonly
                                class="w-full px-4 py-2 bg-gray-100 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Lock Code Toggle -->
                        <div class="flex items-center mt-6">
                            <input type="checkbox" id="lock-code-toggle" name="lock_code_enabled"
                                class="w-5 h-5 text-indigo-600 form-checkbox"
                                {{ old('lock_code_enabled', $student->lock_code_enabled ?? false) ? 'checked' : '' }}>
                            <label for="lock-code-toggle" class="ml-2 text-sm text-gray-700">Generate Lock Code</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                            class="px-6 py-2 text-white add-btn focus:outline-none">
                            Save Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const checkbox = document.getElementById('lock-code-toggle');
            const lockField = document.getElementById('lock-code-field');

            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    const code = Math.floor(100000 + Math.random() * 900000); // Generate 6-digit code
                    lockField.value = code;
                } else {
                    lockField.value = '';
                }
            });
        </script>
    @endpush
</x-app-layout>
