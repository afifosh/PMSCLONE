<?php

namespace App\DataTables\Admin;

use App\Models\Admin;
use App\Services\Core\Setting\General\SettingService;
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
      ->addColumn('user', function ($row) {
        return '<div class="d-flex justify-content-start align-items-center">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3"><img src="' . $row->avatar . '" alt="Avatar" class="rounded-circle">
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <span class="text-body text-truncate">
                    <span class="fw-semibold"><a href="' . route('admin.users.show', $row->id) . '" class="fw-semibold">' . htmlspecialchars($row->full_name, ENT_QUOTES, 'UTF-8') . '</a></span>
                  </span>
                  <small class="text-muted">' . htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8') . '</small>
                </div>
              </div>';
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function (Admin $admin) {
        return view('admin.pages.roles.admins.action', compact('admin'));
      })
      ->addColumn('2f-auth', function ($row) {
        return $row->two_factor_confirmed_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->addColumn('organization', function ($row) {
        return @$row->designation->department->company->name ? view('admin._partials.sections.company-avatar', ['company' => @$row->designation->department->company]) : '-';
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->addColumn('pwd_expires', function ($row) {
        $security = app(SettingService::class)->getFormattedSettings('security');
        $passwordChangedAt = new Carbon(($row->password_changed_at) ? $row->password_changed_at : $row->created_at);
        
        $passwordExpiresInDays = $security['password_expire_days'] ?? config('auth.password_expire_days');

        $days = $passwordExpiresInDays - Carbon::now()->diffInDays($passwordChangedAt);

        return $days > 0 ? "{$days} days" : __('Expired');
      })
      ->editColumn('email_verified_at', function ($row) {
        return $row->email_verified_at ? '<i class="ti fs-4 ti-shield-check text-success"></i>' : '<i class="ti fs-4 ti-shield-x text-danger"></i>';
      })
      ->filterColumn('user', function ($query, $keyword) {
        $sql = "CONCAT(admins.first_name,' ',admins.last_name, ' ',admins.email)  like ?";
        $query->whereRaw($sql, ["%{$keyword}%"]);
      })
      ->filterColumn('organization', function ($query, $keyword) {
        $query->whereHas('designation.department.company', function ($q) use ($keyword) {
          return $q->where('name', 'like', '%' . $keyword . '%');
        });
      })
      ->filterColumn('roles', function ($query, $keyword) {
        $query->whereHas('roles', function ($q) use ($keyword) {
          return $q->where('name', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['user', '2f-auth', 'pwd_expires', 'action', 'email_verified_at', 'status']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Admin $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Admin $model): QueryBuilder
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
    $query->when(request('filter_companies'), function ($q) {
      return $q->whereHas('designation.department', function ($dep) {
        return $dep->whereIn('company_id', request('filter_companies'));
      });
    });
    return $query->select(['admins.*', DB::raw("CONCAT(admins.first_name,' ',admins.last_name) as full_name")])->with('roles');
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'active':
        return '<span class="badge bg-label-success">' . $b_status . '</span>';
        break;
      case 'suspended':
        return '<span class="badge bg-label-secondary">' . $b_status . '</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
    }
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
          'data-toggle' => "ajax-modal",
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
      Column::make('user'),
      Column::make('phone'),
      Column::make('organization'),
      Column::make('roles'),
      Column::make('status'),
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
