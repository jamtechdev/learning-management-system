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
                <div>
                    <label class="block mb-3 font-semibold text-gray-800">MCQ Options</label>
                    @foreach ($question->options as $index => $option)
                        <div class="flex items-center gap-4 p-3 mb-4 border-2 bg-gray-50 rounded-xl">
                            <input type="radio" name="question_data[correct_option]" value="{{ $index }}"
                                {{ old('question_data.correct_option', $option->is_correct) === 1 ? 'checked' : '' }}
                                required class="w-6 h-6 accent-gray-500" />
                            <input type="text" name="question_data[options][]"
                                value="{{ old('question_data.options.' . $index, $option->value) }}"
                                placeholder="Option text" required
                                class="flex-1 p-3 text-lg border rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200" />
                        </div>
                    @endforeach
                    <button type="button" disabled
                        class="px-5 py-2 font-semibold text-gray-400 bg-gray-100 cursor-not-allowed rounded-xl">
                        Adding options in edit not supported
                    </button>
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
                @php $pairs = old('question_data.options', $question->metadata['answer'] ?? []); @endphp
                <div>
                    <label class="block mb-3 font-semibold text-gray-700">Linking Pairs</label>
                    @foreach ($pairs as $i => $pair)
                        <div class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50">
                            <h3 class="mb-2 font-semibold text-gray-700">Pair #{{ $i + 1 }}</h3>

                            <!-- Left (Label) -->
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-700">Left Side (Label)</label>
                                @if (($pair['left']['match_type'] ?? 'text') === 'image')
                                    <div class="mb-2">
                                        <img src="{{ $pair['left']['image_uri'] }}" alt="Left Image" class="h-20" />
                                        <input type="hidden"
                                            name="question_data[options][{{ $i }}][existing_label_image_uri]"
                                            value="{{ $pair['left']['image_uri'] }}" />
                                    </div>
                                    <input type="file"
                                        name="question_data[options][{{ $i }}][label_image]"
                                        class="mb-2" />
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][match_type]"
                                        value="image" />
                                @else
                                    <input type="text"
                                        name="question_data[options][{{ $i }}][label_text]"
                                        value="{{ $pair['left']['word'] }}"
                                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-gray-200" />
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][match_type]"
                                        value="text" />
                                @endif
                            </div>

                            <!-- Right (Value) -->
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-700">Right Side (Value)</label>
                                @if (($pair['right']['match_type'] ?? 'text') === 'image')
                                    <div class="mb-2">
                                        <img src="{{ $pair['right']['image_uri'] }}" alt="Right Image"
                                            class="h-20" />
                                        <input type="hidden"
                                            name="question_data[options][{{ $i }}][existing_value_image_uri]"
                                            value="{{ $pair['right']['image_uri'] }}" />
                                    </div>
                                    <input type="file"
                                        name="question_data[options][{{ $i }}][value_image]"
                                        class="mb-2" />
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][value_type]"
                                        value="image" />
                                @else
                                    <input type="text"
                                        name="question_data[options][{{ $i }}][value_text]"
                                        value="{{ $pair['right']['word'] }}"
                                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-gray-200" />
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][value_type]"
                                        value="text" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <small class="text-gray-500">To add or remove pairs, please recreate the question.</small>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::REARRANGING)
                <div class="p-4 space-y-4 border rounded bg-gray-50">
                    {{-- @dd($question->toArray()); --}}

                    <!-- Instruction -->
                    <div>
                        <label class="font-semibold">Instruction:</label>
                        <input type="text" name="instruction"
                            value="{{ $question->metadata['instruction'] ?? '' }}" name="question_data[instruction]"
                            class="w-full p-2 mt-1 border rounded" placeholder="Enter instruction">
                    </div>

                    <!-- Options to Rearrange -->
                    <div>
                        <label class="font-semibold">Words (Options):</label>
                        <div class="space-y-2">
                            @foreach ($question->metadata['options'] ?? [] as $index => $opt)
                                <div class="flex items-center gap-2">
                                    <input type="text" name="question_data[options][{{ $index }}][value]"
                                        value="{{ $opt['value'] }}" class="w-full p-2 border rounded"
                                        placeholder="Word">
                                    <input type="hidden" name="options[{{ $index }}][is_correct]"
                                        value="false">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Correct Answer Order -->
                    <div>
                        <label class="font-semibold">Correct Answer Order:</label>
                        <div class="space-y-2">
                            @foreach ($question->metadata['answer']['answer'] ?? [] as $index => $word)
                                <input type="text" name="question_data[answer][{{ $index }}]"
                                    value="{{ $word }}" class="w-full p-2 border rounded"
                                    placeholder="Correct Word in Order">
                            @endforeach
                        </div>
                        <input type="hidden" name="question_data[answer][format]"
                            value="{{ $question->metadata['answer']['format'] ?? 'ordered' }}">
                    </div>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::COMPREHENSION)
                <div x-data="comprehension()" class="p-4 space-y-4 border rounded bg-gray-50">
                    <label class="block mb-4 font-semibold">Passage Questions</label>
                    <template x-for="(question, index) in questions" :key="index">
                        <div class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50">
                            <label class="block mb-2 font-semibold">Question # <span
                                    x-text="index + 1"></span></label>
                            <input type="text" required x-model="question.question"
                                class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200" />
                            <label class="block mb-2 font-semibold">Answer # <span x-text="index + 1"></span></label>
                            <input type="text" required x-model="question.answer"
                                class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200" />

                            <button type="button" @click="removeQuestion(index)"
                                class="text-red-600 hover:underline">Remove</button>
                        </div>

                    </template>

                    <input type="hidden" name="question_data[questions]" :value="JSON.stringify(questions)">

                    <button type="button" @click="addQuestion"
                        class="px-5 py-2 font-semibold text-white bg-indigo-500 rounded-xl">+ Add Question</button>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS)
                @php
                    $items = $question->metadata['questions'] ?? [];
                @endphp

                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl">
                    <div class="p-4 mt-4 border rounded bg-gray-50" x-data="underlinecorrect()">
                        <h2 class="mb-3 font-semibold text-gray-700">Select Correct Answers</h2>
                        <template x-for="(item , index) in questions" x-key="index">
                            <div class="flex items-center mb-3 space-x-4">
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-gray-600">
                                        Question No:<span x-text="item.blank_number"></span>
                                    </label>
                                    <select :name="`question_data[questions][${index}][correct_answer]`"
                                        class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        x-model="item.correct_answer">
                                        <option value="">-- Select --</option>
                                        <template x-for="option in item.options" :key="option">
                                            <option x-text="option" :value="option"
                                                x-bind:selected="option == item.correct_answer"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <input type="hidden" name="question_data[questions]" :value="JSON.stringify(questions)">


                        {{-- @foreach ($items as $item)
                            <div class="flex items-center mb-3 space-x-4">
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-gray-600">
                                        Question No:{{ $item['blank_number'] }}
                                    </label>
                                    <select name="question_data[questions][{{ $loop->index }}][correct_answer]"
                                        class="block w-full px-4 py-3 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Select --</option>
                                        @foreach ($item['options'] as $option)
                                            <option value="{{ $option }}"
                                                {{ $option === $item['correct_answer'] ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach --}}
                    </div>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::EDITING)
                @php
                    $items = [];
                @endphp

                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl" x-data="editing()">


                    <div class="p-4 mt-4 border rounded bg-gray-50">
                        <h2 class="mb-3 font-semibold text-gray-700">Editing Questions:</h2>
                        <template x-for="(item, index) in questions" :key="index">
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Box No:</label>
                                    <input type="number" x-model="item.box"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Wrong Word:</label>
                                    <input type="text" x-model="item.wrong"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Correct Word:</label>
                                    <input type="text" x-model="item.correct"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                    <button type="button" @click="removeQuestion(index)"
                                        class="text-red-600 hover:underline">Remove</button>
                                </div>

                            </div>


                        </template>

                        <input type="hidden" name="question_data[questions]" :value="JSON.stringify(questions)">

                        <button type="button" @click="addQuestion"
                            class="px-4 py-2 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                            + Add Question
                        </button>




                        {{-- @foreach ($items as $index => $item)
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Box No:</label>
                                    <input type="number" name="questions[{{ $index }}][box]"
                                        value="{{ $item['box'] }}"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Wrong Word:</label>
                                    <input type="text" name="questions[{{ $index }}][wrong]"
                                        value="{{ $item['wrong'] }}"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                </div>

                                <div>
                                    <label class="block mb-1 text-sm font-medium text-gray-600">Correct Word:</label>
                                    <input type="text" name="questions[{{ $index }}][correct]"
                                        value="{{ $item['correct'] }}"
                                        class="w-full p-2 border rounded focus:ring focus:ring-blue-300" />
                                </div>
                            </div>
                        @endforeach --}}
                    </div>
                </div>
            @endif
            @if ($question->type === \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_OPTIONS)
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
                            value="{{ implode(',', $question->metadata['question_group']['shared_options'] ?? []) ?? '' }}"
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
                    <input type="hidden" name="question_data[metadata]" id="metadataInput"
                        x-model="formattedJson" />
                </div>
            @endif
            <div class="flex justify-end mt-8">
                <button type="submit" class="px-10 py-3 text-lg font-extrabold text-white transition add-btn">
                    Update Question
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
<script>
    function underlinecorrect() {
        return {
            questions: @json($question->metadata['questions'] ?? []),
        }
    }

    function editing() {
        return {
            questions: @json($question->metadata['questions'] ?? []),
            init() {
                console.log(this.questions);
            },
            addQuestion() {
                this.questions.push({
                    box: this.questions.length + 1,
                    wrong: null,
                    correct: null
                })
            }
        }
    }

    function comprehension() {
        return {
            questions: @json($question->metadata['subquestions'] ?? []),
            init() {
                console.log(this.questions);
            },
            addQuestion() {
                this.questions.push({
                    question: '',
                    answer: ''
                })
            },
            removeQuestion(index) {
                this.questions.splice(index, 1);
            }
        }
    }

    function questionForm() {
        return {
            quill: null,
            questionContent: @json(old('question_data.content', $question->content)),
            questions: @json($question->metadata['questions'] ?? []),
            sharedOptionsRaw: `{{ implode(',', $question->metadata['question_group']['shared_options'] ?? []) ?? '' }}`,
            formattedJson: @json(json_encode($question->metadata) ?? '{}'),
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
            blankCounter: 12,
            blanks: @json($question->metadata['blanks'] ?? []),
            jsonOutput: '',

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
                // Show answer input immediately for new blank
                // this.blanks.push({ number: this.blanks.length + 1, answer: '' });
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


        };
    }
</script>
