<x-app-layout>
    <div class="max-w-5xl p-8 mx-auto bg-white border border-gray-100 shadow-xl rounded-2xl" x-data="questionForm()"
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
                <div class="flex justify-center gap-4">
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
            <div>
                <h2 class="mb-4 text-2xl font-semibold text-center">Select Level</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
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
            <div>
                <h2 class="mb-4 text-2xl font-semibold text-center">Select Subject</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
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
                <div class="flex flex-wrap justify-center gap-3">
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
            <input type="hidden" name="question_data[level]" :value="selectedLevel">
            <input type="hidden" name="question_data[subject]" :value="selectedSubject?.id">
            <input type="hidden" name="question_data[type]" :value="questionType">

            <!-- Question Content -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Question Content</label>
                <textarea x-model="questionContent" name="question_data[content]" rows="3" class="w-full p-3 border rounded-lg"
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

            <template x-if="questionType === 'fill_blank'">
                <div>
                    <label class="block mb-2 font-medium">Blank Answers</label>
                    <template x-for="(answer, idx) in answers" :key="idx">
                        <input type="text" :name="'question_data[answers][' + idx + ']'" x-model="answers[idx]"
                            class="w-full p-2 mb-2 border rounded-lg" required>
                    </template>
                    <button type="button" class="px-4 py-1 mt-2 text-sm bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addBlank">Add Blank</button>
                </div>
            </template>

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

            <template x-if="questionType === 'spelling'">
                <div>
                    <label class="block mb-2 font-medium">Correct Spelling</label>
                    <input type="text" name="question_data[spelling_answer]" x-model="spellingAnswer"
                        class="w-full p-2 border rounded" required>
                </div>
            </template>

            <template x-if="questionType === 'math'">
                <div>
                    <label class="block mb-2 font-medium">Math Answer (LaTeX)</label>
                    <textarea name="question_data[math_answer]" x-model="mathAnswer" rows="3" class="w-full p-2 border rounded"
                        placeholder="e.g. \frac{a}{b} + \sqrt{c}" required></textarea>
                </div>
            </template>

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
            levelsByType: @json($levels),
            levels: [],
            subjects: [],
            selectedSubject: null,

            questionTypes: ['mcq', 'fill_blank', 'true_false', 'linking', 'spelling', 'math'],
            questionType: '',
            questionContent: '',
            answers: [],
            trueFalseAnswer: 'True',
            linkingPairs: [{
                    label: '',
                    value: ''
                },
                {
                    label: '',
                    value: ''
                },
            ],
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
                this.selectedLevel = levelName;
                const foundLevel = this.levels.find(l => l.name === levelName);
                this.subjects = foundLevel?.subjects || [];
                this.step = 3;
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
                        },
                        {
                            value: '',
                            is_correct: false
                        }
                    ];
                } else if (type === 'fill_blank') {
                    this.answers = [];
                } else if (type === 'true_false') {
                    this.trueFalseAnswer = 'True';
                } else if (type === 'linking') {
                    this.linkingPairs = [{
                            label: '',
                            value: ''
                        },
                        {
                            label: '',
                            value: ''
                        }
                    ];
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
                let textarea = this.$refs.questionContent;
                if (!textarea) return;

                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;

                this.questionContent = this.questionContent.substring(0, start) +
                    ' _____ ' +
                    this.questionContent.substring(end);

                this.$nextTick(() => {
                    textarea.selectionStart = textarea.selectionEnd = start + 6;
                    textarea.focus();
                });

                this.answers.push('');
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
                this.$root.querySelector('form').submit();
            }
        }
    }
</script>
