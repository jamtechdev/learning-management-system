<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800 sm:rounded-lg"
                x-data="{
                    type: '{{ old('question_data.type') }}',
                    questionContent: '{{ old('question_data.content') }}',
                    answers: @json(old('question_data.answer', [])),

                    insertBlank() {
                        const textarea = document.getElementById('question_data_content');
                        const cursorPos = textarea.selectionStart;
                        const before = this.questionContent.slice(0, cursorPos);
                        const after = this.questionContent.slice(cursorPos);
                        this.questionContent = before + ' _____ ' + after;
                        this.$nextTick(() => {
                            textarea.focus();
                            textarea.selectionStart = textarea.selectionEnd = cursorPos + 6;
                        });
                        const blanks = (this.questionContent.match(/_____/g) || []).length;
                        while (this.answers.length < blanks) {
                            this.answers.push('');
                        }
                    }
                }">

                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Create Question</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose question type and fill relevant
                        fields.</p>
                </header>

                <form method="POST" action="{{ route('admin.questions.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <!-- Question Type -->
                    <div>
                        <x-input-label for="question_data_type" value="Type" />
                        <select id="question_data_type" name="question_data[type]" x-model="type" class="w-full mt-1">
                            <option value="">Select Type</option>
                            <option value="mcq" {{ old('question_data.type') == 'mcq' ? 'selected' : '' }}>MCQ
                            </option>
                            <option value="fill_blank"
                                {{ old('question_data.type') == 'fill_blank' ? 'selected' : '' }}>Fill in the Blank
                            </option>
                            <option value="true_false"
                                {{ old('question_data.type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="linking" {{ old('question_data.type') == 'linking' ? 'selected' : '' }}>
                                Linking</option>
                            <option value="spelling" {{ old('question_data.type') == 'spelling' ? 'selected' : '' }}>
                                Spelling</option>
                            <option value="math" {{ old('question_data.type') == 'math' ? 'selected' : '' }}>Math
                            </option>
                            <option value="grouped" {{ old('question_data.type') == 'grouped' ? 'selected' : '' }}>
                                Grouped</option>
                            <option value="comprehension"
                                {{ old('question_data.type') == 'comprehension' ? 'selected' : '' }}>Comprehension
                            </option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('question_data.type')" />
                    </div>

                    <!-- Question Content -->
                    <div>
                        <x-input-label for="question_data_content" value="Question Content" />
                        <textarea id="question_data_content" name="question_data[content]" x-model="questionContent"
                            class="w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">{{ old('question_data.content') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('question_data.content')" />
                    </div>

                    <!-- MCQ Section -->
                    <template x-if="type === 'mcq'">
                        <div x-data="{
                            options: Array.from({ length: 1 }, () => ({ value: '', is_correct: false })),
                            setCorrect(index) {
                                this.options.forEach((opt, i) => opt.is_correct = (i === index));
                            },
                            addOption() {
                                if (this.options.length < 4) {
                                    this.options.push({ value: '', is_correct: false });
                                }
                            },
                            removeOption(index) {
                                this.options.splice(index, 1);
                            }
                        }" x-init="@if(old('question_data.options'))
                        options = @json(array_values(old('question_data.options')));
                        @endif" class="mt-4 space-y-2">
                            <x-input-label value="MCQ Options" />

                            <template x-for="(option, index) in options" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" class="w-full" :placeholder="`Option ${index + 1}`"
                                        :name="`question_data[options][${index}][value]`" x-model="option.value" />

                                    <label class="flex items-center gap-1">
                                        <input type="checkbox" :checked="option.is_correct"
                                            @change="setCorrect(index)" />
                                        <input type="hidden" :name="`question_data[options][${index}][is_correct]`"
                                            :value="option.is_correct ? 1 : 0" />
                                        Correct
                                    </label>

                                    <button type="button" class="font-bold text-red-600" @click="removeOption(index)"
                                        x-show="options.length > 1">✕</button>
                                </div>
                            </template>

                            <div class="pt-2">
                                <button type="button" class="text-sm text-blue-600 underline" @click="addOption"
                                    :disabled="options.length >= 4">
                                    Add Option
                                </button>
                                <p x-show="options.length >= 4" class="text-xs text-gray-500">Maximum 4 options allowed.
                                </p>
                            </div>

                            <input type="hidden" name="question_data[format]" value="text">
                        </div>
                    </template>

                    <!-- Fill in the Blank Section -->
                    <template x-if="type === 'fill_blank'">
                        <div class="space-y-4">
                            <div>
                                <button type="button" @click="insertBlank"
                                    class="inline-flex items-center px-3 py-1 mt-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    + Add Blank
                                </button>
                            </div>

                            <div x-show="answers.length > 0" class="space-y-2">
                                <x-input-label value="Blank Answers" />
                                <template x-for="(answer, index) in answers" :key="index">
                                    <input type="text" :name="'question_data[answer][' + index + ']'"
                                        :placeholder="'Answer ' + (index + 1)" class="w-full px-2 py-1 border rounded"
                                        x-model="answers[index]" />
                                </template>
                            </div>

                            <input type="hidden" name="question_data[format]" value="text">
                        </div>
                    </template>

                    <!-- True / False with Textbox Explanation -->
                    <template x-if="type === 'true_false'">
                        <div class="space-y-4">
                            <div>
                                <x-input-label value="Select Answer" />
                                <select name="question_data[answer][choice]" class="w-full">
                                    <option value="True"
                                        {{ old('question_data.answer.choice') == 'True' ? 'selected' : '' }}>True
                                    </option>
                                    <option value="False"
                                        {{ old('question_data.answer.choice') == 'False' ? 'selected' : '' }}>False
                                    </option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('question_data.answer.choice')" />
                            </div>

                            <input type="hidden" name="question_data[format]" value="text">
                        </div>
                    </template>


                    <!-- Linking -->
                    <div x-show="type === 'linking'" class="space-y-2">
                        <x-input-label value="Matching Pairs" />
                        @for ($i = 0; $i < 4; $i++)
                            <div class="flex gap-2">
                                <input type="text" name="options[{{ $i }}][label]"
                                    value="{{ old('options.' . $i . '.label') }}"
                                    placeholder="Label {{ $i + 1 }}" class="w-1/3" />
                                <input type="text" name="options[{{ $i }}][value]"
                                    value="{{ old('options.' . $i . '.value') }}"
                                    placeholder="Match Value {{ $i + 1 }}" class="w-2/3" />
                            </div>
                        @endfor
                    </div>






                    <!-- Spelling -->
                    <div x-show="type === 'spelling'" class="space-y-2">
                        <x-input-label value="Correct Spelling" />
                        <input type="text" name="answer[]" value="{{ old('answer.0') }}" class="w-full" />
                        <input type="hidden" name="format" value="text" />
                    </div>

                    <!-- Math -->
                    <div x-show="type === 'math'" class="space-y-2">
                        <x-input-label value="Math Answer" />
                        <input type="text" name="answer[]" value="{{ old('answer.0') }}" class="w-full" />
                        <input type="hidden" name="format" value="text" />
                    </div>

                    <!-- Grouped -->
                    <div x-show="type === 'grouped'" class="space-y-2">
                        <x-input-label value="(Info only) Save this as a grouped parent. Add children later." />
                    </div>

                    <!-- Comprehension -->
                    <div x-show="type === 'comprehension'" class="space-y-2">
                        <x-input-label value="Passage for Comprehension" />
                        <textarea name="passage" rows="4" class="w-full">{{ old('passage') }}</textarea>
                    </div>

                    <!-- Explanation -->
                    <div>
                        <x-input-label for="question_data_explanation" value="Explanation (optional)" />
                        <textarea id="question_data_explanation" name="question_data[explanation]"
                            class="w-full mt-1 border-gray-300 rounded-md dark:bg-gray-700 dark:text-white">{{ old('question_data.explanation') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center gap-4">
                        <x-primary-button>Save Question</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
