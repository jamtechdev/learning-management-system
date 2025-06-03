<x-app-layout>
    <div class="max-w-4xl px-4 py-10 mx-auto" x-data="questionAssign()">
        <h2 class="mb-8 text-3xl font-bold text-gray-800">📘 Assign Questions to Assessment</h2>

        <div class="p-8 space-y-10 bg-white rounded-lg shadow-lg">
            {{-- Step Form --}}
            <form method="POST" action="{{ route('admin.assignments.questionstore') }}" class="space-y-8">
                @csrf

                {{-- Hidden Fields --}}
                <input type="hidden" name="assessment_id" value="{{ $assignment->id }}">
                <input type="hidden" name="student_id" :value="studentId">
                {{-- <input type="hidden" name="subject_id" :value="subjectId"> --}}
                {{-- <input type="hidden" name="type" :value="type"> --}}

                {{-- Step 1: Select Student --}}
                <div>
                    <label class="block mb-2 text-lg font-semibold text-gray-700">👤 Select Student</label>
                    <select x-model="studentId" @change="onStudentChange()"
                        class="w-full px-4 py-3 border rounded-lg shadow-sm">
                        <option value="">-- Choose Student --</option>
                        <template x-for="s in students" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Step 2: Select Subject --}}
                <template x-if="filteredSubjects.length > 0">
                    <div>
                        <label class="block mb-2 text-lg font-semibold text-gray-700">📚 Select Subject</label>
                        <select x-model="subjectId" @change="filterQuestions()"
                            class="w-full px-4 py-3 border rounded-lg shadow-sm">
                            <option value="">-- Choose Subject --</option>
                            <template x-for="sub in filteredSubjects" :key="sub.id">
                                <option :value="sub.id" x-text="sub.name"></option>
                            </template>
                        </select>
                    </div>
                </template>

                {{-- Step 3: Select Type --}}
                <template x-if="subjectId">
                    <div>
                        <label class="block mb-2 text-lg font-semibold text-gray-700">🧩 Select Question Type</label>
                        <select x-model="type" @change="filterQuestions()"
                            class="w-full px-4 py-3 border rounded-lg shadow-sm">
                            <option value="">-- Choose Type --</option>
                            <template x-for="t in types" :key="t">
                                <option :value="t" x-text="t.replace('_', ' ').toUpperCase()"></option>
                            </template>
                        </select>
                    </div>
                </template>

                {{-- Step 4: Questions --}}
                <template x-if="filteredQuestions.length > 0">
                    <div>
                        <h3 class="mb-3 text-lg font-semibold text-gray-800">✅ Select Questions</h3>
                        <div
                            class="p-4 space-y-2 overflow-y-auto border border-gray-200 rounded-lg bg-gray-50 max-h-96">
                            <template x-for="q in filteredQuestions" :key="q.id">
                                <label class="flex items-start space-x-3">
                                    <input type="checkbox" :value="q.id" name="question_ids[]"
                                        class="mt-1 text-indigo-600 border-gray-300 rounded">
                                    <span class="text-gray-700" x-html="q.content"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Submit --}}
                <div x-show="filteredQuestions.length > 0">
                    <button type="submit"
                        class="px-6 py-3 font-semibold text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700">
                        🚀 Assign Selected Questions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function questionAssign() {
            return {
                studentId: '',
                subjectId: '',
                type: '',
                students: @json($students->map(fn($s) => ['id' => $s->id, 'name' => $s->first_name . ' ' . $s->last_name, 'level_id' => $s->student_level])),
                allSubjects: @json($subjects),
                allQuestions: @json($questions),
                types: @json($types),

                filteredSubjects: [],
                filteredQuestions: [],

                onStudentChange() {
                    const level = this.students.find(s => s.id == this.studentId)?.level_id;
                    this.filteredSubjects = this.allSubjects.filter(sub => sub.level_id == level);
                    this.subjectId = '';
                    this.type = '';
                    this.filteredQuestions = [];
                },

                filterQuestions() {
                    const student = this.students.find(s => s.id == this.studentId);
                    if (!student) return;

                    this.filteredQuestions = this.allQuestions.filter(q =>
                        q.level_id == student.level_id &&
                        q.subject_id == this.subjectId &&
                        q.type == this.type
                    );
                }
            };
        }
    </script>
</x-app-layout>
