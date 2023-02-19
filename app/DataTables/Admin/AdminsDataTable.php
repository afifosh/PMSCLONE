<?php

namespace App\DataTables\Admin;

use App\Models\Admin;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdminsDataTable extends DataTable
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
      ->editColumn('full_name', function ($row) {
        return "<img class='avatar avatar-sm pull-up rounded-circle' src='$row->avatar' alt='Avatar'><span class='mx-2'>".htmlspecialchars($row->full_name, ENT_QUOTES, 'UTF-8')."</span>";
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->addColumn('action', function (Admin $admin) {
        return view('admin.pages.roles.admins.action', compact('admin'));
      })
      ->addColumn('2f-auth', function ($row) {
        return $row->two_factor_confirmed_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->addColumn('organization', function ($row) {
        return @$row->designation->department->company->name;
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->addColumn('pwd_expires', function ($row) {
        $app_settings = AppSetting::first();
        $password_changed_at = new Carbon(($row->password_changed_at) ? $row->password_changed_at : $row->created_at);

        $password_expire_days_setting =
            isset($app_settings->password_expire_days) && ! is_null($app_settings->password_expire_days)
            ? $app_settings->password_expire_days
            : config('auth.password_expire_days');

        $days = $password_expire_days_setting - Carbon::now()->diffInDays($password_changed_at);

        return $days > 0 ? "{$days} days" : __('Expired');
      })
      ->editColumn('email_verified_at', function ($row) {
        return $row->email_verified_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->filterColumn('full_name', function($query, $keyword) {
        $sql = "CONCAT(admins.first_name,' ',admins.last_name)  like ?";
        $query->whereRaw($sql, ["%{$keyword}%"]);
    })
      ->setRowId('id')
      ->rawColumns(['full_name','2f-auth', 'pwd_expires', 'action', 'email_verified_at']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Admin $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Admin $model): QueryBuilder
  {
    return $model->select(['admins.*', DB::raw("CONCAT(admins.first_name,' ',admins.last_name) as full_name")])->with('roles');
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */

  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can('create user'))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New User</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-offcanvas",
          'data-title' => 'Add User',
          'data-href' => route('admin.users.create')
        ]
      ];

    return $this->builder()
      ->setTableId('admins-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
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
      // Column::computed('action')
      //       ->exportable(true)
      //       ->printable(true)
      //       ->width(60)
      //       ->addClass('text-center'),
      // Column::make('id'),
      Column::make('full_name'),
      // Column::make('last_name'),
      Column::make('email'),
      Column::make('phone'),
      Column::make('organization'),
      Column::make('roles'),
      // Column::make('roles'),
      Column::make('email_verified_at')->title(__('Verified')),
      Column::make('2f-auth'),
      Column::make('pwd_expires')
    ];
  }

  /**
   * Get filename for export.
   *
   * @return string
   */
  protected function filename(): string
  {
    return 'Admins_' . date('YmdHis');
  }
}
