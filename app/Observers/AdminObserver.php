<?php

namespace App\Observers;

use App\Models\Admin;
use Illuminate\Support\Facades\Log;

class AdminObserver
{
    /**
     * Handle the Admin "created" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function created(Admin $admin)
    {
        $admin->passwordHistories()->create([
            'password' => $admin->password
        ]);
    }

    /**
     * Handle the Admin "updated" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        if(! $admin->isDirty('password')) {
            return;
        }
        
        // if threshold reached, delete one record
        if ($admin->passwordHistories()->count() === config('auth.password_history_count')) {
            $admin->passwordHistories()->first()->delete();
        }

        $admin->passwordHistories()->create([
            'password' => $admin->password
        ]);
    }

    /**
     * Handle the Admin "deleted" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {
        //
    }

    /**
     * Handle the Admin "restored" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function restored(Admin $admin)
    {
        //
    }

    /**
     * Handle the Admin "force deleted" event.
     *
     * @param  \App\Models\Admin  $admin
     * @return void
     */
    public function forceDeleted(Admin $admin)
    {
        $admin->passwordHistories()->delete();
    }
}
