<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentDataTable extends DataTable
{
    protected $parentId;

    public function __construct($parentId = null)
    {
        $this->parentId = $parentId;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('avatar', function ($student) {
                $src = $student->avatar ? asset('storage/' . $student->avatar) : asset('/images/logo/default-avatar.png');
                $fallback = asset('/images/logo/default-avatar.png');

                return '<img src="' . $src . '" onerror="this.onerror=null;this.src=\'' . $fallback . '\';" class="w-8 h-8 border border-gray-300 rounded-full cursor-pointer dark:border-gray-700">';
            })
            ->addColumn('name', fn($s) => $s->first_name . ' ' . $s->last_name)
            ->addColumn('lock_code', fn($s) => $s->lock_code ?: 'N/A')
            ->addColumn('actions', function ($student) {
                $buttons = [
                    [
                        'tag' => 'a',
                        'href' => route('admin.student.edit', $student->id),
                        'class' => 'text-sm text-blue-600 hover:underline',
                        'icon' => 'fas fa-pen',
                        'title' => 'Edit',
                        'data' => [],
                    ],
                    [
                        'tag' => 'button',
                        'href' => 'javascript:void(0);',
                        'class' => 'text-sm text-red-600 hover:underline btn-delete',
                        'icon' => 'fas fa-trash-alt',
                        'title' => 'Delete',
                        'data' => [
                            'url' => route('admin.student.destroy', $student->id),
                            'id' => $student->id,
                            'table_id' => 'student-table',
                        ],
                    ],
                ];

                return view('components.datatable.buttons', ['data' => $buttons])->render();
            })
            ->rawColumns(['avatar', 'actions']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->role('child') // Using Spatie role
            ->when($this->parentId, fn($q) => $q->where('parent_id', $this->parentId));
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('student-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->language([
                'search' => '',
                'lengthMenu' => '_MENU_',
                'searchPlaceholder' => 'Search Students',
            ])
            ->parameters([
                'paging' => true,
                'lengthMenu' => [[10, 25, 50, -1], ['10', '25', '50', 'Show all']],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(30)->addClass('text-center'),
            Column::computed('avatar')->title('Avatar')->orderable(false)->searchable(false)->addClass('text-center'),
            Column::computed('name')->title('Name'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('Phone'),
            Column::computed('lock_code')->title('Lock Code')->addClass('text-center'),
            Column::computed('actions')->title('Actions')->exportable(false)->printable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Students_' . date('YmdHis');
    }
}
