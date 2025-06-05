<x-app-layout>
    <div class="py-6" x-data="{ tab: 'all' }">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <a href="{{ route('admin.questions.create') }}"
                        class="inline-block px-4 py-2 text-sm font-medium text-white add-btn">
                        + Add Question
                    </a>
                </div>

                <!-- Tabs -->
                <div class="mb-4 space-x-2">
                    <button @click="tab = 'all'" :class="{ 'bg-blue-700 text-white': tab === 'all' }"
                        class="px-4 py-2 text-sm font-semibold text-gray-800 border rounded hover:bg-blue-700 hover:text-white">
                        All
                    </button>
                    <button @click="tab = 'mcq'" :class="{ 'bg-blue-700 text-white': tab === 'mcq' }"
                        class="px-4 py-2 text-sm font-semibold text-gray-800 border rounded hover:bg-blue-700 hover:text-white">
                        MCQ
                    </button>
                    <button @click="tab = 'fill_blank'" :class="{ 'bg-blue-700 text-white': tab === 'fill_blank' }"
                        class="px-4 py-2 text-sm font-semibold text-gray-800 border rounded hover:bg-blue-700 hover:text-white">
                        Fill in the Blank
                    </button>
                    <button @click="tab = 'true_false'" :class="{ 'bg-blue-700 text-white': tab === 'true_false' }"
                        class="px-4 py-2 text-sm font-semibold text-gray-800 border rounded hover:bg-blue-700 hover:text-white">
                        True / False
                    </button>
                    <button @click="tab = 'linking'" :class="{ 'bg-blue-700 text-white': tab === 'linking' }"
                        class="px-4 py-2 text-sm font-semibold text-gray-800 border rounded hover:bg-blue-700 hover:text-white">
                        Linking
                    </button>
                </div>

                <!-- Table -->
                <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-800">
                        <tr>
                            <th class="px-4 py-3 font-medium text-white">#</th>
                            <th class="px-4 py-3 font-medium text-white">Question</th>
                            <th class="px-4 py-3 font-medium text-white">Type</th>
                            <th class="px-4 py-3 font-medium text-white">Level</th>
                            <th class="px-4 py-3 font-medium text-white">Subject</th>
                            <th class="px-4 py-3 font-medium text-white">Options</th>
                            <th class="px-4 py-3 font-medium text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 dark:divide-gray-800 dark:bg-gray-900">
                        @forelse ($questions as $index => $question)
                            <tr x-show="tab === 'all' || tab === '{{ $question->type }}'">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <div class="prose dark:prose-invert max-w-none">{!! $question->content !!}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 capitalize dark:text-gray-100">
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded dark:bg-gray-800">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->level->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->subject->name ?? '-' }}</td>

                                <!-- Options -->
                                <td class="max-w-md px-4 py-3 overflow-y-auto text-gray-900 dark:text-gray-100">
                                    @if ($question->type === 'mcq')
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
                                    @elseif ($question->type === 'true_false')
                                        @php $correct = $question->metadata['answer']['choice'] ?? null; @endphp
                                        <div class="flex flex-col space-y-1">
                                            <span
                                                class="{{ $correct === 'True' ? 'text-green-600 font-semibold' : '' }}">True
                                                @if ($correct === 'True')
                                                    (Correct)
                                                @endif
                                            </span>
                                            <span
                                                class="{{ $correct === 'False' ? 'text-green-600 font-semibold' : '' }}">False
                                                @if ($correct === 'False')
                                                    (Correct)
                                                @endif
                                            </span>
                                        </div>
                                    @elseif ($question->type === 'fill_blank')
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
                                    @elseif ($question->type === 'linking')
                                        <div class="space-y-2">
                                            @foreach ($question->metadata['answer'] ?? [] as $pair)
                                                <div
                                                    class="flex items-center justify-between p-2 space-x-2 border rounded">
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
                                    @elseif ($question->type === 'comprehension')
                                        <div class="space-y-4">
                                            @foreach ($question->metadata['comprehension'] ?? [] as $comp)
                                                <div class="p-3 bg-gray-100 rounded shadow-sm dark:bg-gray-800">
                                                    <div class="mb-2 font-semibold">{{ $comp['question_name'] }}</div>
                                                    @foreach ($comp['blanks'] ?? [] as $blank)
                                                        <div class="mb-2 text-sm">
                                                            <div class="font-medium">Blank
                                                                {{ $blank['blank_number'] }}:</div>
                                                            <ul class="pl-4 list-disc">
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
                                            @endforeach
                                        </div>
                                    @elseif ($question->type === 'rearranging')
                                        <div>
                                            <div class="mb-2 font-semibold">Available words:</div>
                                            <ul class="flex flex-wrap gap-2">
                                                @foreach ($question->metadata['options'] as $opt)
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
                                    @elseif ($question->type === 'grammar_cloze_with_options')
                                        <div class="space-y-4">
                                            @php
                                                $sharedOptions =
                                                    $question->metadata['question_group']['shared_options'] ?? [];
                                                $questions = $question->metadata['questions'] ?? [];
                                            @endphp

                                            @foreach ($questions as $blank)
                                                <div>
                                                    <span class="font-semibold">Blank
                                                        {{ $blank['blank_number'] }}:</span>
                                                    <ul class="pl-4 list-disc">
                                                        @foreach ($sharedOptions as $option)
                                                            <li>
                                                                {{ $option }}
                                                                @if ($option === $blank['correct_answer'])
                                                                    <span
                                                                        class="font-semibold text-green-600">(Correct)</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="italic text-gray-400">No options available</span>
                                    @endif
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

                {{-- <div class="mt-6">
                    {{ $questions->links('pagination::tailwind') }}
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
