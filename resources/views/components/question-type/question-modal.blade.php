<!-- resources/views/components/question-type/question-modal.blade.php -->

<div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="w-full max-w-4xl p-6 mx-2 bg-white rounded-lg shadow-lg dark:bg-gray-900">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">🧠 Question Options</h3>
            <button @click="showModal = false; activeQuestion = null"
                class="text-xl font-bold text-gray-500 hover:text-red-600">&times;</button>
        </div>

        <!-- Modal Content -->
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="relative w-full max-w-4xl p-6 mx-4 bg-white shadow-2xl rounded-xl dark:bg-gray-900" x-transition>
                <!-- Question Header -->
                <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">🧠 Question Options</h2>
                    <button @click="showModal = false; activeQuestion = null"
                        class="text-2xl leading-none text-gray-400 hover:text-red-600">
                        &times;
                    </button>
                </div>

                <!-- Instruction Section -->
                <template x-if="activeQuestion?.metadata?.instruction">
                    <div class="p-4 mb-4 border rounded bg-yellow-50 dark:bg-gray-800 dark:border-gray-700">
                        <h4 class="mb-1 text-sm font-semibold text-yellow-700 dark:text-yellow-300">Instruction</h4>
                        <div class="text-sm text-gray-700 dark:text-gray-300"
                            x-text="activeQuestion?.metadata?.instruction"></div>
                    </div>
                </template>

                <!-- Question Content Section -->
                <div class="p-4 mb-4 border rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="mb-2 text-base font-semibold text-gray-700 dark:text-gray-100">Question</h3>
                    <div class="prose dark:prose-invert max-w-none" x-html="activeQuestion?.content"></div>
                </div>

                <!-- Question Type Content -->
                <template x-if="activeQuestion">
                    <div class="space-y-6 overflow-y-auto max-h-[70vh] pr-2">
                        {{-- MCQ Type  --}}
                        <template x-if="activeQuestion?.type === 'mcq'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Options</h3>
                                <ul class="space-y-2">
                                    <template x-for="option in activeQuestion?.metadata?.options || []">
                                        <li class="flex items-start gap-2">
                                            <span class="font-semibold" x-text="option.value + '.'"></span>
                                            <span x-html="option.explanation"></span>
                                            <template x-if="option.is_correct">
                                                <span class="ml-2 text-xs font-semibold text-green-600">(Correct)</span>
                                            </template>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        {{-- TRUE/FALSE --}}
                        <template x-if="activeQuestion?.type === 'true_false'">
                            <div class="space-y-2">
                                <h3 class="font-semibold text-gray-700 dark:text-gray-200">Correct Answer</h3>
                                <template x-for="choice in ['True', 'False']">
                                    <div
                                        :class="{
                                            'text-green-600 font-bold': activeQuestion?.metadata?.answer?.choice ===
                                                choice,
                                            'text-gray-800 dark:text-white': activeQuestion?.metadata?.answer
                                                ?.choice !== choice
                                        }">
                                        <span x-text="choice"></span>
                                        <template x-if="activeQuestion?.metadata?.answer?.choice === choice">
                                            <span> ✅</span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="activeQuestion?.type === 'linking'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Linked Pairs</h3>
                                <template x-for="pair in activeQuestion?.metadata?.answer || []"
                                    :key="pair.left.word + '_' + pair.right.word">
                                    <div
                                        class="flex items-center justify-between p-2 mb-1 bg-white border rounded dark:border-gray-700 dark:bg-gray-800">
                                        <div class="flex items-center space-x-2">
                                            <template x-if="pair.left.match_type === 'image'">
                                                <img :src="pair.left.image_uri" class="w-10 h-10 border rounded">
                                            </template>
                                            <template x-if="pair.left.match_type !== 'image'">
                                                <span x-text="pair.left.word"></span>
                                            </template>
                                        </div>
                                        <span class="mx-3 text-gray-500">→</span>
                                        <div class="flex items-center space-x-2">
                                            <template x-if="pair.right.match_type === 'image'">
                                                <img :src="pair.right.image_uri" class="w-10 h-10 border rounded">
                                            </template>
                                            <template x-if="pair.right.match_type !== 'image'">
                                                <span x-text="pair.right.word"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- REARRANGING Type -->
                        <template x-if="activeQuestion?.type === 'rearranging'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Available Words</h3>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="opt in activeQuestion?.metadata?.options || []">
                                        <span class="px-2 py-1 text-sm bg-gray-100 rounded dark:bg-gray-700"
                                            x-text="opt.value"></span>
                                    </template>
                                </div>

                                <h3 class="mt-4 mb-2 font-semibold text-gray-700 dark:text-gray-200">Correct Order</h3>
                                <ol class="list-decimal list-inside">
                                    <template x-for="word in activeQuestion?.metadata?.answer?.answer || []">
                                        <li x-text="word"></li>
                                    </template>
                                </ol>
                            </div>
                        </template>

                        <!-- Open Cloze with Options -->
                        <template x-if="activeQuestion?.type === 'open_cloze_with_options'">
                            <div>
                                <!-- Shared Options Section -->
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Shared Options</h3>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <template
                                        x-for="opt in activeQuestion?.metadata?.question_group?.shared_options || []">
                                        <span
                                            class="px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200"
                                            x-text="opt"></span>
                                    </template>
                                </div>

                                <!-- Questions (Blanks) Section -->
                                <template x-for="blank in activeQuestion?.metadata?.questions || []"
                                    :key="blank.blank_number">
                                    <div class="p-3 mb-2 border rounded dark:border-gray-700 dark:bg-gray-900">
                                        <h4 class="mb-1 font-medium text-gray-700 dark:text-gray-100"
                                            x-text="'Blank #' + blank.blank_number"></h4>
                                        <span class="font-semibold text-green-700 dark:text-green-400"
                                            x-text="'Correct: ' + blank.correct_answer"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Open Cloze with Dropdown Options -->
                        <template x-if="activeQuestion?.type === 'open_cloze_with_dropdown_options'">
                            <div>
                                <h3 class="mb-3 font-semibold text-gray-700 dark:text-gray-200">Dropdown Blanks</h3>
                                <template x-for="blank in activeQuestion?.metadata?.questions || []">
                                    <div class="p-4 mb-2 border rounded dark:border-gray-700 dark:bg-gray-800">
                                        <h4 class="mb-1 font-semibold text-gray-700 dark:text-white"
                                            x-text="'Blank #' + blank.blank_number"></h4>
                                        <ul class="pl-5 text-gray-700 list-disc list-inside dark:text-gray-200">
                                            <template x-for="opt in blank.options || []">
                                                <li>
                                                    <span x-text="opt"></span>
                                                    <template x-if="opt === blank.correct_answer">
                                                        <span class="ml-2 font-semibold text-green-600">(Correct)</span>
                                                    </template>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- EDITING Type -->
                        <template x-if="activeQuestion?.type === 'editing'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Mistakes</h3>
                                <template x-for="item in activeQuestion?.metadata?.questions || []"
                                    :key="item.wrong">
                                    <div class="p-2 bg-white border rounded dark:bg-gray-800 dark:border-gray-700">
                                        <span class="font-semibold text-red-600">Wrong:</span>
                                        <span x-text="item.wrong" class="mr-4"></span>
                                        <span class="font-semibold text-green-600">Correct:</span>
                                        <span x-text="item.correct"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <!-- Fill in the Blank -->
                        <template x-if="activeQuestion?.type === 'fill_in_the_blank'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Blanks</h3>
                                <template x-for="blank in activeQuestion?.metadata?.blanks || []"
                                    :key="blank.blank_number">
                                    <div
                                        class="p-2 mb-2 border rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="font-semibold text-gray-700 dark:text-gray-200">
                                            Blank #<span x-text="blank.blank_number"></span>:
                                            <span class="ml-2 font-medium text-green-700 dark:text-green-400"
                                                x-text="blank.correct_answer"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- COMPREHENSION Type -->
                        <!-- COMPREHENSION Type -->
                        <template x-if="activeQuestion?.type === 'comprehension'">
                            <div>
                                <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Sub-Questions</h3>

                                <!-- Loop over each subquestion -->
                                <template x-for="(item, index) in activeQuestion?.metadata?.subquestions || []"
                                    :key="index">
                                    <div
                                        class="p-3 mb-2 border rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-700">

                                        <!-- Display the Question Text -->
                                        <div class="font-medium" x-text="'Q' + (index + 1) + ': ' + item.question">
                                        </div>

                                        <!-- Display the Answer -->
                                        <div class="mt-1 text-green-600 dark:text-green-400">
                                            <span class="font-semibold">Answer:</span> <span
                                                x-text="item.answer"></span>
                                        </div>

                                        <!-- If the question type is MCQ, show options -->
                                        <template x-if="item.type === 'mcq'">
                                            <div class="mt-2">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300">Options:
                                                </div>
                                                <ul class="ml-4 list-disc">
                                                    <template x-for="(option, optIndex) in item.options || []"
                                                        :key="optIndex">
                                                        <li x-text="option" class="text-gray-700 dark:text-gray-400">
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        <!-- You can add more templates to handle other types like 'true_false', 'fill_blank', and 'open_ended' -->
                                        <!-- True/False Type -->
                                        <template x-if="item.type === 'true_false'">
                                            <div class="mt-2">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300">Answer:
                                                </div>
                                                <span class="text-gray-700 dark:text-gray-400"
                                                    x-text="item.answer"></span>
                                            </div>
                                        </template>

                                        <!-- Fill Blank Type -->
                                        <template x-if="item.type === 'fill_blank'">
                                            <div class="mt-2">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300">Answer:
                                                </div>
                                                <span class="text-gray-700 dark:text-gray-400"
                                                    x-text="item.answer"></span>
                                            </div>
                                        </template>

                                        <!-- Open Ended Type -->
                                        <template x-if="item.type === 'open_ended'">
                                            <div class="mt-2">
                                                <div class="font-semibold text-gray-700 dark:text-gray-300">Answer:
                                                </div>
                                                <span class="text-gray-700 dark:text-gray-400"
                                                    x-text="item.answer"></span>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
