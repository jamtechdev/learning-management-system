<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Edit Student - {{ $student->first_name }} {{ $student->last_name }}
                </h2>

                <form action="{{ route('admin.student.update', $student->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- First Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}"
                                required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                                required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $student->email) }}" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Lock Code -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Lock Code</label>
                            <input type="text" name="lock_code" value="{{ old('lock_code', $student->lock_code) }}"
                                readonly
                                class="w-full px-4 py-2 bg-gray-100 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>


                        <!-- Avatar Upload -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Avatar</label>
                            @if ($student->avatar)
                                <img src="{{ asset('storage/' . $student->avatar) }}"
                                    class="w-16 h-16 mb-2 rounded-full shadow" alt="Avatar">
                            @endif
                            <input type="file" name="avatar" accept="image/*"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <!-- Password (Optional) -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">New Password (leave blank to
                                keep current)</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-4 flex space-x-4">
                        <button type="submit"
                            class="px-6 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 focus:outline-none">
                            Update Student
                        </button>
                        <a href="{{ $student->parent_id ? route('admin.parents.students', $student->parent_id) : route('admin.student.index') }}"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 focus:outline-none inline-flex items-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
