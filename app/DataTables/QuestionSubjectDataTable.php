<?php

namespace App\DataTables;

use App\Models\QuestionSubject;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class QuestionSubjectDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('actions', function ($row) {
                $buttons = [
                    [
                        'tag'   => 'a',
                        'href'  => route('admin.subjects.edit', $row->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon'  => 'fas fa-pen',
                        'title' => 'Edit',
                        'data'  => [
                            'id' => $row->id,
                            'table_id' => 'questionsubject-table',
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
                            'url' => route('admin.subjects.destroy', $row->id),
                            'table_id' => 'questionsubject-table',
                        ],
                    ],
                ];

                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->editColumn('created_at', fn($row) => $row->created_at?->format('Y-m-d H:i'))
            ->editColumn('level_id', fn($row) => $row->level->name ?? '')
            ->editColumn('education_type', fn($row) => ucfirst($row->education_type))
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(QuestionSubject $model): QueryBuilder
    {
        $query = $model->newQuery();

        if ($educationType = request('education_type')) {
            $query->where('education_type', $educationType);
        }
        if ($levelId = request('level_id')) {
            $query->where('level_id', $levelId);
        }
        if ($name = request('subject_id')) {
            $query->where('name', $name);
        }
        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('questionsubject-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search Subjects'
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
            Column::make('id')->title('SUBJECT ID')->addClass('text-center')->headerClass('text-center'),
            Column::make('name')->title('NAME')->addClass('text-center')->headerClass('text-center'),
            Column::make('education_type')->title('EDUCATION TYPE')->addClass('text-center')->headerClass('text-center'),
            Column::make('level_id')->title('LEVEL')->addClass('text-center')->headerClass('text-center'),
            Column::make('created_at')->title('ADDED AT')->addClass('text-center')->headerClass('text-center'),
            Column::computed('actions')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->headerClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'QuestionSubjects_' . date('YmdHis');
    }
}
