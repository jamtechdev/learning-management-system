<x-app-layout>
    <div class="max-w-4xl p-6 mx-auto mt-10 bg-white shadow-md rounded-xl">
        <h2 class="pb-2 mb-2 text-3xl font-bold text-gray-800 border-b">Create Question Level</h2>

        <!-- User Guide Note -->
        <div class="p-4 mb-6 text-blue-700 border border-blue-200 rounded bg-blue-50">
            <p><strong>How to use this form:</strong></p>
            <ol class="space-y-1 text-sm list-decimal list-inside">
                <li>Select the <em>Education Type</em> from the dropdown (Primary or Secondary).</li>
                <li>Once selected, enter a new level name in the input box below.</li>
                <li>Click the <em>Add</em> button or press <em>Enter</em> to add the level.</li>
                <li>Added levels appear below where you can remove any by clicking the red ✕ button.</li>
                <li>When done, click <em>Submit</em> to save all added levels for the selected education type.</li>
            </ol>
        </div>

        <form method="POST" action="{{ route('admin.levels.store') }}" x-data="levelForm()">
            @csrf

            <!-- Education Type -->
            <div class="mb-6">
                <label for="education_type" class="block font-medium text-gray-700">Education Type</label>
                <select id="education_type" name="education_type" x-model="educationType"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('education_type') border-red-500 @enderror">
                    <option value="">-- Select --</option>
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
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
                        <input type="text" x-model="newLevel" @input="checkInputLength" @keyup.enter.prevent="addLevel()" pattern="[0-9]*"
                            inputmode="numeric"
                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter level name">
                        <button type="button" @click="addLevel()" :disabled="isAddDisabled"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50">
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
                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50"
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
                levelsByType: {
                    primary: [],
                    secondary: []
                },
                newLevel: '',
                errorMessage: '',
                isAddDisabled: true,
                errorTimeout: null,

                checkInputLength() {
                    this.newLevel = this.newLevel.replace(/\D/g, '');
                    if (this.newLevel.length > 2) {
                        this.errorMessage = 'Level number cannot exceed 2 digits.';
                        this.isAddDisabled = true;
                        this.hideErrorAfterDelay();
                    } else {
                        this.errorMessage = '';
                        this.isAddDisabled = this.newLevel.length === 0;
                    }
                },

                addLevel() {
                    var level = this.newLevel.trim();

                    if (!this.educationType) {
                        this.errorMessage = 'Please select an education type first.';
                        this.hideErrorAfterDelay();
                        return;
                    }

                    var levels = this.levelsByType[this.educationType];

                    if (this.educationType === 'primary' && levels.length >= 6) {
                        this.errorMessage = 'Maximum 6 levels allowed for Primary.';
                        this.hideErrorAfterDelay();
                        return;
                    }

                    if (this.educationType === 'secondary' && levels.length >= 6) {
                        this.errorMessage = 'Maximum 6 levels allowed for Secondary.';
                        this.hideErrorAfterDelay();
                        return;
                    }

                if (!level) {
                    this.errorMessage = 'Level cannot be empty.';
                    this.hideErrorAfterDelay();
                    return;
                }

                if (!/^\d+$/.test(level)) {
                    this.errorMessage = 'Only numeric values are allowed.';
                    this.hideErrorAfterDelay();
                    return;
                }

                if ((this.educationType === 'secondary' && parseInt(level) > 12) || (this.educationType === 'primary' && parseInt(level) > 6)) {
                    this.errorMessage = 'Level number exceeds the maximum allowed for the selected education type.';
                    this.hideErrorAfterDelay();
                    return;
                }

                if ((this.educationType === 'primary' && parseInt(level) < 1) || (this.educationType === 'secondary' && parseInt(level) < 7)) {
                    this.errorMessage = 'Level number is below the minimum allowed for the selected education type.';
                    this.hideErrorAfterDelay();
                    return;
                }

                if (level.length > 2) {
                    this.errorMessage = 'Level number cannot exceed 2 digits.';
                    this.hideErrorAfterDelay();
                    return;
                }

                if (levels.includes(level)) {
                    this.errorMessage = 'This level is already added.';
                    this.hideErrorAfterDelay();
                    return;
                }

                    levels.push(level);
                    this.newLevel = '';
                    this.isAddDisabled = true;
                },


                removeLevel(index) {
                    this.levelsByType[this.educationType].splice(index, 1);
                },

                hideErrorAfterDelay() {
                    if (this.errorTimeout) clearTimeout(this.errorTimeout);
                    this.errorTimeout = setTimeout(() => {
                        this.errorMessage = '';
                    }, 3000);
                },

                get selectedLevels() {
                    return this.levelsByType[this.educationType] || [];
                }
            }
        }
    </script>
</x-app-layout>
