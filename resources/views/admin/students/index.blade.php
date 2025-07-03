<x-app-layout>
    <div class="py-4">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="justify-between py-3 bg-white card-header d-flex align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">
                        @isset($parent)
                            Students of {{ $parent->first_name }} {{ $parent->last_name }}
                        @else
                            Manage Students Record
                        @endisset
                    </h2>

                    <div class="flex-wrap gap-2 d-flex">
                        @isset($parent)
                            <a href="{{ route('admin.parents.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        @endisset

                        <a href="{{ route('admin.student.create', $parent->id ?? 0) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Add Student
                        </a>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table([
                            'class' => 'table table-striped table-hover table-bordered w-100',
                            'id' => 'student-table',
                        ], true) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        {!! $dataTable->scripts() !!}
    @endpush
</x-app-layout>
