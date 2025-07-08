<x-app-layout>
    <div class="py-4">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="py-3 bg-white card-header d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">Manage Subscription Plans</h2>
                    <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Subscription Plan
                    </a>
                </div>

                <!-- Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(
                            [
                                'class' => 'table table-striped table-hover table-bordered w-100',
                                'id' => 'subscriptionplan-table',
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
