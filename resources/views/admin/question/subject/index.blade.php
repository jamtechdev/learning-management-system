<x-app-layout>
    <div class="py-4">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="py-3 bg-white card-header d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">Manage Subject Record</h2>
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Subject
                    </a>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filterEducationType" class="form-label">Education Type</label>
                            <select id="filterEducationType" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterLevel" class="form-label">Level</label>
                            <select id="filterLevel" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">  {{ $level->name . ' (' . $level->education_type . ')' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterSubject" class="form-label">Subject</label>
                            <select id="filterSubject" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
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
                                'id' => 'questionsubject-table',
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
                const table = $('#questionsubject-table').DataTable();

                function reloadTable() {
                    const educationType = $('#filterEducationType').val();
                    const level = $('#filterLevel').val();
                    const subject = $('#filterSubject').val();

                    const url = new URL(table.ajax.url());

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

                    table.ajax.url(url.toString()).load();
                }

                $('#filterEducationType, #filterLevel, #filterSubject').on('change', reloadTable);

                $('#filterReset').on('click', function() {
                    $('#filterEducationType').val('');
                    $('#filterLevel').val('');
                    $('#filterSubject').val('');
                    reloadTable();
                });
            });
        </script>
    @endpush
</x-app-layout>
