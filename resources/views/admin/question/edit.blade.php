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

            <!-- Question Content -->
            <div>
                <div x-ref="questionContentEditor"
                    class="border-2 border-blue-300 shadow-sm quill-editor focus:ring-4 focus:ring-blue-200"
                    style="min-height: 150px;">
                </div>
                <!-- Moved input outside the Quill container -->
                <input type="hidden" name="question_data[content]" :value="questionContent" x-model="questionContent">
            </div>


            <!-- Hidden Question Type -->
            <input type="hidden" name="question_data[type]" value="{{ $question->type }}" />

            <!-- Type-specific inputs -->
            @if ($question->type === 'mcq')
                <div>
                    <label class="block mb-3 font-semibold text-gray-800">MCQ Options</label>
                    @foreach ($question->options as $index => $option)
                        <div class="flex items-center gap-4 p-3 mb-4 border-2 bg-gray-50 rounded-xl">
                            <input type="radio" name="question_data[correct_option]" value="{{ $index }}"
                                {{ old('question_data.correct_option', $option->is_correct) == $index ? 'checked' : '' }}
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
            @elseif ($question->type === 'fill_blank')
                @php $blanks = old('question_data.blanks', $question->metadata['blanks'] ?? []); @endphp
                <div>
                    <label class="block mb-3 font-semibold text-gray-700">Fill in the Blanks</label>
                    @foreach ($blanks as $i => $blank)
                        <div class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50">
                            <h3 class="mb-2 font-semibold text-gray-700">Blank #{{ $i + 1 }}</h3>
                            <input type="hidden" name="question_data[blanks][{{ $i }}][blank_number]"
                                value="{{ $blank['blank_number'] ?? $i + 1 }}">
                            <label class="block mb-1 font-semibold text-gray-700">Correct Answer</label>
                            <select name="question_data[blanks][{{ $i }}][answer]" required
                                class="w-full p-2 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200">
                                <option value="" disabled {{ empty($blank['answer']) ? 'selected' : '' }}>Select
                                    correct answer</option>
                                @foreach ($blank['options'] ?? [] as $option)
                                    <option value="{{ $option }}"
                                        {{ ($blank['answer'] ?? '') === $option ? 'selected' : '' }}>
                                        {{ $option }}</option>
                                @endforeach
                            </select>
                            <label class="block mb-1 font-semibold text-gray-700">Options</label>
                            @foreach ($blank['options'] ?? [] as $optIndex => $option)
                                <input type="text" name="question_data[blanks][{{ $i }}][options][]"
                                    value="{{ $option }}" required
                                    class="w-full p-2 mb-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200" />
                            @endforeach
                        </div>
                    @endforeach
                    <small class="text-gray-500">To add or remove blanks/options, please recreate the question.</small>
                </div>
            @elseif ($question->type === 'true_false')
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
            @elseif ($question->type === 'linking')
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
            @if ($question->type === 'rearranging')
                <div class="p-4 space-y-4 border rounded bg-gray-50">

                    <!-- Instruction -->
                    <div>
                        <label class="font-semibold">Instruction:</label>
                        <input type="text" name="instruction"
                            value="{{ $question->metadata['instruction'] ?? '' }}"
                            class="w-full p-2 mt-1 border rounded" placeholder="Enter instruction">
                    </div>

                    <!-- Options to Rearrange -->
                    <div>
                        <label class="font-semibold">Words (Options):</label>
                        <div class="space-y-2">
                            @foreach ($question->metadata['options'] ?? [] as $index => $opt)
                                <div class="flex items-center gap-2">
                                    <input type="text" name="options[{{ $index }}][value]"
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
                                <input type="text" name="answer[answer][{{ $index }}]"
                                    value="{{ $word }}" class="w-full p-2 border rounded"
                                    placeholder="Correct Word in Order">
                            @endforeach
                        </div>
                        <input type="hidden" name="answer[format]"
                            value="{{ $question->metadata['answer']['format'] ?? 'ordered' }}">
                    </div>

                </div>
            @endif
            @if ($question->type === 'comprehension')
                <div>
                    <label class="block mb-4 font-semibold">Passage Questions</label>
                    @php
                        $comprehension = old('question_data.comprehension', $question->metadata['subquestions'] ?? []);
                    @endphp

                    @foreach ($comprehension as $i => $comp)
                        <div class="p-5 mb-8 border border-gray-300 rounded-lg bg-gray-50">
                            <label class="block mb-2 font-semibold">Question #{{ $i + 1 }}</label>
                            <input type="text" name="question_data[comprehension][{{ $i }}][ques1]"
                                required value="{{ $comp['ques1'] ?? '' }}"
                                class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200" />

                            <label class="block mb-2 font-semibold">Answer #{{ $i + 1 }}</label>
                            <input type="text" name="question_data[comprehension][{{ $i }}][answer]"
                                required value="{{ $comp['answer'] ?? '' }}"
                                class="w-full p-3 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200" />
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($question->type === 'underlinecorrect')
                @php
                    $items = $question->metadata['questions'] ?? [];
                @endphp

                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl">
                    <div class="p-4 mt-4 border rounded bg-gray-50">
                        <h2 class="mb-3 font-semibold text-gray-700">Select Correct Answers</h2>

                        @foreach ($items as $item)
                            <div class="flex items-center mb-3 space-x-4">
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-gray-600">
                                        Question No:{{ $item['blank_number'] }}
                                    </label>
                                    <select name="questions[{{ $loop->index }}][correct_answer]"
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
                        @endforeach
                    </div>
                </div>
            @endif
            @if ($question->type === 'editing')
                @php
                    $items = $question->metadata['questions'] ?? [];
                @endphp

                <div class="p-6 mt-6 space-y-6 bg-white border shadow rounded-xl">


                    <div class="p-4 mt-4 border rounded bg-gray-50">
                        <h2 class="mb-3 font-semibold text-gray-700">Editing Questions:</h2>

                        @foreach ($items as $index => $item)
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
                        @endforeach
                    </div>
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
    function questionForm() {
        return {
            quill: null,
            questionContent: @json(old('question_data.content', $question->content)),
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
                });
            }
        };
    }
</script>
