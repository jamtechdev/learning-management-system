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
            ->addColumn('level_name', fn($row) => $row->level?->name ?? 'N/A')
            ->addColumn('subject_name', fn($row) => $row->subject?->name ?? 'N/A')
            ->addColumn('actions', function ($row) {
                $buttons = [
                    [
                        'tag'   => 'a',
                        'href'  => route('admin.topics.edit', $row->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon'  => 'fas fa-pen',
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
                        'icon'  => 'fas fa-trash',
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
            ->editColumn('created_at', fn($row) => $row->created_at?->format('Y-m-d H:i'))
            ->editColumn('updated_at', fn($row) => $row->updated_at?->format('Y-m-d H:i'))
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(QuestionTopic $model): QueryBuilder
    {
        return $model->newQuery()->with(['level', 'subject']);
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
            Column::make('id')->title('ID'),
            Column::make('name')->title('Topic'),
            Column::computed('level_name')->title('Level'),
            Column::computed('subject_name')->title('Subject'),
            Column::make('created_at')->title('Created At'),
            Column::make('updated_at')->title('Updated At'),
            Column::computed('actions')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->width(100),
        ];
    }

    protected function filename(): string
    {
        return 'QuestionTopics_' . date('YmdHis');
    }
}
