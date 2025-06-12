<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <a href="{{ route('admin.questions.create') }}"
                        class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                        + Add Question
                    </a>
                </div>

                <!-- Tabs -->
                <div class="mb-4 space-x-2">
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

                <!-- Search Bar -->
                <form method="GET" action="{{ route('admin.questions.index') }}" class="mb-4">
                    <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by question or type..."
                        class="w-full px-4 py-2 text-sm border rounded dark:bg-gray-800 dark:text-white" />
                </form>

                <!-- Questions Table -->
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
                    <tbody class="bg-white divide-y divide-gray-100 dark:divide-gray-800 dark:bg-gray-900">
                        @forelse ($questions as $index => $question)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $questions->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <div
                                        class="p-2 overflow-y-auto bg-white border border-gray-300 rounded max-h-40 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="prose dark:prose-invert max-w-none">
                                            {!! $question->content !!}
                                        </div>
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

                                <!-- Options column: render based on question type -->
                                <td class="max-w-md px-4 py-3 overflow-y-auto text-gray-900 dark:text-gray-100">
                                    @switch($question->type)
                                        @case(\App\Enum\QuestionTypes::MCQ)
                                            <ul class="space-y-1">
                                                @foreach ($question->options as $option)
                                                    <li class="flex items-start">
                                                        <span class="font-semibold">{{ $option->value }}.</span>
                                                        <span class="ml-1">{!! $option->text !!}</span>
                                                        @if ($option->is_correct)
                                                            <span
                                                                class="ml-2 text-xs font-semibold text-green-600">(Correct)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @break

                                        @case(\App\Enum\QuestionTypes::TRUE_FALSE)
                                            @php $correct = $question->metadata['answer']['choice'] ?? null; @endphp
                                            <div class="flex flex-col space-y-1">
                                                <span class="{{ $correct === 'True' ? 'text-green-600 font-semibold' : '' }}">
                                                    True @if ($correct === 'True')
                                                        (Correct)
                                                    @endif
                                                </span>
                                                <span class="{{ $correct === 'False' ? 'text-green-600 font-semibold' : '' }}">
                                                    False @if ($correct === 'False')
                                                        (Correct)
                                                    @endif
                                                </span>
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::FILL_IN_THE_BLANK)
                                            <div class="space-y-2">
                                                @foreach ($question->metadata['blanks'] ?? [] as $blank)
                                                    <div>
                                                        <span class="text-sm font-semibold">Blank
                                                            {{ $blank['blank_number'] }}:</span>
                                                        <ul class="pl-4 text-sm list-disc">
                                                            @foreach ($blank['options'] as $option)
                                                                <li>
                                                                    {{ $option }}
                                                                    @if ($option === $blank['answer'])
                                                                        <span
                                                                            class="font-semibold text-green-600">(Correct)</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::LINKING)
                                            <div class="space-y-2">
                                                @foreach ($question->metadata['answer'] ?? [] as $pair)
                                                    <div class="flex items-center justify-between p-2 space-x-2 border rounded">
                                                        <div class="flex items-center space-x-2">
                                                            @if ($pair['left']['match_type'] === 'image')
                                                                <img src="{{ $pair['left']['image_uri'] }}"
                                                                    class="w-10 h-10 border rounded" />
                                                            @else
                                                                <span>{{ $pair['left']['word'] }}</span>
                                                            @endif
                                                        </div>
                                                        <span class="text-sm text-gray-500">→</span>
                                                        <div class="flex items-center space-x-2">
                                                            @if ($pair['right']['match_type'] === 'image')
                                                                <img src="{{ $pair['right']['image_uri'] }}"
                                                                    class="w-10 h-10 border rounded" />
                                                            @else
                                                                <span>{{ $pair['right']['word'] }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::COMPREHENSION)
                                            <div class="p-4 space-y-3 bg-gray-100 rounded shadow dark:bg-gray-900">
                                                <h4 class="font-semibold text-md">Questions & Answers</h4>
                                                <ol class="space-y-2 list-decimal list-inside">
                                                    @foreach ($question->metadata['subquestions'] ?? [] as $index => $item)
                                                        <li>
                                                            <div class="font-medium dark:text-gray-200">
                                                                {{ $item['ques' . ($index + 1)] ?? 'Question ' . ($index + 1) }}
                                                            </div>
                                                            <div class="pl-4 text-sm text-gray-700 dark:text-gray-300">
                                                                <span class="font-semibold">Answer:</span>
                                                                {{ $item['answer'] ?? '-' }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::REARRANGING)
                                            <div>
                                                <div class="mb-2 font-semibold">Available words:</div>
                                                <ul class="flex flex-wrap gap-2">
                                                    @foreach ($question->metadata['options'] ?? [] as $opt)
                                                        <li
                                                            class="px-2 py-1 text-sm bg-gray-100 border rounded dark:bg-gray-800">
                                                            {{ $opt['value'] }}
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <div class="mt-4 mb-1 font-semibold">Correct order:</div>
                                                <ol class="list-decimal list-inside">
                                                    @foreach ($question->metadata['answer']['answer'] ?? [] as $word)
                                                        <li>{{ $word }}</li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::OPEN_CLOZE_WITH_OPTIONS)
                                            <div class="space-y-4">
                                                @foreach ($question->metadata['questions'] ?? [] as $blank)
                                                    <div class="p-2 border rounded dark:border-gray-700">
                                                        <span class="font-semibold">Blank {{ $blank['blank_number'] }}:</span>
                                                        <span
                                                            class="font-medium text-green-600">{{ $blank['correct_answer'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @break

                                        @case(\App\Enum\QuestionTypes::EDITING)
                                            @php $mistakes = $question->metadata['questions'] ?? []; @endphp
                                            @if (count($mistakes))
                                                <div class="space-y-2">
                                                    <h4 class="font-semibold text-gray-800 dark:text-white">Mistakes:</h4>
                                                    @foreach ($mistakes as $item)
                                                        <div class="p-2 border rounded dark:border-gray-700">
                                                            <span class="font-semibold text-red-600">Wrong:</span>
                                                            <span class="mr-4">{{ $item['wrong'] }}</span>
                                                            <span class="font-semibold text-green-600">Correct:</span>
                                                            <span>{{ $item['correct'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @break

                                        @case(\App\Enum\QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS)
                                            @php $items = $question->metadata['questions'] ?? []; @endphp
                                            @if (count($items))
                                                <div x-data="{ open: false }" class="space-y-2">
                                                    <button @click="open = !open"
                                                        class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left text-blue-600 border border-blue-200 rounded bg-blue-50 dark:bg-gray-800 dark:text-blue-400">
                                                        <span
                                                            x-text="'Cloze Questions (Click to ' + (open ? 'Hide' : 'Show') + ')'"></span>
                                                        <svg :class="{ 'rotate-180': open }"
                                                            class="w-4 h-4 transition-transform duration-300" fill="none"
                                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="open" x-transition
                                                        class="mt-2 overflow-y-auto space-y-3 max-h-[400px] pr-2">
                                                        @foreach ($items as $index => $item)
                                                            <div
                                                                class="p-4 border rounded shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                                                <div class="mb-2 font-semibold dark:text-white">Blank
                                                                    #{{ $item['blank_number'] }}</div>
                                                                <ul class="ml-6 text-gray-600 list-disc dark:text-gray-200">
                                                                    @foreach ($item['options'] as $option)
                                                                        <li>
                                                                            {{ $option }}
                                                                            @if ($option === $item['correct_answer'])
                                                                                <span
                                                                                    class="font-semibold text-green-600">(Correct)</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @break

                                        @default
                                            <span class="italic text-gray-400">No options available</span>
                                    @endswitch
                                </td>

                                <!-- Actions -->
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
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        No questions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $questions->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
