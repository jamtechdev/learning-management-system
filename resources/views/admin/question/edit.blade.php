<x-app-layout>
    <div class="max-w-7xl p-10 mx-auto bg-white border border-gray-200 shadow-[0_8px_30px_rgba(55,55,55,0.1)] rounded-3xl"
        style="font-family: 'Inter', sans-serif;">
        <h1 class="mb-10 text-3xl font-bold text-center text-black drop-shadow-[0_8px_30px_rgba(55,55,55,0.1)]">
            Edit Question
        </h1>
        <form method="POST" x-data="questionForm()" action="{{ route('admin.questions.update', $question->id) }}"
            enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Education Type (readonly) -->
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Education Type</label>
                <input type="text" name="question_data[education_type]"
                    value="{{ ucfirst($question->education_type) }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
            </div>
            <!-- Level (readonly + hidden input) -->
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Level</label>
                <input type="text" value="{{ $question->level->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[level_id]" value="{{ $question->level->id ?? '' }}" />
            </div>
            <!-- Subject (readonly + hidden input) -->
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Subject</label>
                <input type="text" value="{{ $question->subject->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[subject_id]" value="{{ $question->subject->id ?? '' }}" />
            </div>
            <!-- Topic (readonly + hidden input) -->
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Topic</label>
                <input type="text" value="{{ $question->topic->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[topic_id]" value="{{ $question->topic->id ?? '' }}" />
            </div>
            <!-- Hidden Question Type -->
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Type</label>
                <input type="text" value="{{ $question->type ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[type]" value="{{ $question->type }}" />
            </div>


            <div>
                <label class="block text-lg font-semibold text-blue-700">Question Instruction</label>
                <input type="text" name="question_data[instruction]"
                    class="w-full p-3 border rounded-lg focus:ring-4 focus:ring-blue-200"
                    placeholder="e.g., Let me help you." required value="{{ $question->metadata['instruction'] }}">

            </div>

            <!-- Type-specific inputs -->

            <!-- Question Content -->
            <div>
                <div x-ref="questionContentEditor"
                    class="border-2 border-blue-300 shadow-sm quill-editor focus:ring-4 focus:ring-blue-200"
                    style="min-height: 150px;">
                </div>
                <!-- Moved input outside the Quill container -->
                <input type="hidden" name="question_data[content]" :value="questionContent" x-model="questionContent">
            </div>
            @if ($question->type === \App\Enum\QuestionTypes::MCQ)
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

                    <!-- MCQ Option Loop -->
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex flex-col mb-4">
                            <div class="flex items-center gap-3">
                                <input type="radio" :name="'question_data[correct_option]'" :value="index"
                                    class="w-5 h-5 text-blue-600" @change="setCorrect(index)"
                                    :checked="option.is_correct" required />

                                <!-- Option Text -->
                                <input type="text" :name="'question_data[options][' + index + '][value]'"
                                    x-model="option.value" class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Option text" required />

                                <!-- Remove Button -->
                                <button type="button" @click="removeOption(index)"
                                    class="text-red-600 hover:text-red-800" x-show="options.length > 1">✕</button>
                            </div>

                            <!-- Explanation (always visible to admin/teacher) -->
                            <input type="text" :name="'question_data[options][' + index + '][explanation]'"
                                x-model="option.explanation"
                                class="w-full px-3 py-2 mt-2 text-sm border border-blue-300 rounded"
                                placeholder="Explanation for this option (optional)" />
                        </div>
                    </template>

                    <!-- Add Option + Submit -->
                    <div class="flex items-center justify-between mt-4">
                        <button type="button" @click="addOption"
                            class="px-4 py-2 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                            + Add Option
                        </button>
                    </div>
                </div>
            @elseif ($question->type === \App\Enum\QuestionTypes::FILL_IN_THE_BLANK)
                <div class="p-6 bg-white border shadow rounded-xl">
                    <h2 class="mb-4 text-lg font-semibold text-blue-700">📝 Fill in the Blank Builder</h2>

                    <div class="flex gap-4 mb-4">
                        <button type="button" @click="insertBlank()"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                            + Insert Blank
                        </button>
                    </div>

                    <!-- Blanks with answer inputs -->
                    <template x-for="(blank, index) in blanks" :key="blank.blank_number">
                        <div
                            class="flex flex-col gap-4 p-4 mb-4 border rounded md:flex-row md:items-center md:justify-between bg-gray-50">
                            <div><strong>Blank #</strong>: <span x-text="blank.blank_number"></span></div>
                            <div>
                                <input type="text" x-model="blank.correct_answer" placeholder="Correct Answer"
                                    class="px-3 py-2 border rounded" @input="updateJsonOutput()" />
                            </div>
                            <button @click="removeBlank(index)" class="text-red-600 hover:underline">Remove</button>
                        </div>
                    </template>

                    <input type="hidden" name="question_data[fill_in_the_blank_metadata]" :value="jsonOutput" />

                </div>
            @elseif ($question->type === \App\Enum\QuestionTypes::TRUE_FALSE)
                <div>
                    <label class="block mb-3 font-semibold text-gray-700">Answer</label>
                    <select name="question_data[true_false_answer]" required
                        class="w-full p-3 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200">
                        <option value="True"
                            {{ old('question_data.true_false_answer', $question->metadata['answer']['choice'] ?? '') == 'True' ? 'selected' : '' }}>
                            True</option>
                        <option value="False"
                            {{ old('question_data.true_false_answer', $question->metadata['answer']['choice'] ?? '') == 'False' ? 'selected' : '' }}>
                            False</option>
                    </select>
                </div>
            @elseif ($question->type === \App\Enum\QuestionTypes::LINKING)
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
                                            <input type="radio" selectQuestionType
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
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::REARRANGING)
                <div x-data="rearrangingForm()" x-init="init()" class="space-y-6">
                    <!-- Instruction Field -->


                    <!-- Correct Sentence Input -->
                    <div>
                        <label class="block text-lg font-semibold text-blue-700">Correct Sentence</label>
                        <input type="text" name="question_data[question_text]" x-model="sentence"
                            class="w-full p-3 border rounded-lg focus:ring-4 focus:ring-blue-200"
                            placeholder="e.g., Let me help you." required>
                    </div>

                    <!-- Auto-generated word inputs -->
                    <div>
                        <label class="block text-lg font-semibold text-blue-700">Correct Word Order</label>
                        <div class="flex flex-wrap items-center gap-3">
                            <template x-for="(word, index) in wordList" :key="index">
                                <input type="text" class="p-2 border rounded-md"
                                    :name="'question_data[rearranging][answer][' + index + ']'"
                                    x-model="wordList[index]" placeholder="Word or phrase" required>
                            </template>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <template x-if="wordList.length > 1">
                        <div class="mt-3">
                            <label class="text-sm text-gray-600">Preview:</label>
                            <div class="p-3 mt-1 bg-gray-100 rounded-lg">
                                <span x-text="wordList.join(' ')"></span>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::EDITING)
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
                            <template x-for="(item, idx) in editingErrors" :key="idx">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <input type="number" x-model="item.box" class="w-1/6 p-2 border rounded"
                                        placeholder="Box #" />
                                    <input type="text" x-model="item.wrong" class="w-1/4 p-2 border rounded"
                                        placeholder="Misspelled Word" />
                                    <input type="text" x-model="item.correct" class="w-1/4 p-2 border rounded"
                                        placeholder="Correct Word" />
                                    <button type="button" @click="removeEditingError(idx)"
                                        class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">Remove</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <input type="hidden" id="editingMetadataInput" name="question_data[metadata]"
                        x-model="formattedJson" />
                </div>
            @endif

            @if ($question->type === \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_OPTIONS)
                <div class="p-6 space-y-6 bg-white border rounded shadow">

                    <!-- Insert Blank Button -->
                    <div>
                        <button type="button" @click="addGrammarBlank"
                            class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded hover:bg-green-700">
                            ➕ Insert Blank
                        </button>
                    </div>

                    <!-- Shared Options Input -->
                    <div>
                        <label class="block mb-2 font-semibold text-gray-700">Shared Options</label>
                        <input type="text" x-model="sharedOptionsRaw" @input="updateGrammarClozeJson"
                            class="w-full px-3 py-2 border rounded" placeholder="Enter options separated by commas ,">
                    </div>
                    <input type="hidden" name="question_data[metadata]" id="metadataInput"
                        x-model="formattedJson" />
                    <!-- Detected Blanks -->
                    <template x-if="questions.length">
                        <div>
                            <h2 class="mb-4 text-xl font-bold text-gray-800">Detected Blanks</h2>
                            <template x-for="(q, idx) in questions" :key="q.id">
                                <div
                                    class="flex flex-col gap-4 p-4 mb-4 border rounded md:flex-row md:items-center md:justify-between bg-gray-50">
                                    <!-- Editable blank number -->
                                    <div class="flex items-center gap-2">
                                        <label class="font-semibold text-gray-700">Blank #</label>
                                        <input type="text" x-model="q.blank_number"
                                            @input="updateGrammarClozeJson"
                                            class="w-24 px-2 py-1 text-center border rounded" />
                                    </div>

                                    <!-- Correct Answer -->
                                    <div>
                                        <input type="text" x-model="q.correct_answer" placeholder="Correct Answer"
                                            class="w-full px-3 py-2 border rounded" @input="updateGrammarClozeJson">
                                    </div>

                                    <!-- Remove Button -->
                                    <button @click="removeQuestion(idx)"
                                        class="text-red-600 hover:underline whitespace-nowrap">
                                        ❌ Remove
                                    </button>
                                </div>
                            </template>
                        </div>

                    </template>
                </div>
            @endif

            @if ($question->type === \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS)
                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl">

                    <!-- Insert Blank Button -->
                    <button type="button" @click="insertDropdownBlank"
                        class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                        ➕ Insert Dropdown Blank
                    </button>

                    <!-- Generate Button -->
                    <button type="button" @click="parseDropdownOptions"
                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                        🔍 Generate Dropdown Questions
                    </button>
                    <input type="hidden" name="question_data[underline_metadata]" id="correctUnderlineInput"
                        x-model="formattedJson" />
                    <!-- Questions Section -->
                    <div x-show="questions.length" class="p-4 mt-4 border rounded bg-gray-50">
                        <h2 class="mb-3 font-semibold text-gray-700">Enter Correct Answers</h2>

                        <template x-for="(q, index) in questions" :key="index">
                            <div
                                class="grid items-center grid-cols-1 gap-4 p-4 mb-4 bg-white border rounded md:grid-cols-2">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">
                                        Blank Number
                                    </label>
                                    <input type="text"
                                        class="w-full px-3 py-2 text-gray-600 bg-gray-100 border rounded"
                                        :value="q.blank_number" readonly />
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-700">
                                        Correct Answer
                                    </label>
                                    <select x-model="q.correct_answer" @change="updateDropdownJson"
                                        class="w-full px-3 py-2 border rounded">
                                        <option value="" disabled>Select correct answer</option>
                                        <template x-for="opt in q.options">
                                            <option :value="opt" x-text="opt"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            @endif

            @if ($question->type === \App\Enum\QuestionTypes::COMPREHENSION)
                <div x-effect="updateComprehensionJson()"></div>
                <div class="mt-4 space-y-6">
                    <!-- Passage Display -->
                    <div class="p-4 bg-white rounded shadow">
                        <h2 class="mb-2 text-lg font-bold">Passage</h2>
                        <div class="min-h-[160px] border border-gray-300 p-4 rounded-lg focus-within:ring-2 focus-within:ring-blue-400"
                            x-html="questionContent"></div>
                    </div>

                    <!-- Add Question Type -->
                    <div class="p-4 space-y-4 bg-white rounded shadow">
                        <h3 class="font-semibold text-md">Add a Question</h3>

                        <select x-model="selectedComprehensionType" class="w-full p-2 border rounded">
                            <option value="">Select Question Type</option>
                            <option value="mcq">Multiple Choice (MCQ)</option>
                            <option value="true_false">True / False</option>
                            <option value="fill_blank">Fill in the Blank</option>
                            <option value="open_ended">Open-ended (Text Answer)</option>
                        </select>

                        <button type="button" @click="addComprehensionQuestion()"
                            class="px-4 py-2 text-white bg-blue-500 rounded">Add Question</button>
                    </div>
                    <input type="hidden" id="comprehensionMetadataInput"
                        name="question_data[comprehension_metadata]" x-model="formattedJson" />
                    <!-- Render Questions -->
                    <template x-for="(question, index) in comprehensionQuestions" :key="index">
                        <div class="p-4 space-y-2 bg-white rounded shadow">
                            <h4 class="font-semibold">Question <span x-text="index + 1"></span> (<span
                                    x-text="formatQuestionType(question.type)"></span>)</h4>


                            <!-- MCQ Section -->
                            <div x-show="question.type === 'mcq'" class="space-y-3">
                                <input type="text" x-model="question.question" placeholder="Enter MCQ Question"
                                    class="w-full p-2 border rounded" />

                                <!-- Option Inputs -->
                                <template x-for="(opt, optIndex) in question.options" :key="optIndex">
                                    <div class="flex items-center gap-2">
                                        <input type="text" x-model="question.options[optIndex]"
                                            class="flex-1 p-2 border rounded"
                                            :placeholder="'Option ' + (optIndex + 1)" />
                                        <button type="button" class="text-red-600 hover:text-red-800"
                                            @click="question.options.splice(optIndex, 1)">🗑</button>
                                    </div>
                                </template>

                                <!-- Add Option -->
                                <button type="button" @click="question.options.push('')"
                                    class="px-3 py-1 text-sm text-white bg-gray-600 rounded hover:bg-gray-700">
                                    ➕ Add Option
                                </button>

                                <!-- Correct Answer Dropdown -->
                                <label class="block mt-3 font-semibold text-gray-600">Correct Answer</label>
                                <select x-model="question.answer" class="w-full p-2 border rounded">
                                    <option value="">-- Select Correct Option --</option>
                                    <template x-for="(opt, i) in question.options" :key="i">
                                        <option :value="opt" x-text="'Option ' + (i + 1) + ': ' + opt">
                                        </option>
                                    </template>
                                </select>
                            </div>


                            <!-- True/False Section -->
                            <div x-show="question.type === 'true_false'" class="space-y-2">
                                <input type="text" x-model="question.question"
                                    placeholder="Enter True/False Statement" class="w-full p-2 border rounded">
                                <label class="block font-semibold">Expected Answer</label>
                                <select x-model="question.answer" class="w-full p-2 border rounded">
                                    <option value="">Select Answer</option>
                                    <option value="True">True</option>
                                    <option value="False">False</option>
                                </select>
                            </div>

                            <!-- Fill Blank Section -->
                            <div x-show="question.type === 'fill_blank'" class="space-y-2">
                                <input type="text" x-model="question.question"
                                    placeholder="Enter sentence with ___ for blank" class="w-full p-2 border rounded">
                                <label class="block font-semibold">Expected Answer</label>
                                <input type="text" x-model="question.answer" placeholder="Correct Answer"
                                    class="w-full p-2 border rounded">
                            </div>

                            <!-- Open Ended Section -->
                            <div x-show="question.type === 'open_ended'" class="space-y-2">
                                <input type="text" x-model="question.question"
                                    placeholder="Enter Open-ended Question" class="w-full p-2 border rounded">
                                <label class="block font-semibold">Answer</label>
                                <input type="text" x-model="question.answer" placeholder="Enter Answer"
                                    class="w-full p-2 border rounded">
                            </div>

                            <button @click="removeComprehensionQuestion(index)"
                                class="mt-2 text-red-500">Remove</button>
                        </div>
                    </template>

                </div>
            @endif
            <div class="flex justify-end mt-8">
                <button type="submit" class="px-10 py-3 text-lg font-extrabold text-white transition add-btn">
                    Update Question
                </button>
            </div>
        </form>
    </div>
    {{-- @dd($question->metadata['subquestions']) --}}
</x-app-layout>
<script>
    function questionForm() {
        return {
            quill: null,
            questionContent: @json(old('question_data.content', $question->content)),
            questions: @json($question->metadata['questions'] ?? []),
            sharedOptionsRaw: `{{ implode(',', $question->metadata['question_group']['shared_options'] ?? []) ?? '' }}`,
            formattedJson: @json(json_encode($question->metadata) ?? '{}'),
            blankCounter: 12,
            blanks: @json($question->metadata['blanks'] ?? []),
            jsonOutput: '',

            options: @json($question->metadata['options'] ?? []),
            hasInsertedBlank: true,

            init() {
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': 1
                    }, {
                        'header': 2
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
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
                        'header': [1, 2, 3, 4, 5, 6, false]
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
                    ['link', 'image', 'video'],
                    ['clean']
                ];

                this.quill = new Quill(this.$refs.questionContentEditor, {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                this.quill.root.innerHTML = this.questionContent;

                this.quill.on('text-change', () => {
                    this.questionContent = this.quill.root.innerHTML;
                    this.updateBlanks();
                });
                this.highlightEditingMistakes();
                this.updateEditingJson();
            },

            parseGrammarCloze() {
                if (!this.questionContent) return;

                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.questionContent;
                const rawText = tempDiv.innerText.trim();

                const blankRegex = /\((\d+)\)\s*_{4,}/g;
                const matches = [];
                let match;
                while ((match = blankRegex.exec(rawText)) !== null) {
                    const blankNumber = parseInt(match[1]);

                    // 👇 Try to find existing question with this blank_number
                    const existing = this.questions.find(q => q.blank_number == blankNumber);

                    matches.push({
                        id: matches.length + 1,
                        blank_number: blankNumber,
                        correct_answer: existing ? existing.correct_answer : '',
                        input_type: existing ? existing.input_type : 'input'
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
                    question_type: '{{ \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_OPTIONS }}',
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

            addGrammarBlank() {
                const nextBlank = this.questions.length + 1;

                const insertText = ` (${nextBlank})_____ `;
                let range = this.quill.getSelection(true);
                const editorLength = this.quill.getLength();
                const insertIndex =
                    range && typeof range.index === 'number' && range.index >= 0 && range.index <= editorLength ?
                    range.index :
                    editorLength;

                this.quill.insertText(insertIndex, insertText, 'user');
                this.quill.setSelection(insertIndex + insertText.length, 0);
                this.questionContent = this.quill.root.innerHTML;

                // Refresh blanks
                this.parseGrammarCloze();
            },

            //start with dropdown

            insertDropdownBlank() {
                const editorText = this.quill.getText();
                const matches = [...editorText.matchAll(/\((\d+)\)\[[^\[\]]+\]/g)];
                const usedNumbers = matches.map(m => parseInt(m[1])).sort((a, b) => a - b);

                let nextBlankNumber = 1;
                while (usedNumbers.includes(nextBlankNumber)) {
                    nextBlankNumber++;
                }

                const insertText = ` (${nextBlankNumber})[option1/option2] `;
                let range = this.quill.getSelection(true);
                const editorLength = this.quill.getLength();
                const insertIndex =
                    range && typeof range.index === 'number' && range.index >= 0 && range.index <= editorLength ?
                    range.index :
                    editorLength;

                this.quill.insertText(insertIndex, insertText, 'user');
                this.quill.setSelection(insertIndex + insertText.length, 0);
                this.questionContent = this.quill.root.innerHTML;
            },

            parseDropdownOptions() {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = this.quill.root.innerHTML;
                const text = tempDiv.innerText
                    .replace(/\(\s*(\d+)\s*\)/g, "($1)")
                    .replace(/\[\s*/g, "[")
                    .replace(/\s*\]/g, "]")
                    .replace(/\s*\/\s*/g, "/");

                const regex = /\((\d+)\)\[([^\[\]]+\/[^\[\]]+)\]/g;
                const parsed = [];

                let match;
                while ((match = regex.exec(text)) !== null) {
                    const blankNumber = parseInt(match[1]);
                    const options = match[2].split("/").map(opt => opt.trim());

                    // Preserve old correct answer if it matches the blank_number
                    const existing = this.questions.find(q => q.blank_number === blankNumber);

                    parsed.push({
                        id: parsed.length + 1,
                        blank_number: blankNumber,
                        options,
                        correct_answer: existing?.correct_answer ?? '',
                        input_type: "dropdown"
                    });
                }

                this.questions = parsed;
                this.updateDropdownJson();
            },


            updateDropdownJson() {
                const output = {
                    question_type: 'dropdown_cloze',
                    paragraph: this.quill.root.innerText.trim(),
                    questions: this.questions.map((q, idx) => ({
                        id: idx + 1,
                        blank_number: q.blank_number,
                        options: q.options,
                        correct_answer: q.correct_answer || null,
                        input_type: "dropdown"
                    }))
                };

                this.formattedJson = JSON.stringify(output, null, 2);

                const hiddenInput = document.querySelector('#correctUnderlineInput');
                if (hiddenInput) {
                    hiddenInput.value = this.formattedJson;
                }
            },

            // Fill in blank
            insertBlank(customText = 'no') {
                const blankText = ` (${customText})_____`;
                let range = this.quill.getSelection(true);
                const editorLength = this.quill.getLength();
                let insertIndex = range && typeof range.index === 'number' && range.index >= 0 && range.index <=
                    editorLength ? range.index : editorLength;
                this.quill.insertText(insertIndex, blankText, 'user');
                this.quill.setSelection(insertIndex + blankText.length, 0);
                this.questionContent = this.quill.root.innerHTML;
                this.updateBlanks();
            },

            updateBlanks() {
                const oldBlanks = this.blanks.slice();
                this.blanks = [];
                const text = this.quill.getText();
                const regex = /\(([^)]+)\)_____/g;
                let match;
                while ((match = regex.exec(text)) !== null) {
                    const blankNumber = match[1];
                    // Check if blank already exists to preserve answer
                    const existingBlank = oldBlanks.find(b => b.blank_number === blankNumber);
                    if (existingBlank) {
                        this.blanks.push(existingBlank);
                    } else {
                        this.blanks.push({
                            blank_number: blankNumber,
                            correct_answer: '',
                            input_type: 'input'
                        });
                    }
                }
                this.updateJsonOutput();
            },

            // New watcher to update blanks in real-time when parentheses content changes
            watchBlanks() {
                this.quill.on('text-change', () => {
                    this.updateBlanks();
                });
            },

            updateJsonOutput() {

                const payload = this.blanks.map(blank => ({
                    blank_number: blank.blank_number,
                    correct_answer: blank.correct_answer,
                    input_type: 'input'
                }));
                this.jsonOutput = JSON.stringify(payload, null, 2);
            },

            removeBlank(index) {
                const blankNumber = this.blanks[index].blank_number;
                this.blanks.splice(index, 1);

                // Remove first occurrence of (blankNumber)_____ from editor content
                const editorText = this.quill.getText();
                const placeholder = `(${blankNumber})_____`;
                const pos = editorText.indexOf(placeholder);
                if (pos !== -1) {
                    this.quill.deleteText(pos, placeholder.length, 'user');
                }

                this.updateJsonOutput();
            },

            //mcq question type


            //mcq start js
            insertMCQBlank() {
                const blankNumber = (this.questionContent.match(/_____/g) || []).length + 1;
                const insertText = ` _____ `;

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
                        is_correct: false,
                        explanation: ''
                    },
                    {
                        value: '',
                        is_correct: false,
                        explanation: ''
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
                    is_correct: false,
                    explanation: ''
                });
            },

            setCorrect(index) {
                this.options.forEach((opt, i) => {
                    opt.is_correct = (i === index);
                });
            },

            removeOption(index) {
                if (this.options.length > 1) {
                    this.options.splice(index, 1);
                }
            },
            // edit linking
            linkingOptions: (() => {
                const answer = @json($question->metadata['answer'] ?? []);
                return Array.isArray(answer) ? answer.map(item => ({
                    label_type: item.left.match_type,
                    label_text: item.left.match_type === 'text' ? item.left.word : '',
                    label_image: item.left.match_type === 'image' ? item.left.image_uri : null,
                    label_preview: item.left.match_type === 'image' ? item.left.image_uri : '',
                    value_type: item.right.match_type,
                    value_text: item.right.match_type === 'text' ? item.right.word : '',
                    value_image: item.right.match_type === 'image' ? item.right.image_uri : null,
                    value_preview: item.right.match_type === 'image' ? item.right.image_uri : '',
                })) : [];
            })(),

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
            formatQuestionType(type) {
                return type
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },
            // end linking

            // === Editing Question Specific States ===
            editingParagraph: '',
            editingBoxNumber: '', // <--- NEW: Manual box number input
            editingWrong: '',
            editingCorrect: '',
            editingErrors: @json($question->metadata['questions'] ?? []),
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
                    if (!error.wrong || typeof error.wrong !== 'string') return;

                    const escapedWord = error.wrong.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    const regex = new RegExp(`\\b(${escapedWord})\\b`, 'gi');
                    html = html.replace(regex, '<strong>$1</strong>');
                });

                this.quill.root.innerHTML = html;
                this.questionContent = this.quill.root.innerHTML;
            },
            // end editing

            // ==== START: Comprehension Question Logic ====
            selectedComprehensionType: '',
            comprehensionQuestions: @json($question->metadata['subquestions'] ?? []),

            addComprehensionQuestion() {
                if (!this.selectedComprehensionType) return;

                const newQuestion = {
                    type: this.selectedComprehensionType,
                    question: '',
                    options: this.selectedComprehensionType === 'mcq' ? ['', ''] : [],
                    answer: ''
                };

                this.comprehensionQuestions.push(newQuestion);
                this.selectedComprehensionType = '';
            },

            removeComprehensionQuestion(index) {
                this.comprehensionQuestions.splice(index, 1);
            },

            updateComprehensionJson() {
                const output = {
                    passage: this.questionContent.trim(),
                    subquestions: []
                };

                this.comprehensionQuestions.forEach((q) => {
                    const subQ = {
                        type: q.type,
                        question: q.question.trim(),
                        answer: q.answer ? q.answer.trim() : ''
                    };

                    if (['mcq', 'fill_blank'].includes(q.type) && Array.isArray(q.options)) {
                        subQ.options = q.options.map(opt => opt.trim());
                    }

                    output.subquestions.push(subQ);
                });

                this.comprehensionJson = output;

                const hiddenInput = document.querySelector('#comprehensionMetadataInput');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(this.comprehensionJson);
                }
            },
            // ==== END: Comprehension Question Logic ====


        };
    }

    function rearrangingForm() {
        return {
            instruction: @json($question->metadata['instruction'] ?? ''),
            sentence: @json(implode(' ', $question->metadata['answer']['answer'] ?? [])),
            wordList: @json($question->metadata['answer']['answer'] ?? []),

            init() {
                this.$watch('sentence', (val) => {
                    this.wordList = val
                        .replace(/[.,!?]/g, '')
                        .trim()
                        .split(/\s+/)
                        .filter(Boolean);
                });
            }
        };
    }
</script>
