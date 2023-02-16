<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait PasswordHistory
{
    /**
     * Create password history
     * 
     * @param Model $model
     */
    public function storePasswordHistory(Model $model)
    {
        if (! method_exists($model, 'passwordHistories')) {
            return throw ('Relationship is not defined');
        }

        $model->passwordHistories()->create([
            'password' => $model->password
        ]);
    }

    /**
     * Update password history
     * 
     * @param Model $model
     */
    public function updatePasswordHistory(Model $model)
    {
        if (!$model->isDirty('password')) {
            return;
        }

        if (! method_exists($model, 'passwordHistories')) {
            return throw ('Relationship is not defined');
        }

        // if threshold reached, delete one record
        if ($model->passwordHistories()->count() === config('auth.password_history_count')) {
            $model->passwordHistories()->first()->delete();
        }

        $model->passwordHistories()->create([
            'password' => $model->password
        ]);
    }

    /**
     * Delete password history
     * 
     * @param Model $model
     */
    public function deletePasswordHistory(Model $model)
    {
        $model->passwordHistories()->delete();
    }
}
