<x-app-layout>
    <div class="py-4">
        <div class="container-fluid">
            <div class="border-0 shadow card rounded-3">

                <!-- Header -->
                <div class="py-3 bg-white card-header d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="mb-0 h4 text-dark">Manage Feedback</h2>
                </div>

                <!-- Table -->
                <div class="pt-0 card-body">
                    <div class="table-responsive">
                         {!! $dataTable->table(['class' => 'table table-bordered w-100'], true) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
         {!! $dataTable->scripts() !!}
    @endpush
</x-app-layout>
