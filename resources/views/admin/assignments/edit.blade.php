<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="container py-8 mx-auto" x-data="assignmentForm()">
        <div class="p-8 bg-white rounded-lg shadow-lg">
            <div class="pb-6 mb-6 border-b">
                <h2 class="text-3xl font-semibold text-gray-800">Edit Assignment</h2>
            </div>

            <form method="POST" action="{{ route('admin.assignments.update', $assignment->id) }}" @submit.prevent="submitForm">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-6 col-12">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" name="title" x-model="form.title" required
                        class="mt-2 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                        :value="form.title">
                    @error('title')
                        <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student -->
                <div class="mb-6 col-12">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Select Student</label>
                    <select id="student_id" name="student_id" x-model="form.student_id" @change="onStudentChange"
                        class="mt-2 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('student_id') border-red-500 @enderror"
                        required>
                        <option value="">Select Student</option>
                        <template x-for="student in students" :key="student.id">
                            <option :value="student.id"
                                x-text="`${student.first_name} (Parent: ${student.parent.first_name || 'N/A'})`"
                                :data-level="student.student_level"
                                :data-education-type="JSON.stringify(student.student_type)"
                                :selected="form.student_id == student.id">
                            </option>
                        </template>
                    </select>
                    @error('student_id')
                        <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject and Choose Questions Button in the Same Row -->
                <div class="grid gap-4 mb-6"
                    :class="{ 'grid-cols-1': !form.subject_id, 'grid-cols-2': form.subject_id }">
                    <!-- Subject -->
                    <div class="col-span-1">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                        <select id="subject_id" name="subject_id" x-model="form.subject_id" @change="onSubjectChange"
                            class="mt-2 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('subject_id') border-red-500 @enderror"
                            required>
                            <option value="">Select Subject</option>
                    <template x-for="subject in filteredSubjects" :key="subject.id">
                        <option :value="subject.id" x-text="subject.name" :selected="form.subject_id == subject.id"></option>
                    </template>
                        </select>
                        @error('subject_id')
                            <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Choose Questions Button -->
                    <div class="col-span-1">
                        <label for="subject_id" class="block mb-2 text-sm font-medium text-gray-700">Choose questions</label>
                        <template x-if="form.subject_id">
                            <button type="button"
                                class="w-full px-4 py-3 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                @click="showQuestionModal = true">
                                Choose Questions
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="mb-6 col-12">
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" id="due_date" name="due_date" x-model="form.due_date" min="{{date('Y-m-d')}}"
                        class="mt-2 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('due_date') border-red-500 @enderror"
                         required>
                    @error('due_date')
                        <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6 col-12">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" x-model="form.description" rows="4"
                        class="mt-2 p-3 w-full border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror" x-text="form.description"></textarea>
                    @error('description')
                        <div class="mt-2 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit and Cancel -->
                <div class="flex items-center justify-between col-12">
                    <input type="hidden" name="question_ids" :value="form.question_ids" />
                    <button type="submit"
                        class="px-6 py-3 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Update Assignment
                    </button>
                    <a href="{{ route('admin.assignments.index') }} "
                        class="font-medium text-gray-600 hover:text-indigo-600">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Modal Background Overlay -->
        <div x-show="showQuestionModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden bg-gray-500 bg-opacity-75">

            <!-- Modal Content -->
            <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-lg overflow-y-auto max-h-[80vh]">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b">
                    <h5 class="text-2xl font-semibold text-gray-800">Select Questions</h5>
                    <button type="button" class="text-2xl text-gray-500 hover:text-gray-700"
                        @click="showQuestionModal = false">&times;</button>
                </div>

                <!-- Scrollable Questions List -->
                <div class="mb-4 space-y-4 overflow-y-auto max-h-80">
                    <template x-if="questions.length > 0">
                        <ul class="space-y-4">
                            <template x-for="question in questions" :key="question.id">
                                <li
                                    class="flex items-center justify-between px-4 py-3 border-b rounded-lg bg-gray-50 hover:bg-gray-100">
                                    <div class="flex-1">
                                        <p x-text="question.content" class="text-lg font-medium text-gray-700"></p>
                                        <span x-text="question.type" class="text-sm text-gray-500"></span>
                                    </div>
                                    <input type="checkbox" :value="question.id" x-model="selectedQuestionIds"
                                        class="w-6 h-6 text-indigo-500 border-gray-300 rounded form-checkbox focus:ring-indigo-500">
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="questions.length === 0">
                        <p class="text-center text-gray-600">No questions available for the selected subject.</p>
                    </template>
                </div>

                <!-- Modal Footer with Small Buttons -->
                <div class="flex justify-between pt-4 mt-4 border-t">
                    <button type="button"
                        class="px-4 py-2 text-sm text-gray-800 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none"
                        @click="showQuestionModal = false">Close</button>
                    <button type="button"
                        class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none"
                        @click="applySelectedQuestions">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function assignmentForm() {
            return {
                form: {
                    title: '{{ $assignment->title }}',
                    student_id: '{{ $assignment->student_id }}',
                    subject_id: '{{ $assignment->subject_id }}',
                    description: `{{ $assignment->description }}`,
                    due_date: '{{ \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d') }}',
                    question_ids: '{{ $assignment->questions->pluck('id')->implode(',') }}',
                },
                showQuestionModal: false,
                questions: @json($questions), // Questions associated with the assignment
                selectedQuestionIds: @json($assignment->questions->pluck('id')), // Pre-select assigned questions
                studentLevel: '{{ optional($children->firstWhere("id", $assignment->student_id))->student_level }}',
                filteredSubjects: [],
                students: @json($children), // Pass students to Alpine.js

                init() {
                    // Initialize filteredSubjects with all subjects on page load to ensure assignment's subject is included
                    this.filteredSubjects = @json($subjects);
                },

                // Fetch filtered subjects based on the student level
                onStudentChange() {
                    const selectedStudent = this.students.find(student => student.id == this.form.student_id);
                    this.studentLevel = selectedStudent ? selectedStudent.student_level : null;

                    this.filteredSubjects = @json($subjects).filter(subject => subject.level_id === this.studentLevel);
                },

                // Fetch questions when subject is selected
                onSubjectChange() {
                    this.selectedQuestionIds = [];
                    this.form.question_ids = '';
                    if (this.form.subject_id) {
                        this.fetchQuestions();
                    } else {
                        this.questions = [];
                    }
                },

                // Fetch and filter questions by selected subject
                fetchQuestions() {
                    this.questions = @json($questions).filter(question => question.subject_id == this.form.subject_id);
                },

                applySelectedQuestions() {
                    this.form.question_ids = this.selectedQuestionIds.join(',');
                    this.showQuestionModal = false;
                },

                submitForm() {
                    this.$root.querySelector('form').submit();
                }
            };
        }
    </script>
</x-app-layout>
