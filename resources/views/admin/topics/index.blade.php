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
    @endpush
</x-app-layout>
