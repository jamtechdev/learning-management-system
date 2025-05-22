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
                                        {{ str_replace('_', ' ', $question->type) }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $question->level->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        {{ $question->subject->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
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
                                        @elseif($question->type === 'true_false')
                                            @if (!empty($question->metadata['answer']['choice']))
                                                <div class="mt-4">
                                                    <label class="block mb-2 font-semibold text-yellow-700">True And
                                                        False</label>
                                                    <ul class="text-yellow-800 list-disc list-inside">
                                                        @php
                                                            $correct = $question->metadata['answer']['choice'];
                                                        @endphp

                                                        <li
                                                            class="{{ $correct === 'True' ? 'font-bold text-green-600' : '' }}">
                                                            True
                                                            @if ($correct === 'True')
                                                                (Correct)
                                                            @endif
                                                        </li>
                                                        <li
                                                            class="{{ $correct === 'False' ? 'font-bold text-green-600' : '' }}">
                                                            False
                                                            @if ($correct === 'False')
                                                                (Correct)
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        @elseif($question->type === 'fill_blank')
                                            <span class="italic text-gray-600">Fill in the blanks</span>
                                            <div class="mt-2 space-y-2">
                                                @foreach ($question->metadata['blanks'] ?? [] as $blank)
                                                    <div>
                                                        <span class="font-medium text-gray-700">Blank
                                                            {{ $blank['blank_number'] }}:</span>
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
                                            </div>
                                        @elseif($question->type === 'linking')
                                            @if (!empty($question->metadata['answer']))
                                                <label
                                                    class="block mb-2 font-semibold text-yellow-700">Match The Following</label>
                                                <ol class="list-decimal list-inside">
                                                    @foreach ($question->metadata['answer'] as $pair)
                                                        <li class="mb-2">
                                                            <div>
                                                                <strong>Left:</strong>
                                                                @if ($pair['left']['match_type'] === 'image' && $pair['left']['image_uri'])
                                                                    <img src="{{ $pair['left']['image_uri'] }}"
                                                                        alt="Left image"
                                                                        class="inline-block w-auto h-12 mr-2">
                                                                @endif
                                                                {{ $pair['left']['word'] }}
                                                            </div>
                                                            <div>
                                                                <strong>Right:</strong>
                                                                @if ($pair['right']['match_type'] === 'image' && $pair['right']['image_uri'])
                                                                    <img src="{{ $pair['right']['image_uri'] }}"
                                                                        alt="Right image"
                                                                        class="inline-block w-auto h-12 mr-2">
                                                                @endif
                                                                {{ $pair['right']['word'] }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            @else
                                                <span class="italic text-gray-400">No options available</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif

                                        <!-- Edit & Delete buttons -->
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
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                        No questions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
