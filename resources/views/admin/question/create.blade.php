<x-app-layout>
    <div class="px-6 py-10 mx-auto bg-white border border-gray-100 shadow-xl max-w-7xl rounded-3xl"
        x-data="questionForm()" x-cloak style="font-family: 'Inter', sans-serif;">
        <h1 class="mb-8 text-4xl font-bold text-blue-900">📝 Create New Question</h1>

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
                        <option :value="type" x-text="formatQuestionType(type)"></option>
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
            <!-- Grammar Cloze Metadata (only when type is grammar_cloze_with_options) -->

            <template x-if="questionType === 'grammar_cloze_with_options'">
                <input type="hidden" name="question_data[metadata]" id="metadataInput" x-model="formattedJson" />
            </template>

            <template x-if="questionType === 'underlinecorrect'">
                <input type="hidden" name="question_data[underline_metadata]" id="correctUnderlineInput"
                    x-model="formattedJson" />
            </template>

            <!-- Comprehension Metadata (only when type is comprehension) -->
            <template x-if="questionType === 'comprehension'">
                <input type="hidden" id="comprehensionMetadataInput" name="question_data[comprehension_metadata]"
                    x-model="formattedJson" />
            </template>

            <template x-if="questionType === 'editing'">
                <input type="hidden" id="editingMetadataInput" name="question_data[editing_metadata]"
                    x-model="formattedJson" />
            </template>

            <template x-if="questionType">
                <div class="px-6 py-4 mb-6 text-xl font-bold text-white bg-blue-600 rounded-lg shadow">
                    You are adding a
                    <span x-text="questionTypeLabels[questionType] || questionType"></span> question
                </div>
            </template>

            <!-- Question Content -->
            <div class="p-6 bg-white border shadow rounded-xl">
                <label class="block mb-3 text-lg font-semibold text-blue-700">📘 Instruction</label>
                <textarea name="question_data[instruction]" x-model="questionInstruction"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter instructions for this question..." rows="1" required></textarea>

                <label class="block mb-3 text-lg font-semibold text-blue-700">
                    🧠
                    <span
                        x-text="
                        questionType === 'grammar_cloze_with_options' ? 'Grammar Cloze Passage' :
                        (questionType === 'comprehension' ? 'Comprehension Passage' : 'Question Content')
                    "></span>
                </label>
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
                                class="w-5 h-5 text-blue-600" @change="setCorrect(index)"
                                :checked="option.is_correct" required />
                            <input type="text" :name="'question_data[options][' + index + ']'"
                                x-model="option.value" class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                placeholder="Option text" required />
                            <button type="button" @click="removeOption(index)"
                                class="text-red-600 hover:text-red-800" x-show="options.length > 1">
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

            <template x-if="questionType === 'grammar_cloze_with_options'">
                <div class="p-6 bg-white border rounded shadow">
                    <!-- Generate Button -->
                    <div class="mb-6">
                        <a href="javascript:void(0)" @click="parseGrammarCloze"
                            class="px-4 py-2 font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                            Generate Grammar Cloze Questions
                        </a>
                    </div>

                    <!-- Shared Options Input -->
                    <div class="mb-6">
                        <label class="block mb-2 font-semibold text-gray-700">Shared Options</label>
                        <input type="text" x-model="sharedOptionsRaw" @input="updateGrammarClozeJson"
                            class="w-full px-3 py-2 border rounded" placeholder="Enter options separated by commas ,">
                    </div>

                    <!-- Detected Blanks -->
                    <template x-if="questions.length">
                        <div class="mb-6">
                            <h2 class="mb-4 text-xl font-bold">Detected Blanks</h2>
                            <template x-for="(q, idx) in questions" :key="q.blank_number">
                                <div
                                    class="flex flex-col gap-4 p-4 mb-4 border rounded md:flex-row md:items-center md:justify-between bg-gray-50">
                                    <div><strong>Blank #</strong>: <span x-text="q.blank_number"></span></div>
                                    <div>
                                        <input type="text" x-model="q.correct_answer" placeholder="Correct Answer"
                                            class="px-3 py-2 border rounded" @input="updateGrammarClozeJson">
                                    </div>
                                    <button @click="removeQuestion(idx)"
                                        class="text-red-600 hover:underline">Remove</button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="questionType === 'underlinecorrect'">
                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl">

                    <button type="button" @click="parseParagraph"
                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                        🔍 Generate Dropdowns
                    </button>

                    <div x-show="questions.length" class="p-4 mt-4 border rounded bg-gray-50">
                        <h2 class="mb-3 font-semibold text-gray-700">Select Correct Answers</h2>
                        <template x-for="(q, index) in questions" :key="q.blank_number">
                            <div class="mb-3">
                                <label class="text-sm font-medium text-gray-600">
                                    ( <span x-text="q.blank_number"></span> )
                                </label>
                                <select class="p-1 ml-2 border rounded" x-model="q.correct_answer"
                                    @change="updateJSON">
                                    <option value="">-- Select --</option>
                                    <template x-for="opt in q.options" :key="opt">
                                        <option :value="opt" x-text="opt"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                    </div>

                    <!-- Metadata JSON Preview -->
                    <div x-show="formattedJson"
                        class="p-4 mt-4 overflow-x-auto text-sm bg-gray-100 border rounded shadow">
                        <label class="block mb-1 font-semibold text-gray-600">Generated JSON</label>
                        <textarea x-model="formattedJson" class="w-full h-48 font-mono text-sm bg-white border rounded" readonly></textarea>
                    </div>
                </div>
            </template>

            <!-- This auto-runs updateComprehensionJson whenever Alpine data changes -->
            <div x-effect="updateComprehensionJson()"></div>
            <template x-if="questionType === 'comprehension'">
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold">Questions & Answers</label>
                        <template x-for="(question, index) in comprehensionQuestions" :key="index">
                            <div class="p-3 mb-4 border rounded">
                                <label class="block text-sm">Question</label>
                                <input type="text" x-model="question.text"
                                    class="w-full p-2 mt-1 border rounded" />

                                <label class="block mt-3 text-sm">Answer</label>
                                <input type="text" x-model="question.answer"
                                    class="w-full p-2 mt-1 border rounded" />

                                <button type="button" class="mt-2 text-sm text-red-500"
                                    @click="removeComprehensionQuestion(index)">Remove</button>
                            </div>
                        </template>

                        <button type="button" class="px-4 py-2 mt-2 text-white bg-blue-500 rounded"
                            @click="addComprehensionQuestion()">+ Add Question</button>
                    </div>
                </div>
            </template>

            <template x-if="questionType === 'editing'">
                <div class="p-4 mt-6 bg-white border rounded">
                    <!-- Input Fields -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <input type="number" x-model="editingBoxNumber" class="w-1/6 p-2 border rounded"
                            placeholder="Box #" />
                        <input type="text" x-model="editingWrong" class="w-1/4 p-2 border rounded"
                            placeholder="Misspelled Word" />
                        <input type="text" x-model="editingCorrect" class="w-1/4 p-2 border rounded"
                            placeholder="Correct Word" />
                        <button type="button" class="px-4 py-2 text-white bg-green-600 rounded"
                            @click="addEditingError">
                            Add Error
                        </button>
                    </div>

                    <!-- Errors List -->
                    <template x-if="editingErrors.length > 0">
                        <div class="mb-4">
                            <h3 class="mb-2 font-semibold">Mistakes Added:</h3>
                            <ul class="pl-6 space-y-1 list-disc">
                                <template x-for="(item, idx) in editingErrors" :key="idx">
                                    <li>
                                        <span class="font-semibold">Box <span x-text="item.box"></span></span>:
                                        <span class="font-semibold text-red-500" x-text="item.wrong"></span>
                                        →
                                        <span class="font-semibold text-green-500" x-text="item.correct"></span>
                                        <button @click="removeEditingError(idx)"
                                            class="ml-2 text-sm text-blue-600 hover:underline">
                                            Remove
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
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
            // ===================== Grammar Cloze =====================
            paragraph: '',
            sharedOptionsRaw: '',
            questions: [],
            formattedJson: '', // existing grammar cloze JSON output
            // ========== NEW: Comprehension data =============
            comprehensionPassage: '',
            comprehensionQuestions: [],
            comprehensionJson: '',
            questionInstruction: "",
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
                if (this.questionType === 'grammar_cloze_with_options') {
                    this.parseGrammarCloze();
                } else if (this.questionType === 'comprehension') {
                    this.updateComprehensionJson(); // ✅ auto update comprehension JSON on passage edit
                }
                if (this.questionType === 'editing') {
                    this.instruction = "Correct the words in bold for spelling.";
                    this.originalParagraph = "She recieved the invitashun yesterday but did not respond.";
                    this.editingQuestions = [{
                        id: 1,
                        wrong: "invitashun",
                        correct: "invitation"
                    }];
                }
                this.$watch('questionType', (newType, oldType) => {
                    if (newType !== oldType) {
                        this.fullFormReset();
                    }
                });

            },

            fullFormReset() {
                this.questionInstruction = '';
                this.questionContent = '';
                this.options = [];
                this.correctAnswer = null;
                this.sharedOptions = [];
                this.paragraph = '';
                this.groupedQuestions = [];

                // Reset the rich text editor manually
                const editor = this.$refs.questionContentEditor;
                if (editor && editor.__quill) {
                    editor.__quill.setText('');
                }
            },

            // Grammar Cloze parse and update
            parseGrammarCloze() {
                if (!this.questionContent) return;

                // Get raw text from Quill HTML content
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.questionContent;
                const rawText = tempDiv.innerText.trim();

                const blankRegex = /\((\d+)\)\s*_{4,}/g;
                const matches = [];
                let match;
                while ((match = blankRegex.exec(rawText)) !== null) {
                    const blankNumber = parseInt(match[1]);
                    matches.push({
                        id: matches.length + 1,
                        blank_number: blankNumber,
                        correct_answer: '',
                        input_type: 'input'
                    });
                }

                this.questions = matches;
                this.updateGrammarClozeJson();
            },

            // Update ONLY Grammar Cloze JSON and hidden input
            updateGrammarClozeJson() {
                const sharedOptions = this.sharedOptionsRaw
                    .split(',')
                    .map(opt => opt.trim())
                    .filter(opt => opt.length > 0);

                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.questionContent;
                const rawText = tempDiv.innerText.trim();

                const output = {
                    paragraph: rawText,
                    question_type: 'grammar_cloze_with_options',
                    question_group: {
                        shared_options: sharedOptions
                    },
                    questions: this.questions.map(q => ({
                        id: q.id,
                        blank_number: q.blank_number,
                        correct_answer: q.correct_answer,
                        input_type: q.input_type
                    }))
                };

                this.formattedJson = JSON.stringify(output, null, 2);

                // Update hidden input for Grammar Cloze JSON (keep old #metadataInput or add new id)
                const hiddenInput = document.querySelector('#metadataInput');
                if (hiddenInput) {
                    hiddenInput.value = this.formattedJson;
                }
            },

            // Remove question for Grammar Cloze
            removeQuestion(index) {
                this.questions.splice(index, 1);
                this.updateGrammarClozeJson();
            },

            // ========== NEW FUNCTIONS FOR COMPREHENSION =============

            // Add comprehension question

            addComprehensionQuestion() {
                this.comprehensionQuestions.push({
                    text: '',
                    answer: ''
                });
            },

            removeComprehensionQuestion(index) {
                this.comprehensionQuestions.splice(index, 1);
            },

            updateComprehensionJson() {
                const output = {
                    passage: this.questionContent.trim(),
                    subquestions: []
                };

                this.comprehensionQuestions.forEach((q, index) => {
                    const i = index + 1;
                    output.subquestions.push({
                        [`ques${i}`]: q.text.trim(),
                        answer: q.answer.trim()
                    });
                });

                this.comprehensionJson = output;

                // Store stringified JSON in hidden input for backend
                const hiddenInput = document.querySelector('#comprehensionMetadataInput');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(this.comprehensionJson);
                }
            },

            // ===================== (existing) handlers for EducationType, Level, Subject, MCQ etc =====================
            onEducationTypeChange() {
                this.levels = this.levelsByType[this.educationType] || [];
                this.selectedLevel = '';
                this.selectedLevelId = '';
                this.subjects = [];
                this.selectedSubject = null;
                this.selectedSubjectId = '';
                this.questionType = '';
            },
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
            onSubjectChange() {
                this.selectedSubject = this.subjects.find(s => s.id == this.selectedSubjectId);
                this.questionType = '';
            },

            insertMCQBlank() {
                const blankNumber = (this.questionContent.match(/_____/g) || []).length + 1;
                const insertText = `${blankNumber}. _____ `;

                let range = this.quill.getSelection(true);
                const editorLength = this.quill.getLength();
                let insertIndex = range && typeof range.index === 'number' && range.index >= 0 && range.index <=
                    editorLength ?
                    range.index : editorLength;

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

            addOption() {
                this.options.push({
                    value: '',
                    is_correct: false
                });
            },

            removeOption(index) {
                if (this.options.length > 1) {
                    this.options.splice(index, 1);
                }
            },

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
            trueFalseAnswer: 'True',
            linkingPairs: [{
                label: '',
                value: ''
            }, {
                label: '',
                value: ''
            }],
            rearrangingText: '',
            rearrangingItems: [],
            addRearrangingItem() {
                this.rearrangingItems.push('');
            },

            formatQuestionType(type) {
                return type
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },

            questionTypeLabels: '',
            questionTypeLabels: {
                mcq: "Multiple Choice",
                true_false: "True or False",
                linking: "Matching",
                rearranging: "Rearranging",
                grammar_cloze_with_options: "Grammar Cloze (With Options)",
                comprehension: "Comprehension"
            },

            // === Editing Question Specific States ===
            editingParagraph: '',
            editingBoxNumber: '', // <--- NEW: Manual box number input
            editingWrong: '',
            editingCorrect: '',
            editingErrors: [],
            editingJson: '',

            addEditingError() {
                if (!this.editingWrong || !this.editingCorrect || !this.editingBoxNumber) return;

                const boxNum = Number(this.editingBoxNumber.trim());

                // Prevent duplicate box numbers
                const boxExists = this.editingErrors.some(err => err.box === boxNum);
                if (boxExists) {
                    alert("Box number already used. Please choose a unique one.");
                    return;
                }

                this.editingErrors.push({
                    box: boxNum,
                    wrong: this.editingWrong.trim(),
                    correct: this.editingCorrect.trim()
                });

                // Clear inputs
                this.editingBoxNumber = '';
                this.editingWrong = '';
                this.editingCorrect = '';

                this.highlightEditingMistakes();
                this.updateEditingJson();
            },

            removeEditingError(index) {
                this.editingErrors.splice(index, 1);
                this.highlightEditingMistakes();
                this.updateEditingJson();
            },

            updateEditingJson() {
                this.editingJson = JSON.stringify({
                    type: 'editing',
                    paragraph: this.questionContent.trim(),
                    questions: this.editingErrors
                }, null, 2);

                const input = document.querySelector('#editingMetadataInput');
                if (input) {
                    input.value = this.editingJson;
                }
            },

            highlightEditingMistakes() {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.quill.root.innerHTML;

                let html = tempDiv.innerHTML;

                this.editingErrors.forEach(error => {
                    const escapedWord = error.wrong.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    const regex = new RegExp(`\\b(${escapedWord})\\b`, 'gi');
                    html = html.replace(regex, '<strong>$1</strong>');
                });

                this.quill.root.innerHTML = html;
                this.questionContent = this.quill.root.innerHTML;
            },
            // === End Editing ===

            instruction: '',
            paragraph: '',
            questions: [],
            json: null,


            parseParagraph() {
                // Get and log Quill editor HTML
                const html = this.quill.root.innerHTML;
                console.log("📌 Quill HTML:", html);

                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = html;

                const text = tempDiv.innerText
                    .replace(/\(\s*(\d+)\s*\)/g, "($1)")
                    .replace(/\[\s*/g, "[")
                    .replace(/\s*\]/g, "]")
                    .replace(/\s*\/\s*/g, " / ");

                console.log("📝 Cleaned Text:", text);

                const inlineRegex = /\((\d+)\)\s*\[([^\[\]]+\/[^\[\]]+)\]/g;
                this.questions = [];

                let match;
                while ((match = inlineRegex.exec(text)) !== null) {
                    const blank_number = parseInt(match[1], 10);
                    const options = match[2].split("/").map(opt => opt.trim());

                    const question = {
                        id: this.questions.length + 1,
                        blank_number,
                        options,
                        correct_answer: "",
                        input_type: "radio"
                    };

                    this.questions.push(question);

                    console.log("✅ Match Found:", match[0]);
                    console.log("➡️ Parsed Question:", question);
                }

                this.paragraph = text;

                this.updateJSON();
                console.log("📦 Final JSON:", this.json);
            },



            updateJSON() {
                this.json = {
                    questions: this.questions.map((q, idx) => ({
                        id: idx + 1,
                        blank_number: q.blank_number,
                        options: q.options,
                        correct_answer: q.correct_answer || null,
                        input_type: "radio"
                    }))
                };

                // Optionally save to hidden input
                const hiddenInput = document.querySelector('#correctUnderlineInput');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(this.json);
                }
            },




            submitForm() {
                this.$root.querySelector('form').submit();
            }
        }
    }
</script>
