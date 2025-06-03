<x-app-layout>
    <div class="max-w-full py-6 mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Edit Parent</h2>

            <form action="{{ route('admin.parents.update', $parent->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                @method('PUT')
                     <div class="mb-3">
                        <label for="avatar"
                            class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Avatar</label>
                            @if ($parent->avatar)
                            <div class="mt-3 relative w-[100px] h-[100px] rounded-[100px] border-[1.5px] border-blue-700">
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    class="absolute top-0 bottom-0 left-0 z-10 w-full text-sm text-gray-500 opacity-0 end-0 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <img src="{{ asset('storage/' . $parent->avatar) }}"
                                    onerror="this.onerror=null;this.src='{{ asset('/images/logo/default-avatar.png') }}';"
                                    class="object-cover rounded-full shadow w-[100%] h-[100%]" alt="Current Avatar">
                                    <!-- Outlined Camera SVG -->
                                <svg class="absolute bottom-0 right-0 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h2l2-3h10l2 3h2a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2zm9 3a4 4 0 100 8 4 4 0 000-8z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <input type="text" name="first_name" id="first_name"
                            value="{{ old('first_name', $parent->first_name) }}" required
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <input type="text" name="last_name" id="last_name"
                            value="{{ old('last_name', $parent->last_name) }}" required
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $parent->email) }}"
                            required
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $parent->phone) }}"
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Password (leave
                            blank to keep current)</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>

                <div>
                    <label for="address"
                        class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-3 py-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address', $parent->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex mt-4 space-x-4">
                    <button type="submit"
                        class="w-full px-6 py-3 text-white add-btn">
                        Update Parent
                    </button>
                    <a href="{{ route('admin.parents.index') }}"
                        class="flex items-center justify-center px-6 py-3 text-gray-700 bg-gray-300 rounded-lg shadow hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
