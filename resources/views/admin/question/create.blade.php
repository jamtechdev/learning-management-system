{{-- <x-app-layout>
    <div class="max-w-full p-8 mx-auto bg-white rounded-lg shadow-lg" x-data="questionForm()">
        <h1 class="mb-10 text-4xl font-bold text-center text-yellow-600">Create New Question</h1>

        <!-- Step 1: Education Type -->
        <template x-if="step === 1">
            <div class="text-center">
                <h2 class="mb-6 text-2xl font-semibold text-gray-700">Select Education Type</h2>
                <div class="flex justify-center gap-6">
                    <button type="button"
                        class="px-6 py-3 text-lg font-medium transition border rounded-lg hover:bg-yellow-100"
                        :class="educationType === 'primary' ? 'bg-yellow-300' : ''"
                        @click="selectEducationType('primary')">Primary</button>
                    <button type="button"
                        class="px-6 py-3 text-lg font-medium transition border rounded-lg hover:bg-yellow-100"
                        :class="educationType === 'secondary' ? 'bg-yellow-300' : ''"
                        @click="selectEducationType('secondary')">Secondary</button>
                </div>
            </div>
        </template>

        <!-- Step 2: Levels -->
        <template x-if="step === 2">
            <div>
                <h2 class="mb-4 text-2xl font-semibold text-gray-700">Select Level</h2>
                <div class="flex flex-wrap gap-4">
                    <template x-for="level in levels" :key="level.id">
                        <button type="button"
                            class="px-4 py-2 font-medium text-gray-700 transition border rounded-md hover:bg-yellow-100"
                            :class="selectedLevel === level.name ? 'bg-yellow-300' : ''"
                            @click="selectLevel(level.name)" x-text="level.name"></button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 3: Subjects -->
        <template x-if="step === 3">
            <div>
                <h2 class="mb-4 text-2xl font-semibold text-gray-700">Select Subject</h2>
                <div class="flex flex-wrap gap-4">
                    <template x-for="subject in subjects" :key="subject.id">
                        <button type="button"
                            class="px-4 py-2 font-medium text-gray-700 transition border rounded-md hover:bg-yellow-100"
                            :class="selectedSubject?.id === subject.id ? 'bg-yellow-300' : ''"
                            @click="selectSubject(subject)" x-text="subject.name"></button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 4: Question Types -->
        <template x-if="step === 4">
            <div>
                <h2 class="mb-6 text-2xl font-semibold text-gray-700">Select Question Type</h2>
                <div class="flex flex-wrap gap-4">
                    <template x-for="type in questionTypes" :key="type">
                        <button type="button"
                            class="px-4 py-2 text-base font-medium capitalize transition border rounded-md hover:bg-yellow-100"
                            :class="questionType === type ? 'bg-yellow-300' : ''" @click="selectQuestionType(type)"
                            x-text="type.replace('_', ' ')"></button>
                    </template>
                </div>
                <div class="mt-6 text-center">
                    <button type="button" class="text-sm text-gray-500 underline" @click="step = 3">Back</button>
                </div>
            </div>
        </template>

        <!-- Step 5: Question Form -->
        <form method="POST" action="{{ route('admin.questions.store') }}" x-show="step === 5" x-transition
            @submit.prevent="submitForm" class="mt-8 space-y-8">
            @csrf

            <!-- Hidden inputs -->
            <input type="hidden" name="education_type" :value="educationType">
            <input type="hidden" name="level" :value="selectedLevel">
            <input type="hidden" name="subject" :value="selectedSubject">
            <input type="hidden" name="question_type" :value="questionType">

            <!-- Question Content -->
            <div>
                <label for="questionContent" class="block mb-2 text-lg font-medium text-gray-700">Question
                    Content</label>
                <textarea id="questionContent" name="content" rows="4"
                    class="w-full px-4 py-3 text-gray-700 border rounded-md focus:ring-yellow-400 focus:border-yellow-400"
                    x-model="questionContent" x-ref="questionContent" required></textarea>
            </div>

            <!-- Question Type Sections -->
            <!-- MCQ -->
            <template x-if="questionType === 'mcq'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Options (Select the correct answer)</h3>
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center gap-3 mb-2">
                            <input type="radio" :name="'correct_option'" :value="index" class="w-5 h-5"
                                :checked="option.is_correct" @change="setCorrect(index)" required />
                            <input type="text" :name="'options[' + index + ']'" placeholder="Option text"
                                class="flex-grow px-4 py-2 text-gray-700 border rounded-md" x-model="option.value"
                                required />
                            <button type="button" class="text-sm text-red-500 hover:underline"
                                @click="removeOption(index)" x-show="options.length > 2">Remove</button>
                        </div>
                    </template>
                    <button type="button"
                        class="px-4 py-2 mt-2 text-sm font-medium text-yellow-800 bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addOption" x-show="options.length < 6">Add Option</button>
                </div>
            </template>

            <!-- Fill in the Blank -->
            <template x-if="questionType === 'fill_blank'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Fill in the Blank Answers</h3>
                    <template x-for="(answer, idx) in answers" :key="idx">
                        <input type="text" :name="'answers[' + idx + ']'"
                            class="w-full px-4 py-2 mb-2 text-gray-700 border rounded-md" x-model="answers[idx]"
                            required />
                    </template>
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-yellow-800 bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addBlank">Add Blank</button>
                    <p class="mt-2 text-sm italic text-gray-500">Use the "Add Blank" button to insert blanks in the
                        question text.</p>
                </div>
            </template>

            <!-- True / False -->
            <template x-if="questionType === 'true_false'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Select True or False</h3>
                    <select name="true_false_answer" class="w-full px-4 py-2 text-gray-700 border rounded-md"
                        x-model="trueFalseAnswer" required>
                        <option value="True">True</option>
                        <option value="False">False</option>
                    </select>
                </div>
            </template>

            <!-- Linking -->
            <template x-if="questionType === 'linking'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Link the Pairs</h3>
                    <template x-for="(pair, idx) in linkingPairs" :key="idx">
                        <div class="flex gap-4 mb-3">
                            <input type="text" :name="'pairs[' + idx + '][label]'" placeholder="Left item"
                                class="w-1/2 px-4 py-2 border rounded-md" x-model="pair.label" required />
                            <input type="text" :name="'pairs[' + idx + '][value]'" placeholder="Right item"
                                class="w-1/2 px-4 py-2 border rounded-md" x-model="pair.value" required />
                            <button type="button" class="text-sm text-red-500 hover:underline"
                                @click="removePair(idx)" x-show="linkingPairs.length > 2">Remove</button>
                        </div>
                    </template>
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-yellow-800 bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addPair" x-show="linkingPairs.length < 6">Add Pair</button>
                </div>
            </template>

            <!-- Spelling -->
            <template x-if="questionType === 'spelling'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Correct Spelling</h3>
                    <input type="text" name="spelling_answer"
                        class="w-full px-4 py-2 text-gray-700 border rounded-md" x-model="spellingAnswer" required />
                </div>
            </template>

            <!-- Math -->
            <template x-if="questionType === 'math'">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700">Math Answer (LaTeX supported)</h3>
                    <textarea name="math_answer" rows="3" class="w-full px-4 py-2 text-gray-700 border rounded-md"
                        x-model="mathAnswer" placeholder="e.g. \frac{a}{b} + \sqrt{c}" required></textarea>
                </div>
            </template>

            <!-- Submit Section -->
            <div class="flex justify-between pt-6 mt-6 border-t">
                <button type="button" class="text-sm text-gray-600 underline" @click="step = 4">Back</button>
                <button type="submit"
                    class="px-6 py-2 font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">Submit</button>
            </div>
        </form>
    </div>
</x-app-layout> --}}
<x-app-layout>
    <div class="max-w-5xl p-8 mx-auto bg-white shadow-lg rounded-xl" x-data="questionForm()" x-cloak>
        <h1 class="mb-8 text-3xl font-bold text-center text-yellow-600">Create New Question</h1>

        <!-- Progress Bar -->
        <div class="w-full h-2 mb-10 bg-gray-200 rounded-full">
            <div class="h-2 transition-all duration-300 bg-yellow-500 rounded-full"
                :style="`width: ${(step / 5) * 100}%`"></div>
        </div>

        <!-- Step 1: Education Type -->
        <template x-if="step === 1">
            <div class="text-center">
                <h2 class="mb-4 text-xl font-semibold">Select Education Type</h2>
                <div class="flex justify-center gap-6">
                    <template x-for="type in ['primary', 'secondary']">
                        <button type="button"
                            class="px-6 py-3 text-lg font-medium transition border rounded-xl hover:bg-yellow-100"
                            :class="educationType === type ? 'bg-yellow-300 text-yellow-900' : 'text-gray-700'"
                            @click="selectEducationType(type)"
                            x-text="type.charAt(0).toUpperCase() + type.slice(1)"></button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 2: Levels -->
        <template x-if="step === 2">
            <div>
                <h2 class="mb-4 text-xl font-semibold">Select Level</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    <template x-for="level in levels" :key="level.id">
                        <button class="px-4 py-2 transition border rounded-lg hover:bg-yellow-100"
                            :class="selectedLevel === level.name ? 'bg-yellow-300 text-yellow-900' : 'text-gray-700'"
                            @click="selectLevel(level.name)" x-text="level.name"></button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 3: Subjects -->
        <template x-if="step === 3">
            <div>
                <h2 class="mb-4 text-xl font-semibold">Select Subject</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    <template x-for="subject in subjects" :key="subject.id">
                        <button class="px-4 py-2 transition border rounded-lg hover:bg-yellow-100"
                            :class="selectedSubject?.id === subject.id ? 'bg-yellow-300 text-yellow-900' : 'text-gray-700'"
                            @click="selectSubject(subject)" x-text="subject.name"></button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Step 4: Question Type -->
        <template x-if="step === 4">
            <div>
                <h2 class="mb-4 text-xl font-semibold">Select Question Type</h2>
                <div class="flex flex-wrap gap-3">
                    <template x-for="type in questionTypes" :key="type">
                        <button class="px-4 py-2 capitalize transition border rounded-lg hover:bg-yellow-100"
                            :class="questionType === type ? 'bg-yellow-300 text-yellow-900' : 'text-gray-700'"
                            @click="selectQuestionType(type)" x-text="type.replace('_', ' ')"></button>
                    </template>
                </div>
                <div class="mt-6 text-center">
                    <button type="button" class="text-sm text-gray-500 underline" @click="step = 3">Back</button>
                </div>
            </div>
        </template>

        <!-- Step 5: Question Form -->
        <form method="POST" action="{{ route('admin.questions.store') }}" x-show="step === 5" x-transition
            @submit.prevent="submitForm" class="mt-10 space-y-6">
            @csrf

            <!-- Hidden Inputs -->
            <input type="hidden" name="education_type" :value="educationType">
            <input type="hidden" name="level" :value="selectedLevel">
            <input type="hidden" name="subject" :value="selectedSubject?.id">
            <input type="hidden" name="question_type" :value="questionType">

            <!-- Question Content -->
            <div>
                <label class="block mb-2 font-medium text-gray-700">Question Content</label>
                <textarea x-model="questionContent" name="content" rows="3" class="w-full p-3 border rounded-lg"
                    x-ref="questionContent" required></textarea>
            </div>

            <!-- Type Specific Sections -->
            <template x-if="questionType === 'mcq'">
                <div>
                    <label class="block mb-2 font-medium">MCQ Options</label>
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-center gap-3 mb-2">
                            <input type="radio" :name="'correct_option'" :value="index" class="w-5 h-5"
                                @change="setCorrect(index)" :checked="option.is_correct" required>
                            <input type="text" :name="'options[' + index + ']'" x-model="option.value"
                                placeholder="Option text" class="flex-1 p-2 border rounded-lg" required>
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
                        <input type="text" :name="'answers[' + idx + ']'" x-model="answers[idx]"
                            class="w-full p-2 mb-2 border rounded-lg" required>
                    </template>
                    <button type="button" class="px-4 py-1 mt-2 text-sm bg-yellow-200 rounded hover:bg-yellow-300"
                        @click="addBlank">Add Blank</button>
                </div>
            </template>

            <template x-if="questionType === 'true_false'">
                <div>
                    <label class="block mb-2 font-medium">Answer</label>
                    <select name="true_false_answer" class="w-full p-2 border rounded-lg" x-model="trueFalseAnswer"
                        required>
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
                            <input type="text" :name="'pairs[' + idx + '][label]'" x-model="pair.label"
                                class="w-1/2 p-2 border rounded" placeholder="Left" required>
                            <input type="text" :name="'pairs[' + idx + '][value]'" x-model="pair.value"
                                class="w-1/2 p-2 border rounded" placeholder="Right" required>
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
                    <input type="text" name="spelling_answer" x-model="spellingAnswer"
                        class="w-full p-2 border rounded" required>
                </div>
            </template>

            <template x-if="questionType === 'math'">
                <div>
                    <label class="block mb-2 font-medium">Math Answer (LaTeX)</label>
                    <textarea name="math_answer" x-model="mathAnswer" rows="3" class="w-full p-2 border rounded"
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

            options: [{
                    value: '',
                    is_correct: false
                },
                {
                    value: '',
                    is_correct: false
                }
            ],
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
                    this.answers = [''];
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
                if (this.options.length > 2) {
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
