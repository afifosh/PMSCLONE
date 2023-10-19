<?php

namespace App\DataTables\Admin;

use App\Models\Medium;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MediumsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function ($row) {
                return view('admin.pages.medium.medium-info', ['user' => $row]);
            })
            ->editColumn('added_by', function ($medium) {
                return $medium->addedBy ? view('admin._partials.sections.user-info', ['user' => $medium->addedBy]) : '-';
            })
            ->addColumn('action', function (Medium $medium) {
                return view('admin.pages.medium.action', compact('medium'));
            })       
            ->filterColumn('added_by', function($query, $keyword){
                return $query->whereHas('addedBy', function($q) use ($keyword){
                    return $q->where('email', 'like', '%'.$keyword.'%');
                });
            })
            ->setRowId('id')
            ->rawColumns(['name', 'action']);
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Medium $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Medium $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Customize your query, add relationships, etc.
        // Example: $query->with('relatedModel');

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        $buttons = [];
        if (auth('admin')->user()->can('create company'))
          $buttons[] = [
            'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Medium</span>',
            'className' =>  'btn btn-primary mx-3',
            'attr' => [
              'data-toggle' => "ajax-modal",
              'data-title' => 'Add Medium',
              'data-href' => route('admin.companies.create')
            ]
          ];
        return $this->builder()
          ->setTableId(Medium::DT_ID)
          ->columns($this->getColumns())
          ->minifiedAjax()
          ->responsive(true)
          ->dom(
            '
          <"row mx-2"<"col-md-2"<"me-3"l>>
          <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
          >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
          )
          ->addAction(['width' => '80px'])
          ->parameters([
            'buttons' => $buttons,
            "scrollX" => true
          ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('name')->title(__('Medium Name')),
            Column::make('added_by'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Mediums_' . date('YmdHis');
    }
}
