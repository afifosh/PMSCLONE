<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmailServiceRequest;
use App\Models\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EmailServiceController extends Controller
{
    /**
     * Stores email request
     * 
     * @param EmailServiceRequest $request
     * @return Redirect
     */
    public function upsert(EmailServiceRequest $request)
    {
        $service = EmailService::where('service', $request->service)->first();

        $request->except(['service']);

        try {
            DB::beginTransaction();

            foreach ($request->except(['service', '_token']) as $field_name => $input) {
                if (is_null($input)) {
                    continue;
                }

                $service->emailServiceFields()->updateOrCreate([
                    'field_name' => $field_name
                ], [
                    'field_name' => $field_name,
                    'field_value' => $input,
                ]);
            }

            EmailService::query()->update(['is_active' => false]);

            $service->update(['is_active' => 1]);

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
