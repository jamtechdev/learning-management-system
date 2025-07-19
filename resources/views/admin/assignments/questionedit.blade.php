<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg" x-data="questionSelector()" x-init="init()">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Edit Assessment Questions
                </h2>

                <form action="{{ route('admin.assignments.questionupdate', $assessmentQuestion->id) }}" method="POST" @submit="prepareSubmit">
                    @csrf
                    @method('PUT')

                    @if (isset($user))
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                    @endif
                    <input type="hidden" name="assessment_id" value="{{ $assessmentQuestion->assessment_id }}">

                    <!-- Question Type Selector -->
                    <div class="mb-4">
                        <label for="type" class="block mb-1 font-medium text-gray-700">Select Question Type</label>
                        <select id="type" x-model="selectedType" class="w-full px-4 py-2 border rounded-md focus:ring focus:ring-indigo-200">
                            <option value="">-- Select Type --</option>
                            <template x-for="type in types" :key="type">
                                <option :value="type" x-text="type"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Questions List -->
                    <div class="p-4 overflow-y-auto border rounded-md max-h-96">
                        <template x-if="filteredQuestions.length === 0">
                            <p class="italic text-gray-500">No questions available for this type.</p>
                        </template>
                        <template x-for="question in filteredQuestions" :key="question.id">
                            <label class="flex items-start p-2 space-x-3 rounded cursor-pointer hover:bg-indigo-50">
                                <input
                                    type="checkbox"
                                    :value="question.id"
                                    x-model="selectedQuestions"
                                    class="w-5 h-5 mt-1 text-indigo-600 form-checkbox"
                                >
                                <div class="text-gray-800" x-html="question.content"></div>
                            </label>
                        </template>
                    </div>

                    <!-- Hidden inputs for selected questions -->
                    <template x-for="id in selectedQuestions" :key="id">
                        <input type="hidden" name="question_ids[]" :value="id" />
                    </template>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                                class="px-6 py-2 text-white bg-[#3e80f9] rounded-md bg-[#f7941e] focus:outline-none focus:ring focus:ring-indigo-300">
                            Update Selected Questions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function questionSelector() {
            return {
                selectedType: '',
                selectedQuestions: @json($assignedQuestionIds ?? []), // pre-check assigned question IDs
                questions: @json($questions),
                get types() {
                    return [...new Set(this.questions.map(q => q.type))];
                },
                get filteredQuestions() {
                    if (!this.selectedType) return this.questions;

                    return this.questions
                        .filter(q =>
                            q.type === this.selectedType ||
                            this.selectedQuestions.includes(q.id)
                        )
                        .sort((a, b) => {
                            return this.selectedQuestions.includes(b.id) - this.selectedQuestions.includes(a.id);
                        });
                },
                prepareSubmit(event) {
                    if (!this.selectedType) {
                        alert('Please select a question type.');
                        event.preventDefault();
                        return;
                    }
                    if (this.selectedQuestions.length === 0) {
                        alert('Please select at least one question.');
                        event.preventDefault();
                        return;
                    }
                },
                init() {
                    if (this.selectedQuestions.length > 0) {
                        let firstAssignedId = this.selectedQuestions[0];
                        let q = this.questions.find(q => q.id == firstAssignedId);
                        if (q) this.selectedType = q.type;
                    }
                }
            };
        }
    </script>
</x-app-layout>
