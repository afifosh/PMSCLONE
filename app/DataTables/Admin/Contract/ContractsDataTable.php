<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContractsDataTable extends DataTable
{
  public $projectId = null;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('contracts.id', function ($contract) {
        return view('admin.pages.contracts.name', ['contract_id' => $contract->id]);
      })
      ->addColumn('action', function ($contract) {
        return view('admin.pages.contracts.action', compact('contract'));
      })
      ->addColumn('assigned_to', function ($project) {
        if ($project->assignable instanceof Company) {
          return view('admin._partials.sections.company-avatar', ['company' => $project->assignable]);
        } else if ($project->assignable instanceof Client) {
          return view('admin._partials.sections.client-info', ['user' => $project->assignable]);
        } else {
          return '-';
        }
      })
      // ->editColumn('project.name', function ($project) {
      //   return $project->project ? $project->project->name : '-';
      // })
      ->editColumn('type.name', function ($project) {
        return $project->type ? $project->type->name : '-';
      })
      ->editColumn('category.name', function ($project) {
        return $project->category ? $project->category->name : '-';
      })
      ->editColumn('value', function (Contract $contract) {
        return view('admin.pages.contracts.value-column', compact('contract'));
      })
      ->editColumn('start_date', function ($project) {
        return $project->start_date ? $project->start_date->format('d M, Y') : '-';
      })
      ->editColumn('end_date', function ($project) {
        return $project->end_date ? $project->end_date->format('d M, Y') : '-';
      })
      ->filterColumn('assigned_to', function ($query, $keyword) {
        $query->whereHasMorph('assignable', Company::class, function ($q) use ($keyword) {
          $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        })->orWhereHasMorph('assignable', Client::class, function ($q) use ($keyword) {
          $q->where(function ($q) use ($keyword) {
            $q->where('first_name', 'like', '%' . $keyword . '%')
              ->orWhere('last_name', 'like', '%' . $keyword . '%')
              ->orWhere('email', 'like', '%' . $keyword . '%');
          });
        });
      })
      ->rawColumns(['id']);
  }

/**
 * Get the query source of dataTable.
 */
public function query(Contract $model): QueryBuilder
{
    // Start the base query
    $query = $model->newQuery()
        ->select([
            'contracts.id',
            'contracts.refrence_id',
            'contracts.project_id',
            'contracts.type_id',
            'contracts.value',
            'contracts.start_date',
            'contracts.end_date',
            'contracts.status',
            'contracts.assignable_id',
            'assignable_type',
            DB::raw('SUM(invoices.total)/100 as total'),
            DB::raw('SUM(invoices.paid_amount)/100 as paid_amount'),
            DB::raw('sum(invoices.total - invoices.paid_amount)/100 as due_amount'),
            DB::raw('sum(invoices.total_tax)/100 as total_tax'),
            DB::raw('(sum(invoices.paid_amount)/sum(contracts.value))*100 as paid_percent')
        ])
        ->leftJoin('invoices', 'contracts.id', '=', 'invoices.contract_id')
        ->groupBy([
            'contracts.id',
            'contracts.project_id',
            'contracts.type_id',
            'contracts.value',
            'contracts.start_date',
            'contracts.end_date',
            'contracts.status',
            'contracts.assignable_id',
            'assignable_type'
        ])
        ->with(['type', 'assignable.detail', 'category']);

    // If a projectId is provided, filter by it
    if ($this->projectId) {
        $query->where('project_id', $this->projectId);
    }

    // Apply any additional filters if necessary
    $query->applyRequestFilters();

    return $query;
}


  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Contract</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Contract',
        'data-href' => route('admin.contracts.create', ['project' => $this->projectId])
      ]
    ];

    return $this->builder()
      ->setTableId('contracts-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'desc'])
      ->responsive(true)
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      Column::make('contracts.id')->title('Contract'),
      Column::make('refrence_id')->title('Ref ID'),
      Column::make('assigned_to')->title('Assigned To'),
      Column::make('type.name')->title('Type'),
      Column::make('category.name')->title('Category'),
      Column::make('value')->title('Amount'),
      // Column::make('paid_percent')->title('Paid')->searchable(false),
      Column::make('start_date'),
      Column::make('end_date'),
      // Column::make('phases_count')->title('Phases')->searchable(false),
      Column::make('status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Contracts_' . date('YmdHis');
  }
}
