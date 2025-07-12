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
            ->rawColumns(['title', 'description', 'due_date', 'recurrence_type', 'actions']);
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
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center')->searchable(false)->orderable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Assignments_' . date('YmdHis');
    }
}
