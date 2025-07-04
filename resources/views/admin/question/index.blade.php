<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="py-4" x-data="questionPage({{ Js::from($questionTypes ?? []) }})">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="py-3 bg-white card-header d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">Manage All Questions</h2>
                    <div class="flex-wrap gap-2 d-flex">
                        <button @click="showSampleModal = true" class="btn btn-outline-primary btn-sm">
                            📤 Download Sample
                        </button>
                        <button @click="showExcelModal = true" class="btn btn-outline-success btn-sm">
                            📥 Import Questions
                        </button>
                        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Question
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="pb-0 card-body border-bottom">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto d-flex align-items-center">
                            <label for="typeFilter" class="mb-0 me-2 fw-bold">Type:</label>
                            <select id="typeFilter" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($questionTypes as $type)
                                    <option value="{{ $type }}">{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <label for="educationTypeFilter" class="mb-0 me-2 fw-bold">Education Type:</label>
                            <select id="educationTypeFilter" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <label for="levelFilter" class="mb-0 me-2 fw-bold">Level:</label>
                            <select id="levelFilter" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">  {{ $level->name . ' (' . $level->education_type . ')' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <label for="subjectFilter" class="mb-0 me-2 fw-bold">Subject:</label>
                            <select id="subjectFilter" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <label for="topicFilter" class="mb-0 me-2 fw-bold">Topic:</label>
                            <select id="topicFilter" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button id="filterReset" class="btn btn-secondary btn-sm">Reset Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="pt-0 card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(
                            [
                                'class' => 'table table-striped table-hover table-bordered w-100',
                                'id' => 'question-table',
                            ],
                            true,
                        ) !!}
                    </div>
                </div>

            </div>
        </div>

        {{-- Modals --}}
        @include('components.question-type.question-modal')
        @include('components.question-type.excel-modal')
        @include('components.question-type.sample-excel')
    </div>

    {{-- AlpineJS --}}
    <script>
        function questionPage(types) {
            return {
                showModal: false,
                showExcelModal: false,
                showSampleModal: false,
                showSampleSuccess: false,
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

    @push('scripts')
        {!! $dataTable->scripts() !!}

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = $('#question-table').DataTable();

                function reloadTable() {
                    const type = $('#typeFilter').val();
                    const educationType = $('#educationTypeFilter').val();
                    const level = $('#levelFilter').val();
                    const subject = $('#subjectFilter').val();
                    const topic = $('#topicFilter').val();

                    const url = new URL(table.ajax.url());

                    if (type) {
                        url.searchParams.set('type', type);
                    } else {
                        url.searchParams.delete('type');
                    }

                    if (educationType) {
                        url.searchParams.set('education_type', educationType);
                    } else {
                        url.searchParams.delete('education_type');
                    }

                    if (level) {
                        url.searchParams.set('level_id', level);
                    } else {
                        url.searchParams.delete('level_id');
                    }

                    if (subject) {
                        url.searchParams.set('subject_id', subject);
                    } else {
                        url.searchParams.delete('subject_id');
                    }

                    if (topic) {
                        url.searchParams.set('topic_id', topic);
                    } else {
                        url.searchParams.delete('topic_id');
                    }

                    table.ajax.url(url.toString()).load();
                }

                $('#typeFilter, #educationTypeFilter, #levelFilter, #subjectFilter, #topicFilter').on('change', reloadTable);

                $('#filterReset').on('click', function() {
                    $('#typeFilter').val('');
                    $('#educationTypeFilter').val('');
                    $('#levelFilter').val('');
                    $('#subjectFilter').val('');
                    $('#topicFilter').val('');
                    reloadTable();
                });
            });
        </script>
    @endpush
</x-app-layout>
