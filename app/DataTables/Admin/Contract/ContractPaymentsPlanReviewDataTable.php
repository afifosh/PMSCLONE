<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Admin;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;


class ContractPaymentsPlanReviewDataTable extends DataTable
{
    protected $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function ($user) {
                return $user ? view('admin._partials.sections.user-info', ['user' => $user]) : '-';
            })
            ->addColumn('review_status', function ($user) {
                return view('admin.pages.contracts.tracking.paymentsplan.review', [
                    'status' => $user->getReviewStatusForContract($this->contract->id),
                ])->render();
            })
            ->rawColumns(['review_status']);
    }

    public function query(): QueryBuilder
    {
      // Get the associated program and users with access to the contract
      $program = $this->contract->program;
      if (!$program) {
          // Handle the case where $program is null, e.g., by returning an empty query or an error response.
          return Admin::where('id', '=', 0)->newQuery(); // Example: Return an empty query.
      }

      return Admin::whereHas('accessiblePrograms', function ($q) use ($program) {
          $q->where('accessable_id', $program->id);
      });
    }

    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('contract-review-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom(
                '<"html5buttons"B>lTfgitp'
            )
            ->addAction(['width' => '80px']);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Name'),
            Column::make('review_status')->title('Review Status')->orderable(false)->searchable(false),
            // Add more columns as needed...
        ];
    }

    protected function filename(): string
    {
        return 'ContractPaymentsPlanReviewDataTable_' . date('YmdHis');
    }
}
