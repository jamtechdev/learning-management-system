<x-app-layout>
    <div class="max-w-7xl p-10 mx-auto bg-white border border-gray-200  shadow-[0_8px_30px_rgba(55,55, 55, / 10%)] rounded-3xl"
        style="font-family: 'Inter', sans-serif;">
        <h1
            class="mb-10 text-3xl font-bold text-center text-black drop-shadow-[0_8px_30px_rgba(55,55, 55, / 10%]">
            Edit Question
        </h1>

        <form method="POST" action="{{ route('admin.questions.update', $question->id) }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-2 font-semibold text-gray-800">Education Type</label>
                <input type="text" name="question_data[education_type]"
                    value="{{ ucfirst($question->education_type) }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-800">Level</label>
                <input type="text" value="{{ $question->level->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[level_id]" value="{{ $question->level->id ?? '' }}" />
            </div>
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Subject</label>
                <input type="text" value="{{ $question->subject->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50" />
                <input type="hidden" name="question_data[subject_id]" value="{{ $question->subject->id ?? '' }}" />
            </div>
            <div>
                <label class="block mb-2 font-semibold text-gray-800">Question Content</label>
                <textarea name="question_data[content]" rows="4" required
                    class="w-full p-4 text-lg text-black border border-gray-500 cursor-not-allowed rounded-xl bg-gray-50">{{ old('question_data.content', $question->content) }}</textarea>
            </div>


            <input type="hidden" name="question_data[type]" value="{{ $question->type }}" />
            @if ($question->type === 'mcq')
                <div>
                    <label class="block mb-3 font-semibold text-gray-800">MCQ Options</label>
                    @foreach ($question->options as $index => $option)
                        <div class="flex items-center gap-4 p-3 mb-4 border-2 bg-gray-50 rounded-xl">
                            <input type="radio" name="question_data[correct_option]" value="{{ $index }}"
                                {{ old('question_data.correct_option', $option->is_correct) == $index ? 'checked' : '' }}
                                required class="w-6 h-6 accent-grat-500" />
                            <input type="text" name="question_data[options][]"
                                value="{{ old('question_data.options.' . $index, $option->value) }}"
                                placeholder="Option text" required
                                class="flex-1 p-3 text-lg border rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200" />
                        </div>
                    @endforeach
                    <button type="button" disabled
                        class="px-5 py-2 font-semibold text-gray-400 bg-gray-100 cursor-not-allowed rounded-xl">Adding
                        options in edit not supported</button>
                </div>
            @elseif ($question->type === 'fill_blank')
                <div>
                    <label class="block mb-3 font-semibold text-gray-700">Fill in the Blanks</label>

                    @php
                        $blanks = old('question_data.blanks', $question->metadata['blanks'] ?? []);
                    @endphp

                    @foreach ($blanks as $i => $blank)
                        <div class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50">
                            <h3 class="mb-2 font-semibold text-gray-700">Blank #{{ $i + 1 }}</h3>
                            <input type="hidden" name="question_data[blanks][{{ $i }}][blank_number]"
                                value="{{ $blank['blank_number'] ?? $i + 1 }}">
                            <label class="block mb-1 font-semibold text-gray-700">Correct Answer</label>
                            <select name="question_data[blanks][{{ $i }}][answer]" required
                                class="w-full p-2 mb-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-gray-200">
                                <option value="" disabled {{ empty($blank['answer']) ? 'selected' : '' }}>
                                    Select correct answer
                                </option>
                                @foreach ($blank['options'] ?? [] as $option)
                                    <option value="{{ $option }}"
                                        {{ ($blank['answer'] ?? '') === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
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

                    <small class="text-gray-500">To add or remove blanks/options, please recreate the
                        question.</small>
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
                <div>
                    <label class="block mb-3 font-semibold text-gray-700">Linking Pairs</label>
                    @php
                        $pairs = old('question_data.options', $question->metadata['answer'] ?? []);
                    @endphp

                    @foreach ($pairs as $i => $pair)
                        <div class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50">
                            <h3 class="mb-2 font-semibold text-gray-700">Pair #{{ $i + 1 }}</h3>

                            {{-- Left (Label) --}}
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-700">Left Side (Label)</label>

                                @if (($pair['left']['match_type'] ?? 'text') === 'image')
                                    <div class="mb-2">
                                        <img src="{{ $pair['left']['image_uri'] }}" alt="Left Image" class="h-20">
                                        <input type="hidden"
                                            name="question_data[options][{{ $i }}][existing_label_image_uri]"
                                            value="{{ $pair['left']['image_uri'] }}">
                                    </div>
                                    <input type="file"
                                        name="question_data[options][{{ $i }}][label_image]" class="mb-2">
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][match_type]" value="image">
                                @else
                                    <input type="text"
                                        name="question_data[options][{{ $i }}][label_text]"
                                        value="{{ $pair['left']['word'] }}"
                                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-gray-200">
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][match_type]" value="text">
                                @endif
                            </div>

                            {{-- Right (Value) --}}
                            <div class="mb-4">
                                <label class="block font-semibold text-gray-700">Right Side (Value)</label>

                                @if (($pair['right']['match_type'] ?? 'text') === 'image')
                                    <div class="mb-2">
                                        <img src="{{ $pair['right']['image_uri'] }}" alt="Right Image" class="h-20">
                                        <input type="hidden"
                                            name="question_data[options][{{ $i }}][existing_value_image_uri]"
                                            value="{{ $pair['right']['image_uri'] }}">
                                    </div>
                                    <input type="file"
                                        name="question_data[options][{{ $i }}][value_image]"
                                        class="mb-2">
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][value_type]"
                                        value="image">
                                @else
                                    <input type="text"
                                        name="question_data[options][{{ $i }}][value_text]"
                                        value="{{ $pair['right']['word'] }}"
                                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-gray-200">
                                    <input type="hidden"
                                        name="question_data[options][{{ $i }}][value_type]"
                                        value="text">
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <small class="text-gray-500">To add or remove pairs, please recreate the question.</small>
                </div>
            @endif


            <div class="flex justify-end mt-8">
                <button type="submit"
                    class="px-10 py-3 text-lg font-extrabold text-white transition add-btn">
                    Update Question
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
