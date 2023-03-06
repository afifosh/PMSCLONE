<?php
namespace App\Traits;

use App\Models\Scopes\TenancyScope as TenancyScop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenancyScope {
    public static function boot()
    {
      parent::boot();
      static::addGlobalScope(new TenancyScop(Auth::user()));
    }
}
