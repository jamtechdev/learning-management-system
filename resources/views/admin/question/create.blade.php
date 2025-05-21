<x-app-layout>
    <div class="max-w-full p-8 mx-auto bg-white border border-gray-100 shadow-xl rounded-2xl" x-data="questionForm()"
        x-cloak>
        <h1 class="mb-8 text-4xl font-bold text-center text-yellow-600">Create New Question</h1>

        <!-- Progress Bar -->
        <div class="w-full h-3 mb-10 overflow-hidden bg-gray-200 rounded-full">
            <div class="h-full transition-all duration-300 bg-yellow-500 rounded-full"
                :style="`width: ${(step / 5) * 100}%`"></div>
        </div>

        <!-- Step 1: Education Type -->
        <template x-if="step === 1">
            <div class="text-center">
                <h2 class="mb-4 text-2xl font-semibold">Select Education Type</h2>
                <div class="flex flex-col justify-center gap-4">
                    <template x-for="type in ['primary', 'secondary']">
                        <button type="button"
                            class="px-6 py-3 text-lg font-medium transition-all duration-200 border-2 rounded-xl hover:bg-yellow-100"
                            :class="educationType === type ? 'bg-yellow-300 text-yellow-900 border-yellow-400 shadow-md' :
                                'text-gray-700 border-gray-300'"
                            @click="selectEducationType(type)" x-text="type.charAt(0).toUpperCase() + type.slice(1)">
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 2: Levels -->
        <template x-if="step === 2">
            <div class="text-center">
                <h2 class="mb-4 text-2xl font-semibold">Select Level</h2>
                <div class="flex flex-col justify-center gap-4">
                    <template x-for="level in levels" :key="level.id">
                        <button
                            class="px-4 py-2 font-medium transition-all duration-200 border-2 rounded-lg hover:bg-yellow-100"
                            :class="selectedLevel === level.name ? 'bg-yellow-300 text-yellow-900 border-yellow-400 shadow' :
                                'text-gray-700 border-gray-300'"
                            @click="selectLevel(level.name)" x-text="level.name">
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 3: Subjects -->
        <template x-if="step === 3">

            <div class="text-center">
                <h2 class="mb-4 text-2xl font-semibold">Select Level</h2>
                <div class="flex flex-col justify-center gap-4">
                    <template x-for="subject in subjects" :key="subject.id">
                        <button
                            class="px-4 py-2 font-medium transition-all duration-200 border-2 rounded-lg hover:bg-yellow-100"
                            :class="selectedSubject?.id === subject.id ?
                                'bg-yellow-300 text-yellow-900 border-yellow-400 shadow' :
                                'text-gray-700 border-gray-300'"
                            @click="selectSubject(subject)" x-text="subject.name">
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 4: Question Type -->
        <template x-if="step === 4">
            <div>
                <h2 class="mb-4 text-2xl font-semibold text-center">Select Question Type</h2>
                <div class="flex flex-col flex-wrap justify-center gap-3">
                    <template x-for="type in questionTypes" :key="type">
                        <button
                            class="px-4 py-2 font-medium capitalize transition-all duration-200 border-2 rounded-lg hover:bg-yellow-100"
                            :class="questionType === type ? 'bg-yellow-300 text-yellow-900 border-yellow-400 shadow' :
                                'text-gray-700 border-gray-300'"
                            @click="selectQuestionType(type)" x-text="type.replace('_', ' ')">
                        </button>
                    </template>
                </div>
                <div class="mt-6 text-center">
                    <button type="button" class="text-sm text-gray-500 underline hover:text-yellow-600"
                        @click="step = 3">← Back</button>
                </div>
            </div>
        </template>

        <!-- Step 5: Question Form -->
        <form method="POST" action="{{ route('admin.questions.store') }}" x-show="step === 5" x-transition
            @submit.prevent="submitForm" class="mt-10 space-y-6">
            @csrf

            <!-- Hidden Inputs -->
            <input type="hidden" name="question_data[education_type]" :value="educationType">
            <input type="hidden" name="question_data[level_id]" :value="selectedLevelId">
            <input type="hidden" name="question_data[subject_id]" :value="selectedSubject?.id">
            <input type="hidden" name="question_data[type]" :value="questionType">

            <!-- Question Content -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Question Content</label>
                <textarea x-model="questionContent" name="question_data[content]" rows="3" class="w-full p-3 border rounded-l"
                    x-ref="questionContent" required></textarea>
            </div>


            <!-- Type Specific Sections -->
            <template x-if="questionType === 'mcq'">
                <div>
                    <label class="block mb-2 font-medium">MCQ Options</label>
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center gap-3 mb-2">
                            <input type="radio" :name="'question_data[correct_option]'" :value="index"
                                class="w-5 h-5" @change="setCorrect(index)" :checked="option.is_correct" required>
                            <input type="text" :name="'question_data[options][' + index + ']'"
                                x-model="option.value" placeholder="Option text" class="flex-1 p-2 border rounded-lg"
                                required>
                            <button type="button" class="text-sm text-red-500" @click="removeOption(index)"
                                x-show="options.length > 2">Remove</button>
                        </div>
                    </template>
                    <button type="button" class="px-4 py-1 mt-2 text-sm bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addOption" x-show="options.length < 6">Add Option</button>
                </div>
            </template>

            <!-- FILL IN THE BLANK TEMPLATE -->
            <template x-if="questionType === 'fill_blank'">
                <div>
                    <template x-for="(blank, index) in blanks" :key="index">
                        <div class="p-4 mb-4 border rounded bg-gray-50">
                            <!-- Hidden input to include blank_number -->
                            <input type="hidden" :name="'question_data[blanks][' + index + '][blank_number]'"
                                :value="blank.blank_number">

                            <p class="mb-2 font-medium">Blank <span x-text="blank.blank_number"></span> Options:</p>

                            <!-- Options -->
                            <template x-for="(option, optIndex) in blank.options" :key="optIndex">
                                <input type="text" class="w-full p-2 mb-2 border rounded"
                                    :name="'question_data[blanks][' + index + '][options][' + optIndex + ']'"
                                    x-model="blank.options[optIndex]" required />
                            </template>

                            <!-- Correct Answer Selection -->
                            <label class="block mt-2 font-medium">Correct Answer:</label>
                            <select class="w-full p-2 border rounded"
                                :name="'question_data[blanks][' + index + '][answer]'" x-model="blank.answer" required>
                                <option value="" disabled>Select correct answer</option>
                                <template x-for="opt in blank.options">
                                    <option x-text="opt" :value="opt"></option>
                                </template>
                            </select>

                            <!-- Remove Button -->
                            <button type="button" class="mt-2 text-red-600" @click="removeBlank(index)">
                                Remove Blank
                            </button>
                        </div>
                    </template>

                    <!-- Add Blank Button -->
                    <button type="button" class="px-4 py-1 mt-2 text-sm bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addBlank">
                        Add Blank
                    </button>
                </div>
            </template>

            {{-- true false  --}}
            <template x-if="questionType === 'true_false'">
                <div>
                    <label class="block mb-2 font-medium">Answer</label>
                    <select name="question_data[true_false_answer]" class="w-full p-2 border rounded-lg"
                        x-model="trueFalseAnswer" required>
                        <option value="True">True</option>
                        <option value="False">False</option>
                    </select>
                </div>
            </template>

            {{-- Linking --}}
            <template x-if="questionType === 'linking'">
                <div>
                    <label class="block mb-2 font-medium">Pairs</label>
                    <template x-for="(pair, idx) in linkingPairs" :key="idx">
                        <div class="flex gap-3 mb-2">
                            <input type="text" :name="'question_data[pairs][' + idx + '][label]'"
                                x-model="pair.label" class="w-1/2 p-2 border rounded" placeholder="Left" required>
                            <input type="text" :name="'question_data[pairs][' + idx + '][value]'"
                                x-model="pair.value" class="w-1/2 p-2 border rounded" placeholder="Right" required>
                            <button type="button" class="text-sm text-red-500" @click="removePair(idx)"
                                x-show="linkingPairs.length > 2">Remove</button>
                        </div>
                    </template>
                    <button type="button" class="px-4 py-1 mt-2 text-sm bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addPair" x-show="linkingPairs.length < 6">Add Pair</button>
                </div>
            </template>
            <div>
                <label class="block mb-2 font-medium text-gray-700">Explanation</label>
                <textarea x-model="explanation" name="question_data[explanation]" rows="3" class="w-full p-3 border rounded-l"
                    x-ref="explanation" required></textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between pt-4 mt-6 border-t">
                <button type="button" class="text-sm text-gray-500 underline" @click="step = 4">Back</button>
                <button type="submit"
                    class="px-6 py-2 font-semibold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600">Submit</button>
            </div>
        </form>

    </div>
</x-app-layout>

<script>
    function questionForm() {
        return {
            step: 1,
            selectedLevel: '',
            educationType: '',
            selectedLevelId: '',
            levelsByType: @json($levels),
            levels: [],
            subjects: [],
            selectedSubject: null,

            questionTypes: ['mcq', 'fill_blank', 'true_false', 'linking'],
            questionType: '',
            questionContent: '',

            blanks: [],

            // Other question type data
            trueFalseAnswer: 'True',
            linkingPairs: [{
                label: '',
                value: ''
            }, {
                label: '',
                value: ''
            }],
            spellingAnswer: '',
            mathAnswer: '',

            selectEducationType(type) {
                this.educationType = type;
                this.levels = this.levelsByType[type] || [];
                this.step = 2;
                this.selectedLevel = '';
                this.selectedSubject = null;
                this.subjects = [];
            },

            selectLevel(levelName) {
                const foundLevel = this.levels.find(l => l.name === levelName);
                if (foundLevel) {
                    this.selectedLevel = foundLevel.name;
                    this.selectedLevelId = foundLevel.id;
                    this.subjects = foundLevel.subjects || [];
                    this.step = 3;
                }
            },

            selectSubject(subject) {
                this.selectedSubject = subject;
                this.step = 4;
            },

            selectQuestionType(type) {
                this.questionType = type;
                if (type === 'mcq') {
                    this.options = [{
                        value: '',
                        is_correct: false
                    }, {
                        value: '',
                        is_correct: false
                    }];
                } else if (type === 'fill_blank') {
                    this.questionContent = '';
                    this.blanks = [];
                } else if (type === 'true_false') {
                    this.trueFalseAnswer = 'True';
                } else if (type === 'linking') {
                    this.linkingPairs = [{
                        label: '',
                        value: ''
                    }, {
                        label: '',
                        value: ''
                    }];
                } else if (type === 'spelling') {
                    this.spellingAnswer = '';
                } else if (type === 'math') {
                    this.mathAnswer = '';
                }
                this.step = 5;
            },

            addOption() {
                if (this.options.length < 4) {
                    this.options.push({
                        value: '',
                        is_correct: false
                    });
                }
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
            addBlank() {
                const textarea = this.$refs.questionContent;
                if (!textarea) return;

                const blankNumber = this.blanks.length + 1;
                const insertText = `${blankNumber}. _____`;

                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;

                this.questionContent =
                    this.questionContent.substring(0, start) +
                    insertText +
                    this.questionContent.substring(end);

                this.$nextTick(() => {
                    textarea.focus();
                    textarea.selectionStart = textarea.selectionEnd = start + insertText.length;
                });

                this.blanks.push({
                    blank_number: blankNumber,
                    options: ['', '', '', ''],
                    answer: ''
                });
            },

            removeBlank(index) {
                const removed = this.blanks[index];
                const regex = new RegExp(`\\b${removed.blank_number}\\. _____\\b`);
                this.questionContent = this.questionContent.replace(regex, '').replace(/\s+/, ' ');

                this.blanks.splice(index, 1);

                // Re-number blanks and update passage
                this.blanks.forEach((b, i) => {
                    const oldNumber = b.blank_number;
                    const newNumber = i + 1;
                    if (oldNumber !== newNumber) {
                        const re = new RegExp(`\\b${oldNumber}\\. _____\\b`);
                        this.questionContent = this.questionContent.replace(re, `${newNumber}. _____`);
                        b.blank_number = newNumber;
                    }
                });
            },

            addPair() {
                if (this.linkingPairs.length < 6) {
                    this.linkingPairs.push({
                        label: '',
                        value: ''
                    });
                }
            },

            removePair(index) {
                if (this.linkingPairs.length > 2) {
                    this.linkingPairs.splice(index, 1);
                }
            },

            submitForm() {
                // Optional: console.log to debug
                console.log(JSON.stringify(this.blanks, null, 2));
                this.$root.querySelector('form').submit();
            }
        }
    }
</script>
