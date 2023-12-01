<?php

namespace App\DataTables\Admin\AccessList;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class AdminAccessListsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('user', function ($user) {
        return view('admin._partials.sections.user-info', ['user' => $user]);
      })
      ->addColumn('programs', function ($user) {
        return view('admin._partials.sections.programs-avatar-group', ['programs' => $user->accessiblePrograms]);
      })
      ->addColumn('action', function ($user) {
        return view('admin.pages.access-lists.action', compact('user'));
      })
      ->addColumn('roles', function ($row) {
        return $row->roles->pluck('name')->implode(', ');
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
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
        $passwordChangedAt = new Carbon(($row->password_changed_at) ? $row->password_changed_at : $row->created_at);

        $passwordExpiresInDays = config('auth.password_expire_days');

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
   * Get the query source of dataTable.
   */
  public function query(Admin $model): QueryBuilder
  {
    return $model->has('accessiblePrograms')->with('accessiblePrograms:name')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add User</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add User',
        'data-href' => route('admin.admin-access-lists.create')
      ]
    ];

    return $this->builder()
      ->setTableId('admin-access-lists-table')
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
      ->orderBy([0, 'DESC'])
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true,
        "drawCallback" => "function (settings) {
          $('[data-bs-toggle=\"tooltip\"]').tooltip();
        }"
      ]);
  }

  /**
   * Get the dataTable columns definition.
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
      Column::make('pwd_expires'),
      Column::make('programs')->title('Accessible Programs')
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'AdminAccessLists_' . date('YmdHis');
  }
}
