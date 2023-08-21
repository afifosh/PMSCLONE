<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractPaymentSchedule;
use Illuminate\Http\Request;

class PaymentScheduleController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Contract $contract)
  {
    $data['contract'] = $contract;
    $data['paymentSchedule'] = new ContractPaymentSchedule();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.payment-schedules.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(ContractPaymentSchedule $contractPaymentSchedule)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ContractPaymentSchedule $contractPaymentSchedule)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ContractPaymentSchedule $contractPaymentSchedule)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ContractPaymentSchedule $contractPaymentSchedule)
  {
    //
  }
}
