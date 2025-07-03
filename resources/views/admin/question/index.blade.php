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

                <!-- Type Filter -->
                <div class="pb-0 card-body border-bottom">
                    <div class="mb-3 d-flex align-items-center">
                        <label for="typeFilter" class="me-2 fw-bold">Filter by Type:</label>
                        <select id="typeFilter" class="w-auto form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($questionTypes as $type)
                                <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
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

                $('#typeFilter').on('change', function() {
                    const type = $(this).val();
                    const url = new URL(table.ajax.url());

                    if (type) {
                        url.searchParams.set('type', type);
                    } else {
                        url.searchParams.delete('type');
                    }

                    table.ajax.url(url.toString()).load();
                });
            });
        </script>
    @endpush
</x-app-layout>
