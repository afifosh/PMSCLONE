<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmailServiceRequest;
use App\Models\EmailService;
use App\Traits\EmailService as TraitsEmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EmailServiceController extends Controller
{
    use TraitsEmailService;

    /**
     * Stores email request
     * 
     * @param EmailServiceRequest $request
     * @return Redirect
     */
    public function upsert(EmailServiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->updateEmailService(
                EmailService::where('service', $request->service)->first(),
                $request->except(['service', '_token'])
            );

            DB::commit();

            return redirect()->back()->with([
                'status' => __('Email settings updated successfully')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'status' => __($e->getMessage())
            ]);
        }
    }
}
