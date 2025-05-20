<x-app-layout>
    <div class="py-6 mx-auto max-w-7xl">
        <div class="p-6 bg-white rounded-lg shadow-xl">
            <h2 class="mb-6 text-2xl font-bold">Edit Subject</h2>

            <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}"
                x-data="subjectForm()" x-init="init()" x-ref="form" @submit.prevent="submitForm()">
                @csrf
                @method('PUT')

                {{-- Education Type --}}
                <div class="mb-6">
                    <x-input-label for="education_type" :value="__('Education Type')" />
                    <select id="education_type" name="education_type"
                        x-model="educationType" @change="onEducationChange()"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required disabled>
                        <option value="">{{ __('-- Select Education Type --') }}</option>
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                    </select>
                    <x-input-error :messages="$errors->get('education_type')" class="mt-2" />

                    {{-- Note: Education Type cannot be changed after creation --}}
                    <p class="mt-1 text-sm italic font-semibold text-red-600">
                        Education Type cannot be changed once the subject is created.
                    </p>
                </div>

                {{-- Level Dropdown --}}
                <template x-if="educationType">
                    <div class="mb-6">
                        <x-input-label for="level_id" :value="__('Level')" />
                        <select id="level_id" name="level_id" x-model="selectedLevel"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">{{ __('-- Select Level --') }}</option>
                            <template x-for="level in filteredLevels" :key="level.id">
                                <option :value="level.id.toString()" x-text="level.name"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('level_id')" class="mt-2" />

                        {{-- Warning about Level change --}}
                        <p class="mt-1 text-sm italic font-semibold text-yellow-600">
                            Warning: Changing the level might affect linked data.
                        </p>
                    </div>
                </template>

                {{-- Subject Name --}}
                <template x-if="selectedLevel">
                    <div class="mb-6">
                        <x-input-label for="name" :value="__('Subject Name')" />
                        <input id="name" name="name" type="text"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            :disabled="!selectedLevel" x-model="subjectName" required autocomplete="off" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        {{-- Restriction note --}}
                        <p class="mt-1 text-sm italic text-gray-700">
                            Restriction: Subject name must be unique within the selected level.
                        </p>
                    </div>
                </template>

                {{-- Submit --}}
                <div x-show="selectedLevel" class="mt-4">
                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                        Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Alpine.js --}}
    <script>
        function subjectForm() {
            return {
                educationType: @json(old('education_type', $subject->education_type)),
                selectedLevel: @json((string) old('level_id', $subject->level_id)),
                subjectName: @json(old('name', $subject->name)),
                allLevels: @json($levels),
                filteredLevels: [],

                init() {
                    this.filterLevels();
                },

                onEducationChange() {
                    // Usually disabled, but just in case:
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
                    if (this.educationType && this.selectedLevel && this.subjectName.trim() !== '') {
                        this.$refs.form.submit();
                    } else {
                        alert('Please fill all required fields.');
                    }
                }
            };
        }
    </script>
</x-app-layout>
