<x-app-layout>
    <div class="max-w-full p-10 mx-auto bg-white border border-yellow-400 shadow-[0_8px_30px_rgba(255,195,0,0.3)] rounded-3xl"
        style="font-family: 'Inter', sans-serif;">
        <h1
            class="mb-10 text-5xl font-extrabold text-center text-yellow-600 drop-shadow-[0_2px_6px_rgba(255,195,0,0.7)]">
            Edit Question
        </h1>

        <form method="POST" action="{{ route('admin.questions.update', $question->id) }}" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Education Type (readonly) -->
            <div>
                <label class="block mb-2 font-semibold text-yellow-700">Education Type</label>
                <input type="text" name="question_data[education_type]"
                    value="{{ ucfirst($question->education_type) }}" readonly
                    class="w-full p-3 text-lg text-yellow-800 border border-yellow-300 cursor-not-allowed rounded-xl bg-yellow-50" />
            </div>

            <!-- Level (show name readonly, submit id hidden) -->
            <div>
                <label class="block mb-2 font-semibold text-yellow-700">Level</label>
                <!-- Visible readonly input showing name -->
                <input type="text" value="{{ $question->level->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-yellow-800 border border-yellow-300 cursor-not-allowed rounded-xl bg-yellow-50" />
                <!-- Hidden input for level_id sent in request -->
                <input type="hidden" name="question_data[level_id]" value="{{ $question->level->id ?? '' }}" />
            </div>

            <!-- Subject (show name readonly, submit id hidden) -->
            <div>
                <label class="block mb-2 font-semibold text-yellow-700">Subject</label>
                <!-- Visible readonly input showing name -->
                <input type="text" value="{{ $question->subject->name ?? '' }}" readonly
                    class="w-full p-3 text-lg text-yellow-800 border border-yellow-300 cursor-not-allowed rounded-xl bg-yellow-50" />
                <!-- Hidden input for subject_id sent in request -->
                <input type="hidden" name="question_data[subject_id]" value="{{ $question->subject->id ?? '' }}" />
            </div>


            <!-- Question Content (editable) -->
            <div>
                <label class="block mb-2 font-semibold text-yellow-700">Question Content</label>
                <textarea name="question_data[content]" rows="4" required
                    class="w-full p-4 text-lg border border-yellow-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-yellow-200">{{ old('question_data.content', $question->content) }}</textarea>
            </div>


            <input type="hidden" name="question_data[type]" value="{{ $question->type }}" />
            {{-- Dynamic Inputs for Question Type --}}
            @if ($question->type === 'mcq')
                <div>
                    <label class="block mb-3 font-semibold text-yellow-700">MCQ Options</label>
                    @foreach ($question->options as $index => $option)
                        <div class="flex items-center gap-4 p-3 mb-4 border-2 border-yellow-200 rounded-xl">
                            <input type="radio" name="question_data[correct_option]" value="{{ $index }}"
                                {{ old('question_data.correct_option', $option->is_correct) == $index ? 'checked' : '' }}
                                required class="w-6 h-6 accent-yellow-500" />
                            <input type="text" name="question_data[options][]"
                                value="{{ old('question_data.options.' . $index, $option->value) }}"
                                placeholder="Option text" required
                                class="flex-1 p-3 text-lg border rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200" />
                        </div>
                    @endforeach
                    <button type="button" disabled
                        class="px-5 py-2 font-semibold text-yellow-400 bg-yellow-100 cursor-not-allowed rounded-xl">Adding
                        options in edit not supported</button>
                </div>
            @elseif ($question->type === 'fill_blank')
                <div>
                    <label class="block mb-3 font-semibold text-yellow-700">Fill in the Blanks</label>

                    @php
                        $blanks = old('question_data.blanks', $question->metadata['blanks'] ?? []);
                    @endphp

                    @foreach ($blanks as $i => $blank)
                        <div class="p-4 mb-6 border border-yellow-300 rounded-lg bg-yellow-50">
                            <h3 class="mb-2 font-semibold text-yellow-700">Blank #{{ $i + 1 }}</h3>

                            <label class="block mb-1 font-semibold text-yellow-700">Answer</label>
                            <input type="text" name="question_data[blanks][{{ $i }}][answer]"
                                value="{{ $blank['answer'] ?? '' }}" required
                                class="w-full p-2 mb-3 border border-yellow-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200" />

                            <label class="block mb-1 font-semibold text-yellow-700">Options</label>
                            @foreach ($blank['options'] ?? [] as $optIndex => $option)
                                <input type="text" name="question_data[blanks][{{ $i }}][options][]"
                                    value="{{ $option }}" required
                                    class="w-full p-2 mb-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200" />
                            @endforeach
                            {{-- Optionally add a note that adding options dynamically isn't supported here --}}
                        </div>
                    @endforeach

                    <small class="text-yellow-500">To add or remove blanks/options, please recreate the
                        question.</small>
                </div>
            @elseif ($question->type === 'true_false')

                <div>
                    <label class="block mb-3 font-semibold text-yellow-700">Answer</label>
                    <select name="question_data[true_false_answer]" required
                        class="w-full p-3 text-lg border border-yellow-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200">
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
                    <label class="block mb-3 font-semibold text-yellow-700">Linking Pairs</label>
                    @php
                        $pairs = old('question_data.pairs', $question->metadata['answer'] ?? []);
                    @endphp
                    @foreach ($pairs as $i => $pair)
                        <div class="p-4 mb-6 border border-yellow-300 rounded-lg bg-yellow-50">
                            <h3 class="mb-2 font-semibold text-yellow-700">Pair #{{ $i + 1 }}</h3>

                            <div class="mb-3">
                                <label class="block mb-1 font-semibold text-yellow-700">Left Text</label>
                                <input type="text" name="question_data[pairs][{{ $i }}][left][word]"
                                    value="{{ $pair['left']['word'] ?? '' }}" required
                                    class="w-full p-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200" />
                                <input type="hidden"
                                    name="question_data[pairs][{{ $i }}][left][match_type]"
                                    value="text" />
                            </div>

                            <div>
                                <label class="block mb-1 font-semibold text-yellow-700">Right Text</label>
                                <input type="text" name="question_data[pairs][{{ $i }}][right][word]"
                                    value="{{ $pair['right']['word'] ?? '' }}" required
                                    class="w-full p-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-yellow-200" />
                                <input type="hidden"
                                    name="question_data[pairs][{{ $i }}][right][match_type]"
                                    value="text" />
                            </div>
                        </div>
                    @endforeach

                    <small class="text-yellow-500">To add or remove pairs, please recreate the question.</small>
                </div>

            @endif

            <div class="flex justify-end mt-8">
                <button type="submit"
                    class="px-10 py-3 text-lg font-extrabold text-white transition bg-yellow-600 shadow-lg rounded-3xl hover:bg-yellow-700">
                    Update Question
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
