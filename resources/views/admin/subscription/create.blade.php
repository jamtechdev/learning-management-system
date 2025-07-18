<x-app-layout>
    <div class="min-h-screen py-8 bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="container px-4 mx-auto">
            <div class="row">
                <div class="mx-auto col-md-6">
                    <!-- Form Card -->
                    <div class="overflow-hidden bg-white border border-gray-100 rounded-lg shadow-lg">
                        <div class="p-6">
                            <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="space-y-6">
                                @csrf

                                <!-- Plan Name -->
                                <div class="w-full">
                                    <label for="name" class="block mb-2 text-sm font-semibold text-gray-700">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Plan Name
                                        </span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                        class="w-full px-3 py-2 text-sm transition-all duration-200 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent hover:border-gray-300"
                                        placeholder="Enter plan name (e.g., Premium, Basic, Pro)"
                                        required>
                                    @error('name')
                                        <p class="flex items-center mt-1 text-xs text-red-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Price -->
                                <div class="w-full">
                                    <label for="price" class="block mb-2 text-sm font-semibold text-gray-700">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            Price
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute text-sm font-semibold text-gray-500 transform -translate-y-1/2 left-3 top-1/2">$</span>
                                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}"
                                            class="w-full py-2 pl-6 pr-3 text-sm transition-all duration-200 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent hover:border-gray-300"
                                            placeholder="0.00"
                                            required>
                                    </div>
                                    @error('price')
                                        <p class="flex items-center mt-1 text-xs text-red-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="w-full">
                                    <label for="duration" class="block mb-2 text-sm font-semibold text-gray-700">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Duration
                                        </span>
                                    </label>
                                    <select name="duration_days" id="duration"
                                        class="w-full px-3 py-2 text-sm transition-all duration-200 bg-white border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent hover:border-gray-300"
                                        required>
                                        <option value="" disabled selected>Choose duration</option>
                                        <option value="7" {{ old('duration_days') == '7' ? 'selected' : '' }}>
                                            🗓️ 1 Week (7 days)
                                        </option>
                                        <option value="30" {{ old('duration_days') == '30' ? 'selected' : '' }}>
                                            📅 1 Month (30 days)
                                        </option>
                                        <option value="365" {{ old('duration_days') == '365' ? 'selected' : '' }}>
                                            🎯 1 Year (365 days)
                                        </option>
                                    </select>
                                    @error('duration_days')
                                        <p class="flex items-center mt-1 text-xs text-red-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="w-full">
                                    <label for="description" class="block mb-2 text-sm font-semibold text-gray-700">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Description
                                            <span class="ml-2 text-xs text-gray-400">(Optional)</span>
                                        </span>
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full px-3 py-2 text-sm transition-all duration-200 border-2 border-gray-200 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-300"
                                        placeholder="Describe what this plan includes...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="flex items-center mt-1 text-xs text-red-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3 pt-4">
                                    <a href="{{ route('admin.subscriptions.index') }}"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-center text-gray-700 transition-all duration-200 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 hover:border-gray-300">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                            </svg>
                                            Cancel
                                        </span>
                                    </a>
                                    <button type="submit"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-white transition-all duration-200 rounded-lg shadow-md bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 hover:shadow-lg">
                                        <span class="flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create Plan
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
