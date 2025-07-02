{{-- Sample Download Modal --}}
<div x-show="showSampleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div x-show="showSampleModal" x-transition
        class="w-full max-w-xl p-6 mx-2 bg-white shadow-2xl rounded-xl dark:bg-gray-900">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Download Sample File</h2>
            </div>
            <button @click="showSampleModal = false"
                class="text-2xl font-bold text-gray-500 hover:text-red-600">&times;</button>
        </div>

        {{-- Question Type Selection --}}
        <div class="mb-6">
            <p class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Choose Question Type:</p>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="(label, typeKey) in types" :key="typeKey">
                    <form :action="`{{ route('admin.questions.download') }}`" method="POST"
                        @submit="showSampleSuccess = true; setTimeout(() => showSampleSuccess = false, 3000)">
                        @csrf
                        <input type="hidden" name="type" :value="typeKey">
                        <button type="submit"
                            class="flex items-center w-full p-2 space-x-2 text-sm border rounded cursor-pointer dark:text-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span x-text="label" class="capitalize"></span>
                        </button>
                    </form>
                </template>
            </div>
        </div>

        {{-- Success Notification --}}
        <div x-show="showSampleSuccess" x-transition
            class="p-3 mt-4 text-sm text-green-800 bg-green-100 border border-green-300 rounded">
            ✅ Sample downloaded successfully.
        </div>
    </div>
</div>
