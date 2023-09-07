<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractEvent;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EventsDataTable extends DataTable
{
  public $contract;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('actioner', function ($event) {
        if(!$event->actioner)
        return 'System';

        return view('admin._partials.sections.user-info', ['user' => $event->actioner]);
      })
      // ->addColumn('action', function($event){
      //   return view('admin.pages.contracts.events.action', ['event' => $event]);
      // })
      ->filterColumn('actioner', function ($query, $keyword) {
        $query->whereHas('actioner', function ($query) use ($keyword) {
          $query->where('first_name', 'like', '%' . $keyword . '%')
            ->orWhere('last_name', 'like', '%' . $keyword . '%')
            ->orWhere('email', 'like', '%' . $keyword . '%');
        });
      });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractEvent $model): QueryBuilder
  {
    return $model->when($this->contract, function ($q) {
      $q->where('contract_id', $this->contract->id);
    })
    ->when(request()->filter_actioners, function ($q) {
      $q->whereHas('actioner', function ($query) {
        $query->whereIn('id', request()->filter_actioners);
      });
    })
    ->when(request()->filter_event_types, function ($q) {
      $q->whereIn('event_type', request()->filter_event_types);
    })
    ->with('actioner')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if($this->contract->getRawOriginal('status') == 'Active')
    $buttons[] = [
      'text' => '<span>Edit Terms</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Edit Contract Terms',
        'data-href' => route('admin.contracts.terms.edit', [$this->contract->id, 0])
      ]
    ];

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
      // ->addAction(['width' => '80px'])
      ->orderBy(0, 'DESC')
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
      Column::make('actioner')->title('Performed By')->orderable(false),
      Column::make('event_type')->title('Type'),
      Column::make('description'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Events_' . date('YmdHis');
  }
}
