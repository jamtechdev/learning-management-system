<x-app-layout>
    <div class="max-w-4xl p-6 mx-auto mt-10 bg-white shadow-md rounded-xl">
        <h2 class="pb-2 mb-6 text-3xl font-bold text-gray-800 border-b">Create Question Level</h2>

        <form method="POST" action="{{ route('admin.levels.store') }}" x-data="levelForm()" x-init="init()">
            @csrf

            <!-- Education Type -->
            <div class="mb-6">
                <label for="education_type" class="block font-medium text-gray-700">Education Type</label>
                <select id="education_type" name="education_type" x-model="educationType" @change="filterLevels()"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('education_type') border-red-500 @enderror">
                    <option value="">-- Select --</option>
                    <option value="Primary">Primary</option>
                    <option value="Secondary">Secondary</option>
                </select>
                @error('education_type')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input + Add Level -->
            <template x-if="educationType">
                <div class="mb-6">
                    <label class="block mb-2 font-medium text-gray-700">Add Level</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="newLevel" @keyup.enter.prevent="addLevel()"
                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter level name">
                        <button type="button" @click="addLevel()"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                            Add
                        </button>
                    </div>

                    <!-- Selected Levels Preview -->
                    <template x-if="selectedLevels.length">
                        <div class="grid grid-cols-2 gap-2 mt-4 sm:grid-cols-3">
                            <template x-for="(level, index) in selectedLevels" :key="level">
                                <div
                                    class="flex items-center justify-between px-3 py-2 text-sm bg-gray-100 border rounded">
                                    <span x-text="level"></span>
                                    <button type="button" @click="removeLevel(index)"
                                        class="text-red-500 hover:text-red-700">✕</button>
                                    <input type="hidden" name="name[]" :value="level">
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="errorMessage">
                        <p class="mt-2 text-sm text-red-500" x-text="errorMessage"></p>
                    </template>

                    @error('name')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </template>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                    class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700 disabled:opacity-50"
                    :disabled="!educationType || selectedLevels.length === 0">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <script>
        function levelForm() {
            return {
                educationType: @json(old('education_type') ?? ''),
                selectedLevels: @json(old('name') ?? []),
                newLevel: '',
                errorMessage: '',
                usedLevels: @json(collect($existingLevels)->mapWithKeys(function ($collection, $type) {
                        return [$type => $collection->pluck('name')->map(fn($n) => strtolower($n))->unique()->values()];
                    })),

                init() {
                    this.filterLevels();
                },

                filterLevels() {
                    const eduKey = this.educationType;
                    const used = this.usedLevels[eduKey] || [];
                    this.selectedLevels = this.selectedLevels.filter(level => !used.includes(level.toLowerCase()));
                },

                isUsed(level) {
                    return this.usedLevels[this.educationType]?.includes(level.toLowerCase());
                },

                addLevel() {
                    const level = this.newLevel.trim();
                    const lowerLevel = level.toLowerCase();
                    this.errorMessage = '';

                    if (!this.educationType) {
                        this.errorMessage = 'Please select an education type first.';
                        return;
                    }

                    if (!level) {
                        this.errorMessage = 'Level name cannot be empty.';
                        return;
                    }

                    if (this.selectedLevels.map(l => l.toLowerCase()).includes(lowerLevel)) {
                        this.errorMessage = 'This level is already added.';
                        return;
                    }

                    if (this.isUsed(level)) {
                        this.errorMessage = 'This level already exists for this education type.';
                        return;
                    }

                    this.selectedLevels.push(level);
                    this.newLevel = '';
                },

                removeLevel(index) {
                    this.selectedLevels.splice(index, 1);
                },
            }
        }
    </script>
</x-app-layout>
