<?php

namespace App\DataTables;

use App\Models\Feedback;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FeedbackDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query->with('question')))
            ->addIndexColumn()
            ->addColumn('question', function ($feedback) {
                return  strip_tags($feedback->question->content) ?? '-';
            })
            ->addColumn('type', function ($feedback) {
                return ucfirst(str_replace('_', ' ', $feedback->type));
            })
            ->addColumn('message', function ($feedback) {
                return e($feedback->message);
            })
            ->addColumn('created_at', function ($feedback) {
                return $feedback->created_at ? $feedback->created_at->format('d M Y') : '-';
            });
    }

    public function query(Feedback $model): QueryBuilder
    {
        return $model->newQuery()->with('question');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('feedback-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Feedback...',
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
            Column::computed('question')->title('Question')->searchable(false)->orderable(false),
            Column::make('type')->title('Feedback Type')->searchable(true)->orderable(true),
            Column::make('message')->title('Message')->searchable(true)->orderable(true),
            Column::make('created_at')->title('Submitted On')->searchable(false)->orderable(true),
        ];
    }

    protected function filename(): string
    {
        return 'Feedback_' . date('YmdHis');
    }
}
