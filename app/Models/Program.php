<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Akaunting\Money\Money;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\Interfaces\AccountBalanceHolderInterface;
use Avatar;

class Program extends BaseModel implements AccountBalanceHolderInterface
{
    use HasFactory;

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
      })->orWhereHas('parent.users', function($q){
        return $q->where('admins.id', auth()->id());
      });
    }

    public function programUsers()
    {
      return Admin::whereHas('programs', function($q){
        return $q->where('programs.id', $this->id);
      })->orWhereHas('programs', function($q){
        return $q->where('programs.id', $this->parent_id);
      })->get();
    }

    public function users()
    {
      return $this->belongsToMany(Admin::class, ProgramUser::class, 'program_id', 'admin_id')->withTimestamps();
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
