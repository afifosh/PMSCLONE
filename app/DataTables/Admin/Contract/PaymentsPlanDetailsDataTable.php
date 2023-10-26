<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractPhase;
use App\Models\ContractStage;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentsPlanDetailsDataTable extends DataTable
{
  public $stage;
  public $contract_id;
  public $projectId = null;
  public $company = null;
  public $program = null;
  public $programId = null;  
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('checkbox', function ($phase) {
        return '<input class="form-check-input phase-check" name="selected_phases[]" type="checkbox" value="' . $phase->id . '">';
      })
      ->editColumn('action', function ($phase) {
        $is_editable = !(@$phase->addedAsInvoiceItem[0]->invoice->status && in_array(@$phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid']));
         return "sda";
        //return view('admin.pages.contracts.phases.actions', ['phase' => $phase, 'stage' => $this->stage, 'contract_id' => $this->contract_id, 'is_editable' => $is_editable])->render();
      })
      ->editColumn('invoice_id', function ($phase) {
        $invoiceItem = $phase->addedAsInvoiceItem->first();
        return $invoiceItem
          ? '<a href="' . route('admin.invoices.edit', $invoiceItem->invoice_id) . '">' . runtimeInvIdFormat($invoiceItem->invoice_id) . '</a>'
          : 'N/A';
      })   
      ->editColumn('value', function ($phase) {
        return "sad";
        return view('admin.pages.contracts.paymentsplan.value-column', compact('phase'));
      })->rawColumns(['invoice_id', 'action', 'checkbox'])
      ->setRowAttr([
        'data-id' => function ($phase) {
          return $phase->id;
        }
      ]);
  }

  /**
   * Get the query source of dataTable.
   */
public function query(ContractPhase $model): QueryBuilder
{
    // Initialize the base query
    $query = $model
        ->join('contract_stages', 'contract_phases.stage_id', '=', 'contract_stages.id')
        ->join('contracts', 'contract_stages.contract_id', '=', 'contracts.id')
        ->leftJoin('programs', 'contracts.program_id', '=', 'programs.id') 
        ->select([
            'contract_phases.*',
            'contracts.subject as contract_name',
            'contract_stages.name as stage_name',
            'programs.name as program.name', // Include the program name
        ])
        ->with('addedAsInvoiceItem.invoice')
        ->newQuery();

    // ... [rest of the filters remain untouched]

    return $query;
}

  
  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    // if ($this->contract->getRawOriginal('status') == 'Active')
    $buttons[] = [
      'text' => '<span>Select Phases</span>',
      'className' =>  'btn btn-primary mx-3 select-phases-btn',
      'attr' => [
        'onclick' => 'toggleCheckboxes()',
      ]
    ];
    $buttons[] = [
      'text' => '<span>Create Invoices</span>',
      'className' =>  'btn btn-primary mx-3 create-inv-btn d-none',
      'attr' => [
        'onclick' => 'createInvoices()',
      ]
    ];
    $buttons[] = [
      'text' => '<span>Add Phase</span>',
      'className' =>  'btn btn-primary',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Phase',
      //  'data-href' => route('admin.projects.contracts.stages.phases.create', ['project' => 'project', $this->contract_id, $this->stage->id ?? 'stage'])
      ]
    ];

    return $this->builder()
      ->setTableId('paymentsplan-details-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->responsive(true)
      ->parameters([
        //'buttons' => $buttons,
        "scrollX" => true,
        "drawCallback" => "function (settings) {
          initSortable();
        }"
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
      return [
        Column::make('order')->visible(false),
        Column::make('checkbox')->title('<input class="form-check-input phase-check-all" type="checkbox">')->orderable(false)->searchable(false)->printable(false)->exportable(false)->visible(false)->width(1),
        Column::make('stage_name')->title('Stage Name'),
        Column::make('name')->title('Phase Name'),
        Column::make('value')->title('Phase Amount'),
        Column::make('start_date'),
        Column::make('due_date')->title('End Date'),
        Column::make('status')->title('Phase Status'),
        Column::make('invoice_id')->title('Invoice ID')->orderable(true), // Add the new column for invoice_id
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
  }
  

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Phases_' . date('YmdHis');
  }
}
