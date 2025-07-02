<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="py-6" x-data="questionPage({{ Js::from(\App\Enum\QuestionTypes::names()) }})">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-900 sm:rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Manage All Questions</h2>
                    <div class="flex space-x-2">
                        <button @click="showExcelModal = true"
                            class="px-2 py-1 text-xs text-green-700 border border-green-700 rounded hover:bg-green-100">
                            📥 Import Questions
                        </button>
                        <a href="{{ route('admin.questions.create') }}"
                            class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                            + Add Question
                        </a>
                    </div>
                </div>

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
                        @foreach ($questions as $index => $question)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $questions->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <div
                                        class="p-2 overflow-y-auto bg-white border border-gray-300 rounded max-h-40 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="prose dark:prose-invert max-w-none">{!! $question->content !!}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 capitalize dark:text-gray-100">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded dark:bg-gray-800">{{ str_replace('_', ' ', $question->type) }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->level->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    {{ $question->subject->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100"
                                    title="{{ $question?->topic?->name ?? '-' }}">
                                    {{ str($question?->topic?->name ?? '-')->limit(20) }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                    <button @click="openModal({{ $question->toJson() }})"
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
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6">{{ $questions->links('pagination::tailwind') }}</div>
            </div>
        </div>

        {{-- Modal Inclusion --}}
        @include('components.question-type.question-modal')
        @include('components.question-type.excel-modal')
    </div>

    <script>
        function questionPage(types) {
            return {
                showModal: false,
                showExcelModal: false,
                activeQuestion: null,
                selectedType: '',
                types: types,
                openModal(question) {
                    this.activeQuestion = question;
                    this.showModal = true;
                }
            }
        }
    </script>
</x-app-layout>
