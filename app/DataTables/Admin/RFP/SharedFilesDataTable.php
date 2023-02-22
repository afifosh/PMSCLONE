<?php

namespace App\DataTables\Admin\RFP;

use App\Models\FileShare;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SharedFilesDataTable extends DataTable
{

  protected $draft_rfp;

  public function setDraftRFP($draft_rfp)
  {
    $this->draft_rfp = $draft_rfp;
  }
  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('action', 'sharedfiles.action')
      ->addColumn('user', function ($row) {
        return view('admin._partials.sections.user-info', ['user' => $row->user]);
      })
      ->addColumn('shared_by', function ($row) {
        return view('admin._partials.sections.user-info', ['user' => $row->sharedBy]);
      })
      ->addColumn('file', function ($row) {
        return view('admin.pages.rfp.file-share.file-editor', compact('row'));
      })
      ->editColumn('permission', function ($row) {
        return '<span class="badge bg-label-success">' . ucfirst($row->permission) . '</span>';
      })
      ->addColumn('expires_at', function ($row) {
        if ($row->expires_at) {
          return $row->expires_at < today() ? '<span class="badge bg-label-danger">' . $row->expires_at->format('d M, Y') . '</span>' : '<span class="badge bg-label-warning">' . $row->expires_at->format('d M, Y') . '</span>';
        } else {
          return '<span class="badge bg-label-warning"> Never</span>';
        }
      })
      ->addColumn('status', function ($row){
        if($row->is_revoked){
          return '<span class="badge bg-label-danger">Revoked</span>';
        }elseif($row->expires_at && $row->expires_at < today()){
          return '<span class="badge bg-label-danger">Expired</span>';
        }else{
          return '<span class="badge bg-label-success">Active</span>';
        }
      })
      ->addColumn('action', function ($fileShare) {
        return view('admin.pages.rfp.file-share.action', compact('fileShare'));
      })
      ->filterColumn('user', function ($query, $keyword) {
        $sql = "CONCAT(admins.first_name,' ',admins.last_name, ' ',admins.email)  like ?";
        $query->whereHas('user', function ($query) use ($sql, $keyword) {
          $query->whereRaw($sql, ["%{$keyword}%"]);
        });
      })
      ->filterColumn('file', function ($query, $keyword) {
        $query->whereHas('file', function ($query) use ($keyword) {
          $query->where('title', 'like', "%{$keyword}%");
        });
      })
      ->rawColumns(['user', 'action', 'expires_at', 'permission', 'status']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\FileShare $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(FileShare $model): QueryBuilder
  {
    $query = $model->whereHas('file', function ($q) {
      $q->where('rfp_id', $this->draft_rfp->id);
    })->latest()->newQuery();
    return $query->applyRequestFilters()->with(['user', 'sharedBy', 'file']);
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('sharedfiles-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      //->dom('Bfrtip')
      ->orderBy(1)
      ->addAction(['width' => '80px'])
      // ->selectStyleSingle()
      ->buttons([
        Button::make('excel'),
        Button::make('csv'),
        Button::make('pdf'),
        Button::make('print'),
        Button::make('reset'),
        Button::make('reload')
      ])
      ->parameters(
        [
          "drawCallback" => "function (settings) {
            $('[data-toggle=\"tooltip\"]').tooltip();
          }"
        ]
      );
  }

  /**
   * Get the dataTable columns definition.
   *
   * @return array
   */
  public function getColumns(): array
  {
    return [
      Column::make('shared_by')->title('Shared By'),
      Column::make('user')->title('Shared With'),
      Column::make('file'),
      Column::make('permission'),
      Column::make('expires_at')->title('Expires At'),
      Column::make('status')->title('Status')->orderable(false)->searchable(false),
      Column::make('created_at')->title('Shared At'),
    ];
  }

  /**
   * Get filename for export.
   *
   * @return string
   */
  protected function filename(): string
  {
    return 'SharedFiles_' . date('YmdHis');
  }
}
