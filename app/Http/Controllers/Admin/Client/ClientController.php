<?php

namespace App\Http\Controllers\Admin\Client;

use App\DataTables\Admin\ClientsDataTable;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Country;
use Illuminate\Http\Request;

class ClientController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ClientsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.clients.index');
    // view('admin.pages.clients.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['client'] = new Client();
    $accountController = new AdminAccountController();
    $data['timezones'] = $accountController->timezones();
    $data['languages'] = $accountController->languages();
    $data['currencies'] = $accountController->currencies();
    $data['countries'] = Country::get(['id', 'name']);

    return $this->sendRes('success', ['view_data' => view('admin.pages.clients.edit', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'first_name' => 'required|string|max:100',
      'last_name' => 'required|string|max:100',
      'phone' => 'required|string|max:25',
      'email' => 'required|unique:clients,email|email|max:100',
      'address' => 'required|string|max:255',
      'state' => 'required|string|max:100',
      'zip_code' => 'required|string|max:25',
      'language' => 'required|string|max:25',
      'country_id' => 'required|integer|exists:countries,id',
      'currency' => 'required|string|max:100',
      'timezone' => 'required|string|max:100',
      'status' => 'required|string|max:100',
    ]);

    Client::create($request->all());

    return $this->sendRes('Client Created Successfully', ['event' => 'table_reload', 'table_id' => 'clients-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Client $client)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Client $client)
  {
    $data['client'] = $client;
    $accountController = new AdminAccountController();
    $data['timezones'] = $accountController->timezones();
    $data['languages'] = $accountController->languages();
    $data['currencies'] = $accountController->currencies();
    $data['countries'] = Country::get(['id', 'name']);

    return $this->sendRes('success', ['view_data' => view('admin.pages.clients.edit', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Client $client)
  {
    $request->validate([
      'first_name' => 'required|string|max:100',
      'last_name' => 'required|string|max:100',
      'phone' => 'required|string|max:25',
      'email' => 'required|unique:clients,email,' . $client->id . '|email|max:100',
      'address' => 'required|string|max:255',
      'state' => 'required|string|max:100',
      'zip_code' => 'required|string|max:25',
      'language' => 'required|string|max:25',
      'country_id' => 'required|integer|exists:countries,id',
      'currency' => 'required|string|max:100',
      'timezone' => 'required|string|max:100',
      'status' => 'required|string|max:100',
    ]);

    $client->update($request->all());

    return $this->sendRes('Client Updated Successfully', ['event' => 'table_reload', 'table_id' => 'clients-table', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Client $client)
  {
    $client->delete();

    return $this->sendRes('Client Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'clients-table']);
  }
}
