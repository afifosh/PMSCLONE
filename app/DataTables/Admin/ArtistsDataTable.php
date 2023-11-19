<?php

namespace App\DataTables\Admin;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ArtistsDataTable extends DataTable
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
      ->addColumn('full_name', function ($row) {
        return view('admin.pages.artist.artist-info', ['user' => $row]);
      })
      ->editColumn('added_by', function ($artist) {
        return $artist->addedBy ? view('admin._partials.sections.user-info', ['user' => $artist->addedBy]) : '-';
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function (Artist $artist) {
        return view('admin.pages.artist.action', compact('artist'));
      })
      ->addColumn('gender', function ($artist) {
        return $artist->gender;
      })
      ->addColumn('country', function ($artist) {
        $countryName = $countryName = $artist->country ? ucfirst($artist->country->name) : '-';
        return $countryName;
      })
      ->addColumn('age', function ($artist) {
        return $artist->age;
      })
      ->filterColumn('added_by', function ($query, $keyword) {
        return $query->whereHas('addedBy', function ($q) use ($keyword) {
          return $q->where('email', 'like', '%' . $keyword . '%')
            ->orWhere('first_name', 'like', '%' . $keyword . '%')
            ->orWhere('last_name', 'like', '%' . $keyword . '%');
        });
      })
      ->setRowId('id')
      ->rawColumns(['full_name', 'action', 'status']);
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'active':
        return '<span class="badge bg-label-success">' . $b_status . '</span>';
        break;
      case 'pending':
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
      case 'disabled':
        return '<span class="badge bg-label-secondary">' . $b_status . '</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
    }
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Artist $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Artist $model): QueryBuilder
  {
    $query = $model->newQuery();

    // Add a select clause to concatenate first_name and last_name as 'full_name'
    $query->select(['artists.*', DB::raw("CONCAT(artists.first_name, ' ', artists.last_name) as full_name")]);
    // Include the 'country' relationship
    $query->with('country');

    return $query; // Return the modified query

  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Artist</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Arist',
        'data-href' => route('admin.artists.create')
      ]
    ];
    return $this->builder()
      ->setTableId(Artist::DT_ID)
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
      // Column::make('id'),
      Column::make('full_name')->title(__('Artist Name')),
      Column::make('gender'),
      Column::make('age')->title(__('Age')),
      Column::make('country'),
      Column::make('added_by'),
      Column::make('status'),
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
    return 'Artists_' . date('YmdHis');
  }
}
