<?php

namespace App\DataTables;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class AssignmentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $editUrl = route('admin.assignments.edit', '__id__');
        $deleteUrl = route('admin.assignments.delete', '__id__');

        return (new EloquentDataTable($query->with('subject')))
            ->addIndexColumn()
            ->addColumn('title', function ($assignment) {
                return '<strong>' . e($assignment->title) . '</strong>';
            })
            ->addColumn('description', function ($assignment) {
                return e($assignment->description) ?: '-';
            })
            ->addColumn('due_date', function ($assignment) {
                if ($assignment->due_date instanceof \Illuminate\Support\Carbon) {
                    return $assignment->due_date->format('d M Y');
                }
                return $assignment->due_date ? date('d M Y', strtotime($assignment->due_date)) : '-';
            })
            ->addColumn('recurrence_type', function ($assignment) {
                return ucfirst(str_replace('_', ' ', $assignment->recurrence_type));
            })
            ->addColumn('subject', function ($assignment) {
                return $assignment->subject->name ?? '-';
            })
            ->addColumn('assign_question', function ($assignment) {
                $buttonHtml = '
                    <div x-data="{ open: false }">
                        <!-- Button to trigger the modal -->
                        <button class="px-6 py-2 font-semibold text-white bg-blue-600 rounded-lg btn btn-sm btn-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" @click="open = true">
                            View Questions
                        </button>

                        <!-- Modal structure for each row -->
                        <div x-show="open" x-cloak @click.away="open = false" x-transition
                            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                            <div class="w-full max-w-lg transition-all transform bg-white rounded-lg shadow-xl sm:w-3/4 md:w-1/2 lg:w-1/3">

                                <!-- Header -->
                                <div class="flex items-center justify-between p-4 text-white bg-gray-800 rounded-t-lg">
                                    <h3 class="text-xl font-semibold">Assignment Questions</h3>
                                    <button @click="open = false" class="text-gray-300 hover:text-white focus:outline-none">
                                        <i class="text-xl fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="p-6 space-y-4 overflow-y-auto max-h-96">
                                    <!-- Dynamically load the questions from the assignment -->
                                    <div class="space-y-4">
                                        ';

                foreach ($assignment->questions as $question) {
                    $questionType = $question->type;
                    switch ($questionType) {
                        case \App\Enum\QuestionTypes::MCQ:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Multiple Choice';
                            break;
                        case \App\Enum\QuestionTypes::TRUE_FALSE:
                            $badgeColor = 'bg-success';
                            $badgeText = 'True/False';
                            break;
                        case \App\Enum\QuestionTypes::LINKING:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Linking';
                            break;
                        case \App\Enum\QuestionTypes::REARRANGING:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Rearranging';
                            break;
                        case \App\Enum\QuestionTypes::FILL_IN_THE_BLANK:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Fill in the Blank';
                            break;
                        case \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Cloze with Options';
                            break;
                        case \App\Enum\QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Cloze with Dropdown';
                            break;
                        case \App\Enum\QuestionTypes::COMPREHENSION:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Comprehension';
                            break;
                        case \App\Enum\QuestionTypes::EDITING:
                            $badgeColor = 'bg-success';
                            $badgeText = 'Editing';
                            break;
                        default:
                            $badgeColor = 'bg-success';
                            $badgeText = 'General';
                    }

                    // Add the question card with the badge for the type
                    $buttonHtml .= '
                                                <div class="flex items-center p-4 space-x-4 bg-white border border-gray-200 rounded-lg shadow-md">
                                                    <i class="text-blue-600 fas fa-question-circle"></i> <!-- Question icon -->
                                                    <div class="flex-1">
                                                        <p class="text-lg font-medium leading-relaxed text-gray-800">' . e(strip_tags($question->content)) . '</p>
                                                        <span class="inline-block px-3 py-1 mt-2 text-xs font-semibold text-white rounded-full ' . $badgeColor . '">' . $badgeText . '</span>
                                                    </div>
                                                </div>';
                }

                $buttonHtml .= '
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-4 text-white bg-gray-800 rounded-b-lg">
                                    <p class="text-sm text-center">© 2023 Your Company. All Rights Reserved.</p>
                                </div>
                            </div>
                        </div>
                    </div>';

                return $buttonHtml;
            })

            ->addColumn('actions', function ($assignment) {
                $buttons = [
                    [
                        'tag' => 'a',
                        'href' => route('admin.assignments.edit', $assignment->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon' => 'fas fa-edit fa-lg',
                        'title' => 'Edit',
                        'data' => [
                            'id' => $assignment->id,
                            'table_id' => 'assignments-table',
                        ],
                    ],
                    [
                        'tag' => 'button',
                        'href' => 'javascript:void(0);',
                        'class' => 'text-sm text-red-600 hover:underline btn-delete',
                        'icon' => 'fas fa-trash-alt fa-lg',
                        'title' => 'Delete',
                        'data' => [
                            'id' => $assignment->id,
                            'url' => route('admin.assignments.delete', $assignment->id),
                            'table_id' => 'assignments-table',
                        ],
                    ],
                ];
                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->rawColumns(['title', 'description', 'due_date', 'recurrence_type', 'actions', 'assign_question']);
    }

    public function query(Assignment $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('assignments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Assignments',
            ])
            ->buttons([
                Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="fa fa-plus"></i> Add Assignment')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.assignments.create') . '";
                    }'),
            ])
            ->parameters([
                'paging' => true,
                'lengthMenu' => [[10, 25, 50, -1], ['10', '25', '50', 'Show all']],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(30)->addClass('text-center')->orderable(false)->searchable(false),
            Column::make('title')->title('Title')->searchable(true)->orderable(true),
            Column::make('subject')->title('Subject')->searchable(true)->orderable(true),
            Column::make('description')->title('Description')->searchable(true)->orderable(true),
            Column::make('due_date')->title('Due Date')->searchable(true)->orderable(true),
            Column::make('recurrence_type')->title('Recurrence')->searchable(true)->orderable(true),
            Column::make('assign_question')->title('View Question')->searchable(true)->orderable(true),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center')->searchable(false)->orderable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Assignments_' . date('YmdHis');
    }
}
