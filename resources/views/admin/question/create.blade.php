<x-app-layout>
    <div class="px-6 py-10 mx-auto bg-white border border-gray-100 shadow-xl max-w-7xl rounded-3xl"
        x-data="questionForm()" x-cloak style="font-family: 'Inter', sans-serif;">
        <h1 class="mb-8 text-4xl font-bold text-blue-900">📝 Create New Question</h1>

        <!-- Step 1: Question Metadata -->
        <!-- Step 1: Question Metadata -->
        <div class="grid grid-cols-1 gap-6 p-6 mb-10 border border-blue-200 bg-blue-50 rounded-2xl"
            :class="educationType ? 'md:grid-cols-2' : 'md:grid-cols-1'">

            <!-- Education Type -->
            <div>
                <label class="block mb-2 text-sm font-medium text-blue-700">🎓 Education Type</label>
                <select id="educationType" x-model="educationType" @change="onEducationTypeChange"
                    class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose --</option>
                    <option value="primary">Primary</option>
                    <option value="secondary">Secondary</option>
                </select>
            </div>

            <!-- Level -->
            <div x-show="educationType" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">📚 Level</label>
                <select x-model="selectedLevel" @change="onLevelChange"
                    class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose Level --</option>
                    <template x-for="level in levels" :key="level.id">
                        <option :value="level.name" x-text="level.name"></option>
                    </template>
                </select>
            </div>

            <!-- Subject -->
            <div x-show="selectedLevel" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">📘 Subject</label>
                <select x-model="selectedSubjectId" @change="onSubjectChange"
                    class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>-- Choose Subject --</option>
                    <template x-for="subject in subjects" :key="subject.id">
                        <option :value="subject.id" x-text="subject.name"></option>
                    </template>
                </select>
            </div>

            <!-- Question Type -->
            <div x-show="selectedSubject" x-transition>
                <label class="block mb-2 text-sm font-medium text-blue-700">❓ Question Type</label>
                <select x-model="questionType"
                    class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
            <div class="p-6 bg-white border shadow rounded-xl">
                <label class="block mb-3 text-lg font-semibold text-blue-700">🧠 Question Content</label>
                <div x-ref="questionContentEditor"
                    class="min-h-[160px] border border-gray-300 p-4 rounded-lg focus-within:ring-2 focus-within:ring-blue-400">
                </div>
                <input type="hidden" name="question_data[content]" :value="questionContent" required />
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
                            class="px-4 py-2 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200"
                            x-show="options.length > 1">
                            + Add Option
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="questionType === 'true_false'">
                <div>
                    <label class="block mb-3 text-lg font-semibold text-blue-700">Answer</label>
                    <select name="question_data[true_false_answer]"
                        class="w-full p-3 text-lg border rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-200"
                        x-model="trueFalseAnswer" required>
                        <option value="True">True</option>
                        <option value="False">False</option>
                    </select>
                </div>
            </template>
            {{-- Linking --}}
            <template x-if="questionType === 'linking'">
                <div class="space-y-4">
                    <x-input-label value="Matching Pairs" class="mb-4 text-lg font-semibold text-blue-700" />

                    <template x-for="(option, index) in linkingOptions" :key="index">
                        <div
                            class="relative flex flex-col gap-4 p-3 transition-shadow duration-200 border-2 border-blue-200 rounded-xl bg-blue-50 hover:shadow-blue-200">
                            <button type="button" class="absolute text-red-600 top-2 right-2 hover:text-red-800"
                                @click="linkingOptions.splice(index, 1)">✕</button>

                            <div class="flex flex-col md:flex-row md:space-x-6">
                                <!-- Left Label -->
                                <div class="flex-1">
                                    <div class="mb-1">
                                        <span class="font-medium text-blue-700">Left (Label):</span>
                                        <label class="ml-2 text-sm">
                                            <input type="radio"selectQuestionType
                                                :name="'question_data[options][' + index + '][label_type]'"
                                                value="text" x-model="option.label_type"> Text
                                        </label>
                                        <label class="ml-2 text-sm">
                                            <input type="radio"
                                                :name="'question_data[options][' + index + '][label_type]'"
                                                value="image" x-model="option.label_type"> Image
                                        </label>
                                    </div>

                                    <template x-if="option.label_type === 'text'">
                                        <input type="text"
                                            class="w-full p-3 border-2 border-blue-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200"
                                            x-model="option.label_text"
                                            :name="'question_data[options][' + index + '][label_text]'"
                                            placeholder="Label Text">
                                    </template>

                                    <template x-if="option.label_type === 'image'">
                                        <div class="space-y-1">
                                            <input type="file" accept="image/*"
                                                :name="'question_data[options][' + index + '][label_image]'"
                                                @change="previewFile($event, option, 'label')" />
                                            <img x-show="option.label_preview" :src="option.label_preview"
                                                class="object-cover w-16 h-16 rounded">
                                        </div>
                                    </template>
                                </div>

                                <!-- Right Value -->
                                <div class="flex-1">
                                    <div class="mb-1">
                                        <span class="font-medium text-blue-700">Right (Value):</span>
                                        <label class="ml-2 text-sm">
                                            <input type="radio"
                                                :name="'question_data[options][' + index + '][value_type]'"
                                                value="text" x-model="option.value_type"> Text
                                        </label>
                                        <label class="ml-2 text-sm">
                                            <input type="radio"
                                                :name="'question_data[options][' + index + '][value_type]'"
                                                value="image" x-model="option.value_type"> Image
                                        </label>
                                    </div>

                                    <template x-if="option.value_type === 'text'">
                                        <input type="text"
                                            class="w-full p-3 border-2 border-blue-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-200"
                                            x-model="option.value_text"
                                            :name="'question_data[options][' + index + '][value_text]'"
                                            placeholder="Value Text">
                                    </template>

                                    <template x-if="option.value_type === 'image'">
                                        <div class="space-y-1">
                                            <input type="file" accept="image/*"
                                                :name="'question_data[options][' + index + '][value_image]'"
                                                @change="previewFile($event, option, 'value')" />
                                            <img x-show="option.value_preview" :src="option.value_preview"
                                                class="object-cover w-16 h-16 rounded">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button"
                        class="px-6 py-2 font-semibold text-blue-800 transition bg-blue-200 rounded-xl hover:bg-blue-300"
                        @click="addLinkingOption()">
                        + Add Match
                    </button>
                </div>
            </template>

            <template x-if="questionType === 'rearranging'">
                <div class="space-y-4">
                    <!-- Question instruction input -->
                    <label class="block text-lg font-semibold text-blue-700">Question Instruction</label>
                    <input type="text" name="question_data[question_text]" x-model="rearrangingText"
                        class="w-full p-3 border rounded-lg focus:ring-4 focus:ring-blue-200"
                        placeholder="e.g., Let me help you." required>

                    <!-- Automatically splits into input fields -->
                    <label class="block mt-4 text-lg font-semibold text-blue-700">Correct Word/Phrase Order</label>
                    <div class="flex flex-wrap items-center gap-3">

                        <template x-for="(item, index) in rearrangingItems" :key="index">
                            <div class="relative mb-3">
                                <input type="text" :name="'question_data[rearranging][answer][' + index + ']'"
                                    x-model="rearrangingItems[index]"
                                    class="w-full p-3 border rounded-lg focus:ring-4 focus:ring-blue-200"
                                    placeholder="Word or phrase" required>

                            </div>

                        </template>
                    </div>

                    <!-- Preview -->
                    <div class="mt-4" x-show="rearrangingItems.length > 1">
                        <label class="text-sm text-gray-600">Preview:</label>
                        <div class="p-3 mt-1 bg-gray-100 rounded-lg">
                            <span x-text="rearrangingItems.join(' ')"></span>
                        </div>
                    </div>

                    <!-- x-effect to update rearrangingItems in real-time -->
                    <div
                        x-effect="
                            if (questionType === 'rearranging') {
                                rearrangingItems = rearrangingText
                                    .replace(/[.,!?]/g, '') // optional: remove punctuation
                                    .trim()
                                    .split(/\s+/)
                                    .filter(word => word.length > 0);
                            }
                        ">
                    </div>
                </div>
            </template>

            <template x-if="questionType === 'fill_blank'">
                <div class="mb-6">
                    <label for="fillBlankType" class="block mb-2 text-lg font-semibold text-blue-700">
                        Fill in the Blank Type
                    </label>
                    <select id="fillBlankType" x-model="fillBlankType"
                        class="w-full px-4 py-2 text-gray-700 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="grammar_cloze">Grammar Cloze</option>
                        <option value="grammar_cloze_with_options">Grammar Cloze with Options</option>
                    </select>

                    <div class="mt-4">


                        <button type="button" @click="generateQuestions()"
                            class="px-4 py-2 mt-4 text-white bg-blue-600 rounded hover:bg-blue-700">
                            Generate Questions
                        </button>

                        <template x-if="questions.length > 0">
                            <div class="mt-6">
                                <h3 class="mb-2 text-lg font-semibold">Generated Questions JSON Preview:</h3>
                                <pre class="p-3 overflow-auto bg-gray-100 rounded" style="max-height: 300px; font-size: 0.9rem;">
                            <code x-text="JSON.stringify(getQuestionPayload(), null, 2)"></code>
                             </pre>
                            </div>
                        </template>

                        <template x-if="fillBlankType === 'grammar_cloze' && questions.length > 0">
                            <div class="mt-6">
                                <h3 class="mb-2 text-lg font-semibold">Fill in the Blank Inputs</h3>
                                <template x-for="(q, index) in questions" :key="index">
                                    <div class="mb-3">
                                        <label class="block mb-1 font-semibold text-blue-700">Blank Number: <span
                                                x-text="q.blank_number"></span></label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded"
                                            placeholder="User's answer" x-model="q.userAnswer" />
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="fillBlankType === 'grammar_cloze_with_options' && questions.length > 0">
                            <div class="mt-6">
                                <h3 class="mb-2 text-lg font-semibold">Fill in the Blank with Options</h3>
                                <template x-for="(q, index) in questions" :key="index">
                                    <div class="p-3 mb-4 border border-gray-300 rounded">
                                        <label class="block mb-2 font-semibold text-blue-700">Blank Number: <span
                                                x-text="q.blank_number"></span></label>
                                        <template x-if="q.options && q.options.length > 0">
                                            <template x-for="(opt, i) in q.options" :key="i">
                                                <label class="inline-flex items-center mr-4">
                                                    <input type="radio" :name="'blank_' + q.blank_number"
                                                        :value="opt" x-model="q.userAnswer" />
                                                    <span class="ml-2" x-text="opt"></span>
                                                </label>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
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
            questionTypes: @json(\App\Enum\QuestionTypes::TYPES),
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
            linkingOptions: [],

            addLinkingOption() {
                this.linkingOptions.push({
                    label_type: 'text',
                    label_text: '',
                    label_image: '',
                    label_preview: '',
                    value_type: 'text',
                    value_text: '',
                    value_image: '',
                    value_preview: '',
                });
            },

            previewFile(event, option, type) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = () => {
                    if (type === 'label') {
                        option.label_preview = reader.result;
                        option.label_image = file;
                    } else {
                        option.value_preview = reader.result;
                        option.value_image = file;
                    }
                };
                reader.readAsDataURL(file);
            },

            rearrangingText: '',
            rearrangingItems: [],
            addRearrangingItem() {
                this.rearrangingItems.push('');
            },

            // Fill in the blank
            fillBlankType: 'grammar_cloze',

            fillBlankType: 'grammar_cloze',
            paragraph: '',
            questions: [],

            generateQuestions() {
                this.questions = [];
                // Parse the paragraph for blanks:
                // Pattern 1: (number) ______  --> grammar_cloze, input type
                // Pattern 2: (number)[option1 / option2 / option3] --> grammar_cloze_with_options, radio

                if (!this.paragraph.trim()) {
                    alert('Please enter a paragraph with blanks.');
                    return;
                }

                if (this.fillBlankType === 'grammar_cloze') {
                    // Detect (number) ______
                    const regex = /\((\d+)\)\s*_{2,}/g; // matches (15) ______ (2 or more underscores)
                    let match;
                    while ((match = regex.exec(this.paragraph)) !== null) {
                        this.questions.push({
                            blank_number: parseInt(match[1]),
                            correct_answer: null,
                            input_type: 'input',
                            userAnswer: '',
                        });
                    }
                } else if (this.fillBlankType === 'grammar_cloze_with_options') {
                    // Detect (number)[option1 / option2 / option3]
                    const regex = /\((\d+)\)\[([^\]]+)\]/g;
                    let match;
                    while ((match = regex.exec(this.paragraph)) !== null) {
                        // Split options by slash and trim spaces
                        let options = match[2].split('/').map(o => o.trim());
                        this.questions.push({
                            blank_number: parseInt(match[1]),
                            correct_answer: null,
                            input_type: 'radio',
                            options: options,
                            userAnswer: '',
                        });
                    }
                }
            },

            getQuestionPayload() {
                // Return JSON structure like your example
                return {
                    paragraph: this.paragraph,
                    question_type: this.fillBlankType,
                    questions: this.questions.map(q => {
                        let base = {
                            blank_number: q.blank_number,
                            correct_answer: q.correct_answer,
                            input_type: q.input_type,
                        };
                        if (q.input_type === 'radio') {
                            base.options = q.options;
                        }
                        return base;
                    }),
                };
            },


            // Submit question form
            submitForm() {
                this.$root.querySelector('form').submit();
            }
        }
    }
</script>
