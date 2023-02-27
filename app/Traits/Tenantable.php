<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Tenantable
{
  public static function bootTenantable()
  {
    if (auth('web')->check()) {
      static::creating(function ($model) {
        $model->company_id = auth('web')->user()->company_id;
      });
      static::addGlobalScope('company_id', function (Builder $builder) {
        if (auth('web')->check()) {
          return $builder->where('company_id', auth('web')->user()->company_id);
        }
      });
    }
  }
}
