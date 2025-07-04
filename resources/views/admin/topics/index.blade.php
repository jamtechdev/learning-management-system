<x-app-layout>
    <div class="py-4">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="py-3 bg-white card-header d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">Manage Question Topic Record</h2>
                    <a href="{{ route('admin.topics.create') }}" class="btn btn-primary btn-sm">
                        + Add Topic
                    </a>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="filterEducationType" class="form-label">Education Type</label>
                            <select id="filterEducationType" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterLevel" class="form-label">Level</label>
                            <select id="filterLevel" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}"
                                        data-education-type="{{ $level->education_type }}">
                                        {{ $level->name . ' (' . $level->education_type . ')' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterSubject" class="form-label">Subject</label>
                            <select id="filterSubject" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterTopic" class="form-label">Topic</label>
                            <select id="filterTopic" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->name }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="filterReset" class="btn btn-secondary btn-sm w-100">Reset Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(
                            [
                                'class' => 'table table-striped table-hover table-bordered w-100',
                                'id' => 'questiontopic-table',
                            ],
                            true,
                        ) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
        {!! $dataTable->scripts() !!}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const table = $('#questiontopic-table').DataTable();

                function filterLevelOptions() {
                    const educationType = $('#filterEducationType').val();
                    $('#filterLevel option').each(function() {
                        const optionEducationType = $(this).data('education-type');
                        if (!educationType || optionEducationType === educationType) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }

                    function reloadTable() {
                        const topicId = $('#filterTopic').val();
                        const subjectId = $('#filterSubject').val();
                        const educationType = $('#filterEducationType').val();
                        const level = $('#filterLevel').val();

                        const url = new URL(table.ajax.url());
                        if (topicId) {
                            url.searchParams.set('topic_id', topicId);
                        } else {
                            url.searchParams.delete('topic_id');
                        }

                        if (subjectId) {
                            url.searchParams.set('subject_id', subjectId);
                        } else {
                            url.searchParams.delete('subject_id');
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

                        table.ajax.url(url.toString()).load();
                    }

                $('#filterEducationType').on('change', function() {
                    filterLevelOptions();
                    $('#filterLevel').val('');
                    reloadTable();
                });

                $('#filterTopic, #filterLevel, #filterSubject').on('change', reloadTable);

                $('#filterReset').on('click', function() {
                    $('#filterTopic').val('');
                    $('#filterEducationType').val('');
                    $('#filterLevel').val('');
                    $('#filterSubject').val('');
                    reloadTable();
                });

                // Initial filter on page load
                filterLevelOptions();
            });
        </script>
    @endpush
</x-app-layout>
