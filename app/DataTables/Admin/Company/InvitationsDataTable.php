<?php

namespace App\DataTables\Admin\Company;

use App\Models\CompanyInvitation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvitationsDataTable extends DataTable
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
      ->addColumn('person', function($row){
        return $row->contactPerson->email;
      })
      ->addColumn('role', function($row){
        return $row->role->name;
      })
      ->editColumn('status', function($row){
        return ucwords($row->status);
      })
      // ->addColumn('action', function (PartnerCompany $company) {
      //   return view('admin.pages.partner.companies.action', compact('company'));
      // })
      ->setRowId('id');
      // ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\CompanyInvitation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CompanyInvitation $model): QueryBuilder
    {
      // dd(request()->company);
        return $model->whereHas('contactPerson', function($q){
          return $q->where('company_id', request()->company->id);
        })->with('contactPerson')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
      $buttons = [];
      // if (auth('admin')->user()->can(true))
        // $buttons[] = [
        //   'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Company</span>',
        //   'className' =>  'btn btn-primary mx-3',
        //   'attr' => [
        //     'data-toggle' => "ajax-offcanvas",
        //     'data-title' => 'Add Company',
        //     'data-href' => route('admin.partner.companies.create')
        //   ]
        // ];

      return $this->builder()
        ->setTableId(CompanyInvitation::DT_ID)
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom(
          '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        )
        // ->addAction(['width' => '80px'])
        ->orderBy(0, 'DESC')
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
            Column::make('id'),
            Column::make('person'),
            Column::make('role'),
            Column::make('status'),
            Column::make('created_at'),
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
        return 'Invitations_' . date('YmdHis');
    }
}
