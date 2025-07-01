<x-app-layout>
    <div class="py-6" x-data="{ showModal: false, activeQuestion: null }">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <a href="{{ route('admin.questions.create') }}"
                        class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Add Question
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <a href="{{ route('admin.questions.index', ['tab' => 'all', 'search' => request('search')]) }}"
                        class="px-4 py-2 text-sm font-semibold border rounded {{ request('tab', 'all') === 'all' ? 'bg-blue-700 text-white' : 'text-gray-800 hover:bg-blue-700 hover:text-white' }}">
                        All
                    </a>
                    @foreach ($questionTypes as $type => $label)
                        <a href="{{ route('admin.questions.index', ['tab' => $type, 'search' => request('search')]) }}"
                            class="px-4 py-2 text-sm font-semibold border rounded capitalize {{ request('tab') === $type ? 'bg-blue-700 text-white' : 'text-gray-800 hover:bg-blue-700 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.questions.index') }}" class="mb-4">
                    <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by question or type..."
                        class="w-full px-4 py-2 text-sm border rounded dark:bg-gray-800 dark:text-white" />
                </form>

                <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-800">
                        <tr>
                            <th class="px-4 py-3 font-medium text-white">#</th>
                            <th class="px-4 py-3 font-medium text-white">Question</th>
                            <th class="px-4 py-3 font-medium text-white">Type</th>
                            <th class="px-4 py-3 font-medium text-white">Level</th>
                            <th class="px-4 py-3 font-medium text-white">Subject</th>
                            <th class="px-4 py-3 font-medium text-white">Topic</th>
                            <th class="px-4 py-3 font-medium text-white">Options</th>
                            <th class="px-4 py-3 font-medium text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:bg-gray-900 dark:divide-gray-800">
                        @forelse ($questions as $index => $question)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $questions->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <div
                                        class="p-2 overflow-y-auto bg-white border border-gray-300 rounded max-h-40 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="prose dark:prose-invert max-w-none">{!! $question->content !!}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 capitalize dark:text-gray-100">
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded dark:bg-gray-800">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->level->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->subject->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    title="{{ $question?->topic?->name ?? '-' }}">
                                    {{ str($question?->topic?->name ?? '-')->limit(20) ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <button @click="showModal = true; activeQuestion = {{ Js::from($question) }}"
                                        class="px-2 py-1 text-xs text-green-700 border border-green-700 rounded hover:bg-green-100">
                                        View Options
                                    </button>

                                </td>

                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.questions.edit', $question->id) }}"
                                            class="inline-block px-2 py-1 text-xs text-blue-700 border border-blue-700 rounded hover:bg-blue-50 dark:hover:bg-blue-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this question?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-block px-2 py-1 text-xs text-red-600 border border-red-600 rounded hover:bg-red-50 dark:hover:bg-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No
                                    questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $questions->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-4xl p-6 mx-2 bg-white rounded-lg shadow-lg dark:bg-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Question Options</h3>
                    <button @click="showModal = false; activeQuestion = null"
                        class="text-xl font-bold text-gray-500 hover:text-red-600">&times;</button>
                </div>

                <div x-show="showModal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
                    <div class="relative w-full max-w-4xl p-6 mx-4 bg-white shadow-2xl rounded-xl dark:bg-gray-900"
                        x-transition>
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-4 border-b dark:border-gray-700">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">🧠 Question Options</h2>
                            <button @click="showModal = false; activeQuestion = null"
                                class="text-2xl leading-none text-gray-400 hover:text-red-600">
                                &times;
                            </button>
                        </div>
                        <!-- Question Instruction -->
                        <template x-if="activeQuestion.metadata.instruction">
                            <div class="p-4 mb-4 border rounded bg-yellow-50 dark:bg-gray-800 dark:border-gray-700">
                                <h4 class="mb-1 text-sm font-semibold text-yellow-700 dark:text-yellow-300">Instruction
                                </h4>
                                <div class="text-sm text-gray-700 dark:text-gray-300"
                                    x-text="activeQuestion.metadata.instruction"></div>
                            </div>
                        </template>
                        <!-- Question Content -->
                        <div class="p-4 mb-4 border rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                            <h3 class="mb-2 text-base font-semibold text-gray-700 dark:text-gray-100">Question</h3>
                            <div class="prose dark:prose-invert max-w-none" x-html="activeQuestion.content"></div>
                        </div>
                        <!-- Content -->
                        <template x-if="activeQuestion">
                            <div class="space-y-6 overflow-y-auto max-h-[70vh] pr-2">

                                <!-- Type: MCQ -->
                                <template x-if="activeQuestion.type === 'mcq'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Options</h3>
                                        <ul class="space-y-2">
                                            <template x-for="option in activeQuestion.options" :key="option.id">
                                                <li class="flex items-start gap-2">
                                                    <span class="font-semibold" x-text="option.value + '.'"></span>
                                                    <span x-html="option.text"></span>
                                                    <template x-if="option.is_correct">
                                                        <span
                                                            class="ml-2 text-xs font-semibold text-green-600">(Correct)</span>
                                                    </template>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>

                                <!-- Type: TRUE_FALSE -->
                                <template x-if="activeQuestion.type === 'true_false'">
                                    <div class="space-y-2">
                                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Correct Answer</h3>
                                        <template x-for="choice in ['True', 'False']">
                                            <div
                                                :class="{
                                                    'text-green-600 font-bold': activeQuestion.metadata.answer
                                                        .choice ===
                                                        choice,
                                                    'text-gray-800 dark:text-white': activeQuestion.metadata
                                                        .answer.choice !== choice
                                                }">
                                                <span x-text="choice"></span>
                                                <template x-if="activeQuestion.metadata.answer.choice === choice">
                                                    <span> ✅</span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Type: FILL_IN_THE_BLANK -->
                                <template x-if="activeQuestion.type === 'fill_in_the_blank'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Blanks</h3>
                                        <template x-for="blank in activeQuestion.metadata.blanks"
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

                                <!-- Type: LINKING -->
                                <template x-if="activeQuestion.type === 'linking'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Linked Pairs
                                        </h3>
                                        <template x-for="pair in activeQuestion.metadata.answer"
                                            :key="pair.left.word + '_' + pair.right.word">
                                            <div
                                                class="flex items-center justify-between p-2 mb-1 bg-white border rounded dark:border-gray-700 dark:bg-gray-800">
                                                <div class="flex items-center space-x-2">
                                                    <template x-if="pair.left.match_type === 'image'">
                                                        <img :src="pair.left.image_uri"
                                                            class="w-10 h-10 border rounded">
                                                    </template>
                                                    <template x-if="pair.left.match_type !== 'image'">
                                                        <span x-text="pair.left.word"></span>
                                                    </template>
                                                </div>
                                                <span class="mx-3 text-gray-500">→</span>
                                                <div class="flex items-center space-x-2">
                                                    <template x-if="pair.right.match_type === 'image'">
                                                        <img :src="pair.right.image_uri"
                                                            class="w-10 h-10 border rounded">
                                                    </template>
                                                    <template x-if="pair.right.match_type !== 'image'">
                                                        <span x-text="pair.right.word"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Type: COMPREHENSION -->
                                <template x-if="activeQuestion.type === 'comprehension'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Sub-Questions
                                        </h3>
                                        <template x-for="(item, index) in activeQuestion.metadata.subquestions"
                                            :key="index">
                                            <div
                                                class="p-3 mb-2 border rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                                <div class="font-medium"
                                                    x-text="'Q' + (index + 1) + ': ' + item.question"></div>
                                                <div class="mt-1 text-green-600 dark:text-green-400">
                                                    <span class="font-semibold">Answer:</span> <span
                                                        x-text="item.answer"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Type: REARRANGING -->
                                <template x-if="activeQuestion.type === 'rearranging'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Available Words
                                        </h3>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="opt in activeQuestion.metadata.options">
                                                <span class="px-2 py-1 text-sm bg-gray-100 rounded dark:bg-gray-700"
                                                    x-text="opt.value"></span>
                                            </template>
                                        </div>

                                        <h3 class="mt-4 mb-2 font-semibold text-gray-700 dark:text-gray-200">Correct
                                            Order</h3>
                                        <ol class="list-decimal list-inside">
                                            <template x-for="word in activeQuestion.metadata.answer.answer">
                                                <li x-text="word"></li>
                                            </template>
                                        </ol>
                                    </div>
                                </template>

                                <!-- Type: EDITING -->
                                <template x-if="activeQuestion.type === 'editing'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Mistakes</h3>
                                        <template x-for="item in activeQuestion.metadata.questions"
                                            :key="item.wrong">
                                            <div
                                                class="p-2 bg-white border rounded dark:bg-gray-800 dark:border-gray-700">
                                                <span class="font-semibold text-red-600">Wrong:</span>
                                                <span x-text="item.wrong" class="mr-4"></span>
                                                <span class="font-semibold text-green-600">Correct:</span>
                                                <span x-text="item.correct"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Type: OPEN_CLOZE_WITH_OPTIONS -->
                                <template x-if="activeQuestion.type === 'open_cloze_with_options'">
                                    <div>
                                        <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Shared Options
                                        </h3>
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <template
                                                x-for="opt in activeQuestion.metadata.question_group.shared_options">
                                                <span
                                                    class="px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-200"
                                                    x-text="opt"></span>
                                            </template>
                                        </div>

                                        <template x-for="blank in activeQuestion.metadata.questions"
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

                                <!-- Type: OPEN_CLOZE_WITH_DROPDOWN_OPTIONS -->
                                <template x-if="activeQuestion.type === 'open_cloze_with_dropdown_options'">
                                    <div>
                                        <h3 class="mb-3 font-semibold text-gray-700 dark:text-gray-200">Dropdown Blanks
                                        </h3>
                                        <template x-for="blank in activeQuestion.metadata.questions">
                                            <div class="p-4 mb-2 border rounded dark:border-gray-700 dark:bg-gray-800">
                                                <h4 class="mb-1 font-semibold text-gray-700 dark:text-white"
                                                    x-text="'Blank #' + blank.blank_number"></h4>
                                                <ul
                                                    class="pl-5 text-gray-700 list-disc list-inside dark:text-gray-200">
                                                    <template x-for="opt in blank.options">
                                                        <li>
                                                            <span x-text="opt"></span>
                                                            <template x-if="opt === blank.correct_answer">
                                                                <span
                                                                    class="ml-2 font-semibold text-green-600">(Correct)</span>
                                                            </template>
                                                        </li>
                                                    </template>
                                                </ul>
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
    </div>
</x-app-layout>
