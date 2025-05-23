<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <a href="{{ route('admin.questions.create') }}"
                        class="inline-block px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">
                        + Add Question
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="w-1/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">#</th>
                                <th class="w-3/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Question</th>
                                <th class="w-1/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Type</th>
                                <th class="w-2/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Level</th>
                                <th class="w-2/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Subject</th>
                                <th class="w-3/12 px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Options</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 dark:divide-gray-800 dark:bg-gray-900">
                            @forelse ($questions as $index => $question)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $question->content }}</td>
                                    <td class="px-4 py-3 text-gray-900 capitalize dark:text-gray-100">
                                        {{ str_replace('_', ' ', $question->type) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $question->level->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $question->subject->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">

                                        {{-- MCQ --}}
                                        @if ($question->type === 'mcq')
                                            <label class="block mb-2 font-semibold text-yellow-700">Options</label>
                                            <ul class="list-disc list-inside">
                                                @foreach ($question->options as $option)
                                                    <li>
                                                        {{ $option->value }}. {{ $option->text }}
                                                        @if ($option->is_correct)
                                                            <span class="font-semibold text-green-500">(Correct)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>

                                            {{-- True/False --}}
                                        @elseif ($question->type === 'true_false')
                                            @php $correct = $question->metadata['answer']['choice'] ?? null; @endphp
                                            <label class="block mb-2 font-semibold text-yellow-700">True / False</label>
                                            <ul class="list-disc list-inside">
                                                <li class="{{ $correct === 'True' ? 'text-green-600 font-bold' : '' }}">
                                                    True @if ($correct === 'True')
                                                        (Correct)
                                                    @endif
                                                </li>
                                                <li
                                                    class="{{ $correct === 'False' ? 'text-green-600 font-bold' : '' }}">
                                                    False @if ($correct === 'False')
                                                        (Correct)
                                                    @endif
                                                </li>
                                            </ul>

                                            {{-- Fill in the blanks --}}
                                        @elseif ($question->type === 'fill_blank')
                                            <label class="block mb-2 font-semibold text-yellow-700">Fill in the
                                                Blanks</label>
                                            @foreach ($question->metadata['blanks'] ?? [] as $blank)
                                                <div class="mb-2">
                                                    <span class="text-sm font-medium">
                                                        Blank {{ $blank['blank_number'] ?? 'N/A' }}:
                                                    </span>

                                                    <ul class="ml-4 list-disc list-inside">
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

                                            {{-- Linking / Match the following --}}
                                        @elseif ($question->type === 'linking')
                                            <label class="block mb-2 font-semibold text-yellow-700">Match the
                                                Following</label>
                                            @if (!empty($question->metadata['answer']))
                                                <div class="space-y-4">
                                                    @foreach ($question->metadata['answer'] as $pair)
                                                        <div
                                                            class="flex items-center justify-between p-3 transition-all bg-white border shadow rounded-xl hover:shadow-lg">

                                                            {{-- Left side --}}
                                                            <div class="flex items-center w-5/12 space-x-3">
                                                                @if ($pair['left']['match_type'] === 'image' && $pair['left']['image_uri'])
                                                                    <img src="{{ $pair['left']['image_uri'] }}"
                                                                        class="object-cover border rounded-md w-14 h-14" />
                                                                @endif
                                                                @if (!empty($pair['left']['word']))
                                                                    <span
                                                                        class="text-sm font-medium text-gray-800">{{ $pair['left']['word'] }}</span>
                                                                @endif
                                                            </div>

                                                            {{-- Arrow --}}
                                                            <div
                                                                class="hidden w-2/12 text-xl font-bold text-center text-yellow-600 sm:block">
                                                                →</div>

                                                            {{-- Right side --}}
                                                            <div
                                                                class="flex items-center justify-end w-5/12 space-x-3 sm:justify-start">
                                                                @if ($pair['right']['match_type'] === 'image' && $pair['right']['image_uri'])
                                                                    <img src="{{ $pair['right']['image_uri'] }}"
                                                                        class="object-cover border rounded-md w-14 h-14" />
                                                                @endif
                                                                @if (!empty($pair['right']['word']))
                                                                    <span
                                                                        class="text-sm font-medium text-gray-800">{{ $pair['right']['word'] }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="italic text-gray-400">No matching pairs available</p>
                                            @endif

                                            {{-- Default fallback --}}
                                        @else
                                            <span class="italic text-gray-400">No display format available</span>
                                        @endif

                                        {{-- Edit & Delete buttons --}}
                                        <div class="flex mt-4 space-x-2">
                                            <a href="{{ route('admin.questions.edit', $question->id) }}"
                                                class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.questions.destroy', $question->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No
                                        questions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
