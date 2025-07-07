<div x-show="showExcelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div x-show="showExcelModal" x-transition
        class="w-full max-w-xl p-6 mx-2 bg-white shadow-2xl rounded-xl dark:bg-gray-900">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v16h16V4H4zm8 1v6m0 0l-3-3m3 3l3-3m-3 6v2m0 2h.01" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Import Questions</h2>
            </div>
            <button @click="showExcelModal = false"
                class="text-2xl font-bold text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Question Type --}}
            <div class="mb-6">
                <p class="flex items-center gap-2 mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a4 4 0 00-8 0v2a4 4 0 108 0zM3 17a4 4 0 004 4h10a4 4 0 004-4v-5H3v5z" />
                    </svg>
                    Choose Question Type:
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="(label, typeKey) in types" :key="typeKey">
                        <label
                            class="flex items-center p-2 space-x-2 text-sm border rounded cursor-pointer dark:text-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-800">
                            <input type="radio" name="type" :value="typeKey" x-model="selectedType" required>
                            <span x-text="label" class="capitalize"></span>
                        </label>
                    </template>
                </div>
            </div>

            {{-- File Upload --}}
            <div x-show="selectedType" x-transition class="mb-6">
                <label
                    class="flex items-center block gap-2 mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload File for <span class="font-bold" x-text="selectedType.replaceAll('_', ' ')"></span>:
                </label>
                <input type="file" name="file" required
                    class="block w-full px-4 py-2 text-sm border rounded-lg dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring focus:ring-blue-300" />
            </div>

            {{-- Hidden input for type change by rudra--}}
            <input type="hidden" name="type" :value="types[selectedType]">

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-all bg-blue-600 rounded hover:bg-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
