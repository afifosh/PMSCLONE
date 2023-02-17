<?php

namespace App\DataTables\Admin\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
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
      ->addColumn('user', function ($row) {
        return '<div class="d-flex justify-content-start align-items-center">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3"><img src="'.$row->avatar.'" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <span class="text-body text-truncate">
                      <span class="fw-semibold"><a href="'.route('admin.company-users.show', $row->id).'">'.htmlspecialchars($row->full_name, ENT_QUOTES, 'UTF-8').'</a></span>
                    </span>
                    <small class="text-muted">'.htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8').'</small>
                  </div>
                </div>';
      })
      ->addColumn('company', function (User $user) {
        return $user->company->name ?? '-';
      })
      ->editColumn('status', function($row){
        return $this->makeStatus($row->status);
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->addColumn('2f-auth', function ($row) {
        return $row->two_factor_confirmed_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->editColumn('email_verified_at', function ($row) {
        return $row->email_verified_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->addColumn('action', function (User $user) {
        return view('pages.users.action', compact('user'));
      })
      ->filterColumn('user', function($query, $keyword) {
        $sql = "CONCAT(users.first_name,' ',users.last_name, ' ',users.email)  like ?";
        $query->whereRaw($sql, ["%{$keyword}%"]);
      })
      ->filterColumn('company', function($query, $keyword) {
        $query->whereHas('company', function($q) use ($keyword){
          return $q->where('name', 'like', '%'.$keyword.'%');
        });
      })
      ->filterColumn('roles', function($query, $keyword) {
        $query->whereHas('roles', function($q) use ($keyword){
          return $q->where('name', 'like', '%'.$keyword.'%');
        });
      })
      ->setRowId('id')
      ->rawColumns(['user', 'status', '2f-auth', 'action', 'email_verified_at']);
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'active':
        return '<span class="badge bg-label-success">'.$b_status.'</span>';
        break;
      case 'suspended':
        return '<span class="badge bg-label-secondary">'.$b_status.'</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
    }
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\User $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(User $model): QueryBuilder
  {
    $query = $model->query();
    $query->when(request('filter_status'), function ($query) {
      return $query->whereIn('status', request('filter_status'));
    });
    $query->when(request('filer_roles'), function ($query) {
      return $query->whereHas('roles', function ($whas) {
        return $whas->whereIn('name', request('filer_roles'));
      });
    });
    $query->when(request('filter_companies'), function($q){
      $q->whereIn('company_id', request('filter_companies'));
    });
    return $query;
    // return $model->with('roles');
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
    //   $buttons[] = [
    //     'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New User</span>',
    //     'className' =>  'btn btn-primary mx-3',
    //     'attr' => [
    //       'data-toggle' => "ajax-modal",
    //       'data-title' => 'Add User',
    //       'data-href' => route('users.create')
    //     ]
    //   ];
    $script = "data.companies = $('input[name=filter_companies]'); data.status = $('input[name=filter_status]').val(); data.roles = $('input[name=filer_roles]').val();";
    return $this->builder()
      ->setTableId(User::DT_ID)
      ->columns($this->getColumns())
      ->minifiedAjax('', $script = '', $data = [])
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
    // ->language(['sLengthMenu' => '_MENU_',
    // 'search' => '',
    // 'searchPlaceholder' => 'Search..']);
    //   ->parameters([
    //     'buttons'      => ['export', 'print', 'reset', 'reload'],
    // ]);
    // ->selectStyleSingle();
    // ->buttons([
    //     Button::make('excel'),
    //     Button::make('csv'),
    //     Button::make('pdf'),
    //     Button::make('print'),
    //     Button::make('reset'),
    //     Button::make('reload')
    // ]);
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
      Column::make('phone'),
      Column::make('company'),
      Column::make('roles'),
      Column::make('status'),
      Column::make('email_verified_at')->title(__('Verified')),
      Column::make('2f-auth')
    ];
  }

  /**
   * Get filename for export.
   *
   * @return string
   */
  protected function filename(): string
  {
    return 'Users_' . date('YmdHis');
  }
}
