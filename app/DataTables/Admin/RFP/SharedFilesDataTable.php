<?php

namespace App\DataTables\Admin\RFP;

use App\Models\FileShare;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
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
        return '<div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3"><img src="' . $row->user->avatar . '" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <span class="text-body text-truncate">
                      <span class="fw-semibold"><a href="' . route('admin.users.show', $row->id) . '">' . htmlspecialchars($row->user->full_name, ENT_QUOTES, 'UTF-8') . '</a></span>
                    </span>
                    <small class="text-muted">' . htmlspecialchars($row->user->email, ENT_QUOTES, 'UTF-8') . '</small>
                  </div>
                </div>';
      })
      ->addColumn('file', function ($row) {
        return $row->file->title;
      })
      ->editColumn('permission', function ($row) {
        return '<span class="badge bg-label-success">'.ucfirst($row->permission).'</span>';
      })
      ->addColumn('expires_at', function ($row) {
        if ($row->expires_at) {
          return $row->expires_at->isPast() ? '<span class="badge bg-label-danger">Expired '.$row->expires_at->format('d M, Y').'</span>' : '<span class="badge bg-label-warning">' . $row->expires_at->format('d M, Y') . '</span>';
        } else {
          return '<span class="badge bg-label-warning"> Never</span>';
        }
      })
      ->addColumn('action', function($fileShare){
        return view('admin.pages.rfp.file-share.action', compact('fileShare'));
      })
      ->filterColumn('user', function ($query, $keyword) {
        $sql = "CONCAT(admins.first_name,' ',admins.last_name, ' ',admins.email)  like ?";
        $query->whereHas('user', function ($query) use ($sql, $keyword) {
          $query->whereRaw($sql, ["%{$keyword}%"]);
        });
      })
      ->filterColumn('file', function($query, $keyword){
        $query->whereHas('file', function($query) use ($keyword){
          $query->where('title', 'like', "%{$keyword}%");
        });
      })
      ->rawColumns(['user','action', 'expires_at', 'permission']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\FileShare $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(FileShare $model): QueryBuilder
  {
    return $model->whereHas('file', function ($q) {
      $q->where('rfp_id', $this->draft_rfp->id);
    })->latest()->newQuery();
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
      Column::make('user'),
      Column::make('file'),
      Column::make('permission'),
      Column::make('expires_at')->title('Expires At'),
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
