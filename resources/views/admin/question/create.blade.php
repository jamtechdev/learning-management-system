<x-app-layout>
    <div class="px-6 py-10 mx-auto bg-white border border-gray-100 shadow-xl max-w-7xl rounded-3xl"
        x-data="questionForm()" x-cloak style="font-family: 'Inter', sans-serif;">
        <h1 class="mb-8 text-4xl font-bold text-blue-900">📝 Create New Question</h1>

        <!-- Step 1: Question Metadata -->
        <div class="grid grid-cols-1 gap-6 p-6 mb-10 border border-blue-200 md:grid-cols-2 bg-blue-50 rounded-2xl">
            <!-- Education Type -->
            <div>
                <label class="block mb-2 text-sm font-medium text-blue-700">🎓 Education Type</label>
                <select id="educationType" x-model="educationType" @change="onEducationTypeChange" class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose --</option>
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
                </select>
            </div>

            <!-- Level -->
            <div x-show="educationType" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">📚 Level</label>
                <select x-model="selectedLevel" @change="onLevelChange" class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose Level --</option>
                    <template x-for="level in levels" :key="level.id">
                        <option :value="level.name" x-text="level.name"></option>
                    </template>
                </select>
            </div>

            <!-- Subject -->
            <div x-show="selectedLevel" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">📘 Subject</label>
                <select x-model="selectedSubjectId" @change="onSubjectChange" class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose Subject --</option>
                    <template x-for="subject in subjects" :key="subject.id">
                        <option :value="subject.id" x-text="subject.name"></option>
                    </template>
                </select>
            </div>

            <!-- Question Type -->
            <div x-show="selectedSubject" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">❓ Question Type</label>
                <select x-model="questionType" class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose Type --</option>
                    <template x-for="type in questionTypes" :key="type">
                        <option :value="type" x-text="type.replace('_', ' ')"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Step 2: Question Form -->
        <form method="POST" action="{{ route('admin.questions.store') }}" x-show="questionType"
            @submit.prevent="submitForm" class="space-y-10" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="question_data[education_type]" :value="educationType" />
            <input type="hidden" name="question_data[level_id]" :value="selectedLevelId" />
            <input type="hidden" name="question_data[subject_id]" :value="selectedSubject?.id" />
            <input type="hidden" name="question_data[type]" :value="questionType" />

            <!-- Question Content -->
            <div x-show="questionType === 'mcq'" class="p-6 bg-white border shadow rounded-xl">
                <label class="block mb-3 text-lg font-semibold text-blue-700">🧠 Question Content</label>
                <div x-ref="questionContentEditor"
                    class="min-h-[160px] border border-gray-300 p-4 rounded-lg focus-within:ring-2 focus-within:ring-blue-400">
                </div>
                <input type="hidden" name="question_data[content]" :value="questionContent" />
            </div>

            <!-- MCQ Options -->
            <template x-if="questionType === 'mcq'">
                <div class="p-6 bg-white border shadow rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-blue-700">🧩 MCQ Options</h2>
                        <div class="flex gap-4">
                            <button type="button" @click="insertMCQBlank" x-show="!hasInsertedBlank"
                                class="px-3 py-1 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                                + Insert Blank
                            </button>
                            <button type="button" @click="removeMCQBlank" x-show="hasInsertedBlank"
                                class="px-3 py-1 text-sm text-red-700 bg-red-100 rounded-lg hover:bg-red-200">
                                ✕ Remove Blank
                            </button>
                        </div>
                    </div>

                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center gap-3 mb-3">
                            <input type="radio" :name="'question_data[correct_option]'" :value="index"
                                class="w-5 h-5 text-blue-600" @change="setCorrect(index)" :checked="option.is_correct"
                                required />
                            <input type="text" :name="'question_data[options][' + index + ']'"
                                x-model="option.value" class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                placeholder="Option text" required />
                            <button type="button" @click="removeOption(index)" class="text-red-600 hover:text-red-800"
                                x-show="options.length > 1">
                                ✕
                            </button>
                        </div>
                    </template>

                    <div class="mt-4">
                        <button type="button" @click="addOption"
                            class="px-4 py-2 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200" x-show="options.length > 1">
                            + Add Option
                        </button>
                    </div>
                </div>
            </template>

            <!-- Submit Buttons -->
            <div class="flex justify-between mt-10">
                <button type="button" @click="step = step > 1 ? step - 1 : 1"
                    class="px-6 py-3 text-blue-600 border border-blue-500 rounded-lg hover:bg-blue-50">
                    ← Back
                </button>
                <button type="submit"
                    class="px-6 py-3 font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700">
                    ✅ Save Question
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    function questionForm() {
        return {
            quill: null,
            questionContent: '',
            educationType: '',
            selectedLevel: '',
            selectedLevelId: '',
            selectedSubjectId: '',
            selectedSubject: null,
            questionType: '',
            questionTypes: @json(['mcq', 'fill_blank', 'open-ended']),
            levelsByType: @json($levels),
            levels: [],
            subjects: [],
            options: [],
            hasInsertedBlank: false,

            init() {
                const editor = this.$refs.questionContentEditor;

                const toolbarOptions = [
                    [{
                        header: [1, 2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'direction': 'rtl'
                    }],
                    [{
                        'size': ['small', false, 'large', 'huge']
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'font': []
                    }],
                    [{
                        'align': []
                    }],
                    ['link', 'image', 'video', 'formula'],
                    ['clean']
                ];

                this.quill = new Quill(editor, {
                    theme: 'snow',
                    placeholder: 'Type your question...',
                    modules: {
                        toolbar: toolbarOptions,
                    }
                });

                this.quill.on('text-change', () => {
                    this.questionContent = this.quill.root.innerHTML;
                });
            },

            // Called when education type changes
            onEducationTypeChange() {
                this.levels = this.levelsByType[this.educationType] || [];
                this.selectedLevel = '';
                this.selectedLevelId = '';
                this.subjects = [];
                this.selectedSubject = null;
                this.selectedSubjectId = '';
                this.questionType = '';
            },

            // Called when level changes
            onLevelChange() {
                const foundLevel = this.levels.find(l => l.name === this.selectedLevel);
                if (foundLevel) {
                    this.selectedLevelId = foundLevel.id;
                    this.subjects = foundLevel.subjects || [];
                    this.selectedSubject = null;
                    this.selectedSubjectId = '';
                    this.questionType = '';
                }
            },

            // Called when subject changes
            onSubjectChange() {
                this.selectedSubject = this.subjects.find(s => s.id == this.selectedSubjectId);
                this.questionType = '';
            },

            // Called when MCQ blank is inserted
            insertMCQBlank() {
                const blankNumber = (this.questionContent.match(/_____/g) || []).length + 1;
                const insertText = `${blankNumber}. _____ `;

                let range = this.quill.getSelection(true);
                const editorLength = this.quill.getLength();
                let insertIndex = range && typeof range.index === 'number' && range.index >= 0 && range.index <=
                    editorLength ?
                    range.index :
                    editorLength;

                this.quill.insertText(insertIndex, insertText, 'user');
                this.quill.setSelection(insertIndex + insertText.length, 0);
                this.questionContent = this.quill.root.innerHTML;
                this.hasInsertedBlank = true;

                this.options = [{
                        value: '',
                        is_correct: false
                    },
                    {
                        value: '',
                        is_correct: false
                    },
                ];
            },

            // Remove one blank
            removeMCQBlank() {
                const blankRegex = /\d+\.\s*_____\s*/;
                const text = this.quill.getText();
                const match = text.match(blankRegex);

                if (match) {
                    const index = text.indexOf(match[0]);
                    this.quill.deleteText(index, match[0].length, 'user');
                }

                this.questionContent = this.quill.root.innerHTML;
                this.hasInsertedBlank = false;
                this.options = [];
            },

            // Add MCQ option
            addOption() {
                this.options.push({
                    value: '',
                    is_correct: false
                });
            },

            // Remove MCQ option
            removeOption(index) {
                if (this.options.length > 1) {
                    this.options.splice(index, 1);
                }
            },

            // Set correct option
            setCorrect(index) {
                this.options.forEach((opt, i) => {
                    opt.is_correct = (i === index);
                });
            },

            // Submit question form
            submitForm() {
                this.$root.querySelector('form').submit();
            }
        }
    }
</script>
