<?php

namespace App\DataTables;

use App\Models\QuestionLevel;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class QuestionLevelDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('actions', function ($row) {
                $buttons = [
                    [
                        'tag'   => 'a',
                        'href'  => route('admin.levels.edit', $row->id),
                        'class' => 'text-sm text-blue-600 hover:underline me-2',
                        'icon'  => 'fas fa-pen',
                        'title' => 'Edit',
                        'data'  => ['id' => $row->id],
                    ],
                    [
                        'tag'   => 'button',
                        'href'  => 'javascript:void(0);',
                        'class' => 'text-sm text-red-600 hover:underline btn-delete',
                        'icon'  => 'fas fa-trash',
                        'title' => 'Delete',
                        'data'  => [
                            'id' => $row->id,
                            'url' => route('admin.levels.destroy', $row->id),
                            'table_id' => 'questionlevel-table', // pass correct table id here
                        ],
                    ],

                ];

                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->editColumn('created_at', fn($row) => $row->created_at?->format('Y-m-d H:i'))
            ->rawColumns(['actions'])
            ->setRowId('id');
    }

    public function query(QuestionLevel $model)
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('questionlevel-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                "search" => "",
                "lengthMenu" => "_MENU_",
                "searchPlaceholder" => 'Search Users'
            ])
            ->buttons(
                Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="fa fa-plus"></i> New')
                    ->visible(true)
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.levels.create') . '";
                    }')
            )
            ->parameters([
                'paging' => true,
                'lengthMenu' => [
                    [10, 15, 25, 50, -1],
                    ['10', '15', '25', '50', 'Show all']
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('LEVEL ID')->addClass('text-center')->headerClass('text-center'),
            Column::make('name')->title('LEVEL')->addClass('text-center')->headerClass('text-center'),
            Column::make('education_type')->title('TYPE')->addClass('text-center')->headerClass('text-center'),
            Column::make('created_at')->title('ADDED AT')->addClass('text-center')->headerClass('text-center'),
            Column::computed('actions')
                ->title('ACTIONS')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')->headerClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'QuestionLevels_' . date('YmdHis');
    }
}
