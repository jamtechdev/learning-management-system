<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Add New Topic
                </h2>
                <form action="{{ route('admin.topics.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2" x-data="form()">
                        <!-- Education Type -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Education Type</label>
                            <select name="education_type"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('education_type') ? 'border-red-500' : '' }}"
                                id="education_type" x-model="education_type" @change="onEducationChange">
                                <option value="">Select Education Type</option>
                                <option value="primary" :selected="education_type == 'primary'">Primary</option>
                                <option value="secondary" :selected="education_type == 'secondary'">Secondary</option>
                            </select>
                            @error('education_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Level -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Select Topic Level</label>
                            <select name="level_id"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('level_id') ? 'border-red-500' : '' }}"
                                id="level_id" x-model="level_id" @change="onLevelChange">
                                <option value="">Select Level</option>
                                <template x-for="level in filteredLevels" :key="level.id">
                                    <option :value="level.id" :selected="level.id == level_id" x-text="level.name">
                                    </option>
                                </template>
                            </select>
                            @error('level_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Subject -->
                        <div x-show="level_id" x-transition x-cloak>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Select Topic Subject</label>
                            <select name="subject_id"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('subject_id') ? 'border-red-500' : '' }}"
                                id="subject_id" x-model="subject_id">
                                <option value="">Select Subject</option>
                                <template x-for="subject in subjects" :key="subject.id">
                                    <option :value="subject.id" :selected="subject.id == subject_id"
                                        x-text="subject.name"></option>
                                </template>
                            </select>
                            @error('subject_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Name -->
                        <div x-show="level_id && subject_id" x-transition x-cloak>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" x-model="name" placeholder="Topic"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200 {{ $errors->has('name') ? 'border-red-500' : '' }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Button Container -->
                    <div class="flex items-end justify-end mt-6 space-x-2">
                        <!-- Submit Button -->
                        <button type="submit"
                            class="px-6 py-2 text-white bg-[#3e80f9] rounded-md shadow-md bg-[#f7941e] focus:outline-none">
                            Save Topic
                        </button>
                        <!-- Back Button -->
                        <a href="{{ route('admin.topics.index') }}"
                            class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md shadow-md hover:bg-gray-300 focus:outline-none">
                            Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $subjects = [];
        $subject_id = old('subject_id', null);
        $level_id = old('level_id', null);
        $name = old('name', null);
        if ($subject_id) {
            $subjects = $levels->find($level_id)->subjects;
        }
    @endphp
    @push('scripts')
        <script>
            function form() {
                return {
                    levels: @json($levels),
                    education_type: '{{ old('education_type', '') }}',
                    level_id: '{{ $level_id }}',
                    subjects: @json($subjects),
                    subject_id: '{{ $subject_id }}',
                    name: '{{ $name }}',
                    filteredLevels: [],

                    init() {
                        if (this.education_type) {
                            this.filterLevels();
                        }
                    },

                    onEducationChange() {
                        this.level_id = '';
                        this.filterLevels();
                    },

                    filterLevels() {
                        if (!this.education_type) {
                            this.filteredLevels = [];
                            return;
                        }
                        this.filteredLevels = this.levels.filter(level =>
                            level.education_type.toLowerCase() === this.education_type.toLowerCase()
                        );
                    },

                    onLevelChange() {
                        console.log(this.level_id, this.levels.find(l => l.id == this.level_id));
                        const level = this.levels.find(l => l.id == this.level_id);
                        if (level) {
                            this.subjects = level.subjects || [];
                        }
                        this.subject_id = '';
                        this.name = '';
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
