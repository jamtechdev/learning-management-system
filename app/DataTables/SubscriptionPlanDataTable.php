<?php

namespace App\DataTables;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Services\DataTable;

class SubscriptionPlanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name', fn($plan) => $plan->name)
            ->addColumn('price', fn($plan) => '$' . number_format($plan->price, 2))
            ->addColumn('duration_days', fn($plan) => $plan->duration_days)
            ->addColumn('description', function ($plan) {
                $description = $plan->description ?: '—';
                return '
                    <div class="accordion" id="accordionDescription' . $plan->id . '">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading' . $plan->id . '">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription' . $plan->id . '" aria-expanded="true" aria-controls="collapseDescription' . $plan->id . '">
                                    View Description
                                </button>
                            </h2>
                            <div id="collapseDescription' . $plan->id . '" class="accordion-collapse collapse" aria-labelledby="heading' . $plan->id . '" data-bs-parent="#accordionDescription' . $plan->id . '">
                                <div class="accordion-body">
                                    <p>' . e($description) . '</p>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('subjects', function ($plan) {
                $badges = '';
                if ($plan->subjects->count()) {
                    foreach ($plan->subjects as $subject) {
                        $badges .= '<span class="inline-block px-2 py-0.5 text-xs font-semibold text-white bg-[#3e80f9] rounded mr-1">' . e($subject->name) . '</span>';
                    }
                    return $badges;
                }
                return '<span class="text-xs italic text-gray-400">No subjects assigned</span>';
            })
            ->addColumn('actions', function ($plan) {
                $editUrl = route('admin.subscriptions.edit', $plan->id);
                $assignUrl = route('admin.subscriptions.assignSubjects', $plan->id);
                $deleteUrl = route('admin.subscriptions.destroy', $plan->id);
                $csrf = csrf_field();
                $method = method_field('DELETE');

                return '
                    <a href="' . $editUrl . '" class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-blue-600 rounded-md hover:bg-blue-700">Edit</a>
                    <a href="' . $assignUrl . '" class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-green-600 rounded-md hover:bg-green-700">Assign Subjects</a>
                    <form action="' . $deleteUrl . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this subscription plan?\')">
                        ' . $csrf . '
                        ' . $method . '
                        <button type="submit" class="inline-flex items-center px-3 py-1 text-sm text-white transition bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['subjects', 'description', 'actions']);
    }

    public function query(SubscriptionPlan $model): QueryBuilder
    {
        return $model->newQuery()->with('subjects');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subscriptionplan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Subscription Plans',
            ])
            ->buttons([
                Button::make()
                    ->className('btn btn-primary')
                    ->text('<i class="fa fa-plus"></i> Create Subscription Plan')
                    ->action('function(e, dt, node, config) {
                        window.location.href = "' . route('admin.subscriptions.create') . '";
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
            Column::make('name')->title('Name')->searchable(true)->orderable(true),
            Column::make('price')->title('Price')->searchable(true)->orderable(true),
            Column::make('duration_days')->title('Duration (Days)')->searchable(true)->orderable(true),
            Column::make('description')->title('Description')->searchable(true)->orderable(true),
            Column::computed('subjects')->title('Subjects')->searchable(false)->orderable(false),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center')->searchable(false)->orderable(false),
        ];
    }

    protected function filename(): string
    {
        return 'SubscriptionPlans_' . date('YmdHis');
    }
}
