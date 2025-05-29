<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Edit Student
                    @if ($parent)
                        for {{ $parent->first_name }} {{ $parent->last_name }}
                    @endif
                </h2>

                <form action="{{ route('admin.student.update', $student->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if (isset($parent))
                        <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                    @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- First Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name"
                                value="{{ old('first_name', $student->first_name) }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('first_name') ? 'border-red-500' : '' }}">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('last_name') ? 'border-red-500' : '' }}">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $student->email) }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('email') ? 'border-red-500' : '' }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('phone') ? 'border-red-500' : '' }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Student Type -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Student Type</label>
                            <select name="student_type" id="student_type"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('student_type') ? 'border-red-500' : '' }}">
                                <option value="">Select type</option>
                                <option value="primary"
                                    {{ old('student_type', $student->student_type) == 'primary' ? 'selected' : '' }}>
                                    Primary</option>
                                <option value="secondary"
                                    {{ old('student_type', $student->student_type) == 'secondary' ? 'selected' : '' }}>
                                    Secondary</option>
                            </select>
                            @error('student_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Student Level -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Student Level</label>
                            <select name="student_level" id="student_level"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('student_level') ? 'border-red-500' : '' }}">
                                <option value="">Select level</option>
                                @if (isset($levels[old('student_type', $student->student_type)]))
                                    @foreach ($levels[old('student_type', $student->student_type)] as $level)
                                        <option value="{{ $level['id'] }}"
                                            {{ old('student_level', $student->student_level) == $level['id'] ? 'selected' : '' }}>
                                            {{ $level['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('student_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Avatar (optional)</label>
                            <input type="file" name="avatar" accept="image/*"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('avatar') ? 'border-red-500' : '' }}">
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Password (Leave blank to keep
                                current)</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('password') ? 'border-red-500' : '' }}">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Lock Code -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Lock Code</label>
                            <input type="text" name="lock_code" id="lock-code-field" readonly
                                value="{{ old('lock_code', $student->lock_code) }}"
                                class="w-full px-4 py-2 bg-gray-100 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                            <input type="checkbox" id="lock-code-toggle" name="lock_code_enabled"
                                class="w-5 h-5 text-indigo-600 form-checkbox"
                                {{ old('lock_code_enabled', $student->lock_code_enabled) ? 'checked' : '' }}>
                            <label for="lock-code-toggle" class="ml-2 text-sm text-gray-700">Generate Lock Code</label>
                            @error('lock_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Button Container -->
                    <div class="mt-6 flex justify-end space-x-2 items-end">
                        <button type="submit"
                            class="px-6 py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow-md focus:outline-none">
                            Update Student
                        </button>
                        <a href="{{ route('admin.student.index', $parent->id) }}"
                            class="px-6 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md shadow-md focus:outline-none">
                            Back
                        </a>
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
                    const code = Math.floor(100000 + Math.random() * 900000);
                    lockField.value = code;
                } else {
                    lockField.value = '';
                }
            });

            const levels = @json($levels);
            document.getElementById('student_type').addEventListener('change', function() {
                const selectedType = this.value;
                const studentLevelSelect = document.getElementById('student_level');
                studentLevelSelect.innerHTML = '<option value="">Select level</option>';
                levels[selectedType]?.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level.id;
                    option.textContent = level.name;
                    studentLevelSelect.appendChild(option);
                });
            });
        </script>
    @endpush
</x-app-layout>
