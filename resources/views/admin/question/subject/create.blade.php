<x-app-layout>
    <div class="py-6 mx-auto max-w-7xl">
        <div class="p-6 bg-white rounded-lg shadow-xl">

            <h2 class="mb-6 text-2xl font-bold">Create Subject</h2>

            <form method="POST" action="{{ route('admin.subjects.store') }}" x-data="subjectForm()"
                x-init="init()" x-ref="form" @submit.prevent="submitForm()">
                @csrf

                <!-- Education Type -->
                <div class="mb-6">
                    <x-input-label for="education_type" :value="__('Education Type')" />
                    <select id="education_type" name="education_type" x-model="educationType"
                        @change="onEducationChange()"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">{{ __('-- Select Education Type --') }}</option>
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                    </select>
                    <x-input-error :messages="$errors->get('education_type')" class="mt-2" />

                    <!-- Usage Note for Education Type -->
                    <p class="mt-1 text-sm italic text-gray-600">
                        Please select the education type first to load the relevant levels.
                    </p>
                </div>

                <!-- Level Dropdown -->
                <template x-if="educationType">
                    <div class="mb-6">
                        <x-input-label for="level_id" :value="__('Level')" />
                        <select id="level_id" name="level_id" x-model="selectedLevel" @change="onLevelChange()"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">{{ __('-- Select Level --') }}</option>
                            <template x-for="level in filteredLevels" :key="level.id">
                                <option :value="level.id" x-text="level.name"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('level_id')" class="mt-2" />

                        <!-- Warning for Level Selection -->
                        <p class="mt-1 text-sm font-semibold text-red-600">
                            Warning: Changing the level after creation is not recommended.
                        </p>
                    </div>
                </template>

                <!-- Subject Name Input -->
                <template x-if="selectedLevel">
                    <div class="mb-6">
                        <x-input-label for="name" :value="__('Subject Name')" />
                        <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                            x-bind:disabled="!selectedLevel" required autocomplete="off" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        <!-- Restriction Note for Subject Name -->
                        <p class="mt-1 text-sm text-gray-700">
                            Restriction: Subject name should be unique within the selected level.
                        </p>
                    </div>
                </template>

                <!-- Submit Button -->
                <div x-show="selectedLevel" class="mt-4">
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                        Create Subject
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function subjectForm() {
            return {
                educationType: @json(old('education_type') ?? ''),
                selectedLevel: @json(old('level_id') ?? ''),
                allLevels: @json($levels),
                filteredLevels: [],

                init() {
                    if (this.educationType) {
                        this.filterLevels();
                    }
                },

                onEducationChange() {
                    this.selectedLevel = '';
                    this.filterLevels();
                },

                filterLevels() {
                    if (!this.educationType) {
                        this.filteredLevels = [];
                        return;
                    }
                    this.filteredLevels = this.allLevels.filter(level =>
                        level.education_type.toLowerCase() === this.educationType.toLowerCase()
                    );
                },

                submitForm() {
                    if (this.educationType && this.selectedLevel) {
                        this.$refs.form.submit();
                    }
                }
            }
        }
    </script>
</x-app-layout>
