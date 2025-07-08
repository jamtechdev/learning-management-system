<?php

namespace App\DataTables;

use App\Models\User; // Assuming "Parent" users use User model with role "parent"
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ParentDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('avatar', function ($user) {
                $src = $user->avatar ? asset('storage/' . $user->avatar) : asset('/images/logo/default-avatar.png');
                $fallback = asset('/images/logo/default-avatar.png');

                return '<img src="' . $src . '" onerror="this.onerror=null;this.src=\'' . $fallback . '\';" class="w-8 h-8 border border-gray-300 rounded-full dark:border-gray-700">';
            })

            ->addColumn('name', fn($user) => $user->first_name . ' ' . $user->last_name)
            ->addColumn('students', fn($user) => '<a href="' . route('admin.student.index', $user->id) . '" class="text-indigo-600 hover:text-indigo-800" title="View Students"><i class="fas fa-users fa-lg"></i></a>')
            ->addColumn('actions', function ($user) {
                return view('components.datatable.buttons', [
                    'data' => [
                        [
                            'tag' => 'a',
                            'href' => route('admin.parents.edit', $user->id),
                            'class' => 'text-blue-600 hover:text-blue-800',
                            'icon' => 'fas fa-edit fa-lg',
                            'title' => 'Edit',
                            'data' => [],
                        ],
                        [
                            'tag' => 'button',
                            'href' => 'javascript:void(0);',
                            'class' => 'text-red-600 hover:text-red-800 btn-delete',
                            'icon' => 'fas fa-trash-alt fa-lg',
                            'title' => 'Delete',
                            'data' => [
                                'url' => route('admin.parents.destroy', $user->id),
                                'id' => $user->id,
                                'table_id' => 'parent-table',
                            ],
                        ],
                    ]
                ])->render();
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('first_name', 'like', "%$keyword%")
                    ->orWhere('last_name', 'like', "%$keyword%");
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('email', 'like', "%$keyword%");
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('phone', 'like', "%$keyword%");
            })
            ->rawColumns(['avatar', 'students', 'actions']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->role('parent');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('parent-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Parents',
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
            Column::computed('avatar')->title('Avatar')->orderable(false)->searchable(false),
            Column::computed('name')->title('Name')->searchable(true)->orderable(true),
            Column::make('email')->title('Email')->searchable(true)->orderable(true),
            Column::make('phone')->title('Phone')->searchable(true)->orderable(true),
            Column::computed('students')->title('Add Student')->orderable(false)->searchable(false)->addClass('text-center'),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center')->searchable(false)->orderable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Parents_' . date('YmdHis');
    }
}
