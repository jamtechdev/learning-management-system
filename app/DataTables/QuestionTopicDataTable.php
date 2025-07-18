<?php

namespace App\DataTables;

use App\Models\QuestionTopic;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class QuestionTopicDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('level_name', fn($row) => $row->level?->name . ' (' . $row->level?->education_type . ')' ?? 'N/A')
            ->addColumn('subject_name', fn($row) => $row->subject?->name ?? 'N/A')
            ->addColumn('education_type', fn($row) => ucfirst($row->education_type) ?? 'N/A')
            ->addColumn('actions', function ($row) {
                $buttons = [
                    [
                        'tag'   => 'a',
                        'href'  => route('admin.topics.edit', $row->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon'  => 'fas fa-edit fa-lg',
                        'title' => 'Edit',
                        'data'  => [
                            'id' => $row->id,
                            'table_id' => 'questiontopic-table',
                        ],
                    ],
                    [
                        'tag'   => 'button',
                        'href'  => 'javascript:void(0);',
                        'class' => 'text-sm text-red-600 hover:underline btn-delete',
                        'icon'  => 'fas fa-trash-alt fa-lg',
                        'title' => 'Delete',
                        'data'  => [
                            'id' => $row->id,
                            'url' => route('admin.topics.destroy', $row->id),
                            'table_id' => 'questiontopic-table',
                        ],
                    ],
                ];

                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->filterColumn('education_type', function ($query, $keyword) {
                $query->where('education_type', 'like', "%$keyword%");
            })
            ->filterColumn('level_name', function ($query, $keyword) {
                $query->whereHas('level', fn($q) => $q->where('name', 'like', "%$keyword%"));
            })
            ->filterColumn('subject_name', function ($query, $keyword) {
                $query->whereHas('subject', fn($q) => $q->where('name', 'like', "%$keyword%"));
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%$keyword%");
            })

            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(QuestionTopic $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['level', 'subject']);

        if ($educationType = request('education_type')) {
            $query->where('education_type', $educationType);
        }

        if ($levelId = request('level_id')) {
            $query->where('level_id', $levelId);
        }

        if ($subjectName = request('subject_id')) {
            $query->whereHas('subject', function ($q) use ($subjectName) {
                $q->where('name', $subjectName);
            });
        }

        if ($topicName = request('topic_id')) {
            $query->where('name', $topicName);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('questiontopic-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search Topics'
            ])

            ->parameters([
                'paging' => true,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10', '25', '50', 'Show all']
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('TOPIC ID')->addClass('text-center')->headerClass('text-center'),
            Column::make('education_type')->title('EDUCATION TYPE')->addClass('text-center')->headerClass('text-center'),
            Column::computed('level_name')->title('LEVEL')->addClass('text-center')->headerClass('text-center'),
            Column::computed('subject_name')->title('SUBJECT')->addClass('text-center')->headerClass('text-center'),
            Column::make('name')->title('NAME')->addClass('text-center')->headerClass('text-center'),
            Column::computed('actions')
                ->title('Actions')->addClass('text-center')->headerClass('text-center')
                ->exportable(false)
                ->printable(false),
        ];
    }

    protected function filename(): string
    {
        return 'QuestionTopics_' . date('YmdHis');
    }
}
