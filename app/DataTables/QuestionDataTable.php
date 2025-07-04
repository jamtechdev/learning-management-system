<?php

namespace App\DataTables;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class QuestionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('question', function ($q) {
                return '
                    <div class="p-2 overflow-y-auto bg-white border border-gray-300 rounded max-h-40 dark:border-gray-700 dark:bg-gray-800">
                        <div class="prose dark:prose-invert max-w-none">' . $q->content . '</div>
                    </div>';
            })
            ->addColumn('type', fn($q) => '<span class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded dark:bg-gray-800">'
                . $q->type . '</span>')
            ->addColumn('education_type', fn($q) => '<span class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded dark:bg-gray-800">'
                . ucwords(str_replace('_', ' ', $q->education_type)) . '</span>')
            ->addColumn('level', fn($q) => $q->level?->name ?? '-')
            ->addColumn('subject', fn($q) => $q->subject?->name ?? '-')
->addColumn('topic', fn($q) => '<span title="' . e($q->topic?->name ?? '-') . '">' . Str::limit($q->topic?->name ?? '-', 20) . '</span>')
            ->addColumn('options', function ($q) {
                $json = htmlspecialchars($q->toJson(), ENT_QUOTES, 'UTF-8');
                return '<button @click="openModal(' . $json . ')" class="px-2 py-1 text-xs text-green-700 border border-green-700 rounded hover:bg-green-100">View Options</button>';
            })
            ->addColumn('actions', function ($q) {
                $buttons = [
                    [
                        'tag' => 'a',
                        'href' => route('admin.questions.edit', $q->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon' => 'fas fa-pen',
                        'title' => 'Edit',
                        'data' => [
                            'id' => $q->id,
                            'table_id' => 'question-table',
                        ],
                    ],
                    [
                        'tag' => 'button',
                        'href' => 'javascript:void(0);',
                        'class' => 'text-sm text-red-600 hover:underline btn-delete',
                        'icon' => 'fas fa-trash',
                        'title' => 'Delete',
                        'data' => [
                            'id' => $q->id,
                            'url' => route('admin.questions.destroy', $q->id),
                            'table_id' => 'question-table',
                        ],
                    ],
                ];
                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->filterColumn('level', function ($query, $keyword) {
                $query->whereHas('level', fn($q) => $q->where('name', 'like', "%$keyword%"));
            })
            ->filterColumn('subject', function ($query, $keyword) {
                $query->whereHas('subject', fn($q) => $q->where('name', 'like', "%$keyword%"));
            })
            ->filterColumn('topic', function ($query, $keyword) {
                $query->whereHas('topic', fn($q) => $q->where('name', 'like', "%$keyword%"));
            })
            ->rawColumns(['question', 'type', 'education_type', 'topic', 'options', 'actions']);
    }

    public function query(Question $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['level', 'subject', 'topic'])
            ->when(request('type'), function ($q) {
                $q->where('type', request('type'));
            });

        if ($educationType = request('education_type')) {
            $query->where('education_type', $educationType);
        }

        if ($levelId = request('level_id')) {
            $query->where('level_id', $levelId);
        }

        if ($subjectName = request('subject_id')) {
            $query->whereHas('subject', fn($q) => $q->where('name', 'like', "%$subjectName%"));
        }

        if ($topicId = request('topic_id')) {
            $query->where('topic_id', $topicId);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('question-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Questions',
            ])
            ->buttons([
                Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="fa fa-plus"></i> Add Question')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.questions.create') . '";
                    }'),
            ])
            ->parameters([
                'paging' => true,
                'lengthMenu' => [[10, 25, 50, -1], ['10', '25', '50', 'Show all']],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(30)->addClass('text-center')->orderable(false)->searchable(false),
            Column::make('content')->title('Question'),
            Column::computed('type')->title('Type'),
            Column::computed('education_type')->title('Education Type'),
            Column::computed('level')->title('Level'),
            Column::computed('subject')->title('Subject'),
            Column::computed('topic')->title('Topic'),
            Column::computed('options')->title('Options')->exportable(false)->printable(false)->addClass('text-center'),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Questions_' . date('YmdHis');
    }
}
