<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <a href="{{ route('admin.questions.create') }}"
                       class="inline-block px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700">
                        + Add
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
                                    <td class="px-4 py-3 text-gray-900 capitalize dark:text-gray-100">{{ str_replace('_', ' ', $question->type) }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $question->level->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $question->subject->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        @if($question->type === 'mcq' || $question->type === 'image_mcq')
                                            <ul class="list-disc list-inside">
                                                @foreach ($question->options as $option)
                                                    <li>
                                                        {{ $option->text }}
                                                        @if($option->is_correct)
                                                            <span class="font-semibold text-green-500">(Correct)</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif($question->type === 'true_false')
                                            <span>{{ $question->options[0]->text ?? 'True' }} / {{ $question->options[1]->text ?? 'False' }}</span>
                                        @elseif($question->type === 'fill_blank')
                                            <span class="italic text-gray-600">(Answer inside blank)</span>
                                        @elseif($question->type === 'rearrange')
                                            <ul class="list-decimal list-inside">
                                                @foreach ($question->options as $option)
                                                    <li>{{ $option->text }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
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
