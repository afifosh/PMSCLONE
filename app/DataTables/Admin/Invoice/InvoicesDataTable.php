<?php

namespace App\DataTables\Admin\Invoice;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoicesDataTable extends DataTable
{
  /*
  * @var null|App\Models\Company|App\Models\Contract
  */
  public $filterBy = null;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('id', function ($inv) {
        return '<a href="' . route('admin.invoices.edit', $inv->id) . '">' . ($inv->type == 'Down Payment' ? runtimeDpInvIdFormat($inv->id) : runtimeInvIdFormat($inv->id)) . '</a>';
      })
      ->editColumn('checkbox', function ($inv) {
        return '<input class="form-check-input invoice-check" name="selected_invoices[]" type="checkbox" value="' . $inv->id . '">';
      })
      ->editColumn('company_id', function ($invoice) {
        return view('admin._partials.sections.company-avatar', ['company' => $invoice->company])->render();
      })
      ->editColumn('contract_id', function ($invoice) {
        return '<a href="' . route('admin.contracts.show', $invoice->contract->id) . '">' . $invoice->contract->subject . '</a>';
      })
      ->addColumn('program_name', function ($invoice) {
        return $invoice->contract->program
            ? '<a href="' . route('admin.programs.show', $invoice->contract->program->id) . '">' . $invoice->contract->program->name . '</a>'
            : 'N/A';
      })
      ->editColumn('action', function ($invoice) {
        return view('admin.pages.invoices.action', ['invoice' => $invoice]);
      })
      ->addColumn('total', function ($invoice) {
        return view('admin.pages.invoices.total-column', ['invoice' => $invoice]);
      })
      ->rawColumns(['company_id', 'id', 'contract_id', 'program_name', 'checkbox']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Invoice $model): QueryBuilder
  {
    //$query = $model->with(['company', 'contract']);
    $query = $model->with(['company', 'contract', 'contract.program']);
    // if filterBy is instance of Contract
    if ($this->filterBy instanceof Contract) {
      $query->where('contract_id', $this->filterBy->id);
    }else if($this->filterBy instanceof Company){
      $query->where('company_id', $this->filterBy->id);
    }else if($this->filterBy instanceof Program){
        // Fetch child program IDs
        $childProgramIds = Program::where('parent_id', $this->filterBy->id)->pluck('id')->toArray();

        // Include the main program's ID
        $programIds = array_merge([$this->filterBy->id], $childProgramIds);

        $query->whereHas('contract', function ($query) use ($programIds) {
            $query->whereIn('program_id', $programIds);
        });
    }

    return $query->ValidAccessibleByAdmin(auth()->id())->applyRequestFilters()->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<span>Select Invoices</span>',
      'className' =>  'btn btn-primary mx-3 select-invoices-btn',
      'attr' => [
        'onclick' => 'toggleCheckboxes()',
      ]
    ];
    $buttons[] = [
      'text' => '<span>Delete Invoices</span>',
      'className' =>  'btn btn-primary mx-3 delete-inv-btn d-none',
      'attr' => [
        'data-toggle' => "confirm-action",
        'data-success-action' => 'destroyBulkInvoices',
      ]
    ];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Invoice</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Invoice',
        'data-href' => route('admin.invoices.create')
      ]
    ];

    return $this->builder()
      ->setTableId('invoices-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
          <"row mx-2"<"col-md-2"<"me-3"l>>
          <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
          >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'DESC'])
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
      Column::make('checkbox')->title('<input class="form-check-input invoice-check-all" type="checkbox">')->orderable(false)->searchable(false)->printable(false)->exportable(false)->visible(false)->width(1),
      Column::make('id'),
      Column::make('company_id')->title('Client'),
      Column::make('program_name')->title('Program'), // Added this line
      Column::make('contract_id')->title('Contract'),
      Column::make('due_date'),
      Column::make('status'),
      Column::make('total'),
      Column::make('type'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Invoices_' . date('YmdHis');
  }
}
