<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;
use Avatar;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Program extends BaseModel implements AccountBalanceHolderInterface
{
    use HasFactory, HasRecursiveRelationships;

    public const DT_ID = 'programs-dataTable';

    protected $fillable = ['parent_id', 'name', 'image', 'program_code', 'description'];

    protected $casts = [
      'created_at' => 'datetime:d M, Y',
      'updated_at' => 'datetime:d M, Y',
    ];

    public function scopeMine($query){
      if(auth('admin')->check() && auth('admin')->id() == 1){
        return $query;
      }
      return $query->whereHas('users', function($q){
        return $q->where('admins.id', auth()->id());
      });
    }

    public function scopeApplyRequestFilters($query)
    {
      $query->when(request()->has('hasContract'), function ($q){
        $q->has('contracts');
      });
    }


    public function users()
    {
      return $this->belongsToMany(Admin::class, AdminAccessList::class, 'accessable_id', 'admin_id')
                  ->where('accessable_type', self::class)
                  ->withPivot('granted_till')
                  ->withTimestamps();
    }

    public function pivotAccessLists()
    {
      return $this->hasMany(AdminAccessList::class, 'accessable_id', 'id')->where('accessable_type', self::class);
    }

    /**
     * Scope a query to only include programs accessible by the specified admin.
     */
    public function scopeAccessibleByAdmin($query, $admin_id)
    {
      return $query->whereHas('users', function ($q) use ($admin_id) {
        $q->where('admins.id', $admin_id);
      })
      ->with(['pivotAccessLists' => function ($q) use ($admin_id) {
        $q->where('admin_id', $admin_id);
      }]);
    }

    public function parent()
    {
      return $this->belongsTo(Program::class, 'parent_id', 'id');
    }

    public function contracts(): HasMany
    {
      return $this->hasMany(Contract::class, 'program_id', 'id');
    }

    public function accountBalances()
  {
    return $this->morphMany(AccountBalance::class, 'holder');
  }

  public function defaultCurrencyAccount(): HasOne
  {
    return $this->hasOne(AccountBalance::class, 'holder_id')->where('holder_type', self::class);
  }

  public function getAccount(string $currency): ?AccountBalance
  {
    return $this->accountBalances()->where('currency', $currency)->first();
  }

  public function addAccountBalance(AccountBalance $accountBalance)
  {
    $accountBalance->holder()->associate($this);
    $accountBalance->save();
  }

  public function getAvatarAttribute($value)
  {
    if(!$value)
      return Avatar::create($this->program_code ? $this->program_code : $this->name)->toBase64();
    return $value;
  }

}
