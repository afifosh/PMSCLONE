<?php

namespace App\DataTables\Admin\Company;

use App\Models\CompanyInvitation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvitationsDataTable extends DataTable
{
  public $company_id;
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
                    <div class="avatar avatar-sm me-3"><img src="' . $row->contactPerson->avatar . '" alt="Avatar" class="rounded-circle">
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <span class="text-body text-truncate">
                      <span class="fw-semibold">' . htmlspecialchars($row->contactPerson->full_name, ENT_QUOTES, 'UTF-8') . '</span>
                    </span>
                    <small class="text-muted">' . htmlspecialchars($row->contactPerson->email, ENT_QUOTES, 'UTF-8') . '</small>
                  </div>
                </div>';
      })
      ->addColumn('company', function ($row) {
        return @$row->contactPerson->company->name ? view('admin._partials.sections.company-avatar', ['company' => $row->contactPerson->company]) : '-';
      })
      ->addColumn('role', function ($row) {
        return $row->role->name;
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function ($invitation) {
        return view('admin.pages.company.invitations.action', compact('invitation'));
      })
      // Search columns
      ->filterColumn('user', function ($query, $keyword) {
        return $query->whereHas('contactPerson', function ($q) use ($keyword) {
          $sql = "CONCAT(company_contact_persons.first_name,' ',company_contact_persons.last_name, ' ',company_contact_persons.email)  like ?";
          return $q->whereRaw($sql, ["%{$keyword}%"]);
        });
      })
      ->filterColumn('role', function ($query, $keyword) {
        $query->whereHas('role', function ($q) use ($keyword) {
          return $q->where('name', 'like',"%{$keyword}%");
        });
      })
      ->rawColumns(['user', 'status']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\CompanyInvitation $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(CompanyInvitation $model): QueryBuilder
  {
    $query = $model->newQuery();

    $query->when(request()->company, function ($query) {
      return $query->whereHas('contactPerson', function ($q) {
        return $q->where('company_id', request()->company->id);
      });
    });

    $query->when(request('filter_status'), function ($q) {
      return $q->whereIn('status', request('filter_status'));
    });

    $query->when(request('filer_roles'), function ($q) {
      return $q->whereHas('contactPerson', function ($cPerson) {
        return $cPerson->whereIn('role_id', request('filer_roles'));
      });
    });

    $query->when(request('filter_companies'), function ($q) {
      return $q->whereHas('contactPerson', function ($cPerson) {
        return $cPerson->whereIn('company_id', request('filter_companies'));
      });
    });

    return $query->with('contactPerson')->orderBy('id', 'DESC');
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'pending':
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
      case 'accepted':
        return '<span class="badge bg-label-success">' . $b_status . '</span>';
        break;
      case 'revoked':
        return '<span class="badge bg-label-secondary">' . $b_status . '</span>';
        break;
      case 'failed':
        return '<span class="badge bg-label-danger">' . $b_status . '</span>';
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
    $this->company_id = @request()->company->id;
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Invitation</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create Invitation',
          'data-href' => route('admin.company-invitations.create', ['company' => request()->company])
        ]
      ];

    // $script = "data.name = 'test'; data.email = $('input[name=email]').val(); data.filter = $('#custom-filter').val();";

    return $this->builder()
      ->setTableId(CompanyInvitation::DT_ID)
      ->columns($this->getColumns())
      ->minifiedAjax($url = '', $script = '', $data = [])
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
  }

  /**
   * Get the dataTable columns definition.
   *
   * @return array
   */
  public function getColumns(): array
  {
    if (!$this->company_id) {
      return [
        Column::make('user'),
        Column::make('company'),
        Column::make('role'),
        Column::make('status'),
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
    }
    return [
      Column::make('user'),
      Column::make('role'),
      Column::make('status'),
      Column::make('created_at'),
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
    return 'Invitations_' . date('YmdHis');
  }
}
