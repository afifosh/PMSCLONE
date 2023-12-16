<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ContractACLAccessibleByAuth implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(function ($q) {
          $q->whereHas('validProgramACLRules', function ($q) {
            $q->where('admin_id', auth()->id());
          })
            ->orWhereHas('validDirectACLRules', function ($q) {
              $q->where('admin_id', auth()->id());
            });
        })
        ->whereDoesntHave('invalidDirectACLRules', function ($q) {
          $q->where('admin_id', auth()->id());
        });
    }
}
