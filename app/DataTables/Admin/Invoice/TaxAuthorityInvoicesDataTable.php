<?php

namespace App\DataTables\Admin\Invoice;

use App\Models\AuthorityInvoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TaxAuthorityInvoicesDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('id', function ($inv) {
        return '<a href="' . route('admin.invoices.edit', [$inv->invoice->id, 'tab' => 'authority-tax']) . '">' . runtimeTAInvIdFormat($inv->invoice->id) . '</a>';
      })
      ->editColumn('checkbox', function ($inv) {
        return '<input class="form-check-input invoice-check" name="selected_invoices[]" type="checkbox" value="' . $inv->id . '">';
      })
      ->editColumn('company_id', function ($athorityInv) {
        return view('admin._partials.sections.company-avatar', ['company' => $athorityInv->invoice->company])->render();
      })
      ->editColumn('contract_id', function ($athorityInv) {
        return '<a href="' . route('admin.contracts.show', $athorityInv->invoice->contract->id) . '">' . $athorityInv->invoice->contract->subject . '</a>';
      })
      ->addColumn('program_name', function ($athorityInv) {
        return $athorityInv->invoice->contract->program
          ? '<a href="' . route('admin.programs.show', $athorityInv->invoice->contract->program->id) . '">' . $athorityInv->invoice->contract->program->name . '</a>'
          : 'N/A';
      })
      ->editColumn('action', function ($invoice) {
        return view('admin.pages.invoices.tax-authority.action', ['invoice' => $invoice]);
      })
      ->addColumn('total', function ($invoice) {
        return view('admin.pages.invoices.tax-authority.total-column', ['invoice' => $invoice]);
      })
      ->rawColumns(['company_id', 'id', 'contract_id', 'program_name', 'checkbox']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(AuthorityInvoice $model): QueryBuilder
  {
    $query = $model->where('total', '!=', 0)->with(['invoice.company', 'invoice.contract', 'invoice.contract.program']);

    return $query->applyRequestFilters()->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    return $this->builder()
      ->setTableId('authority-invoices-table')
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
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'TaxAuthorityInvoices_' . date('YmdHis');
  }
}
