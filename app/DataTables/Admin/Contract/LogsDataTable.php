<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Audit;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LogsDataTable extends DataTable
{
  /**
   * App\Models\Contract $contract
   */
  public $contract;

  /**
   * App\Models\Contract\ContractStage $stage
   */
  public $stage;

  /**
   * App\Models\Contract\ContractPhase $phase
   */
  public $phase;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('user', function ($log) {
        return view('admin._partials.sections.user-info', ['user' => $log->user]);
      })
      ->addColumn('event', function ($log) {
        return ucwords($log->event) . ' ' . explode('App\\Models\\', $log->auditable_type)[1];
      })
      ->addColumn('changes', function ($log) {
        return view('admin.pages.contracts.logs.audit-log', ['log' => $log]);
      })
      ->filterColumn('user', function ($query, $keyword) {
        $query->whereHas('user', function ($query) use ($keyword) {
          $query->where('first_name', 'like', "%{$keyword}%")->orWhere('last_name', 'like', "%{$keyword}%")->orWhere('email', 'like', "%{$keyword}%");
        });
      })
      ->filterColumn('changes', function ($query, $keyword) {
        $query->where('old_values', 'like', "%{$keyword}%")->orWhere('new_values', 'like', "%{$keyword}%");
      })
      ->filterColumn('event', function ($query, $keyword) {
        $query->where('event', 'like', "%{$keyword}%")->orWhere('auditable_type', 'like', "%{$keyword}%");
      })
      ->orderColumn('event', function ($query, $order) {
        $query->orderBy('event', $order)->orderBy('auditable_type', $order);
      })
      ->orderColumn('changes', function ($query, $order) {
        $query->orderBy('old_values', $order)->orderBy('new_values', $order);
      })
      ->orderColumn('ip_address', function ($query, $order) {
        $query->orderBy('ip_address', $order);
      })
      ->setRowId('id');
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Audit $model): QueryBuilder
  {
    return $model->when($this->contract && $this->contract->id, function ($q) {
      $q->ofContract($this->contract->id);
    })->with('user')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];

    return $this->builder()
      ->setTableId('events-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
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
      Column::make('id'),
      Column::make('user')->title('Actioner'),
      Column::make('event')->title('Action'),
      Column::make('changes'),
      Column::make('ip_address')->title('IP Address'),
      Column::make('created_at')->title('Performed at'),
      // Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Logs_' . date('YmdHis');
  }
}
