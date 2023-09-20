<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Invoice extends Model
{
  use HasFactory;

  protected $fillable = [
    'company_id',
    'contract_id',
    'invoice_date',
    'due_date',
    'sent_date',
    'creator_type',
    'creator_id',
    'subtotal',
    'note',
    'terms',
  ];

  protected $casts = [
    'invoice_date' => 'datetime:d M, Y',
    'due_date' => 'datetime:d M, Y',
    'sent_date' => 'datetime:d M, Y',
    'created_at' => 'datetime:d M, Y',
    'updated_at' => 'datetime:d M, Y',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function contract()
  {
    return $this->belongsTo(Contract::class);
  }

  public function creator()
  {
    return $this->morphTo();
  }

  public function items()
  {
    return $this->hasMany(InvoiceItem::class);
  }

  public function milestones()
  {
    return $this->morphedByMany(ContractMilestone::class, 'invoiceable', 'invoice_items', 'invoice_id', 'invoiceable_id')->withPivot('amount', 'description');
    // return $this->belongsToMany(ContractMilestone::class, 'invoice_items', 'invoice_id', 'invoiceable_id')->where('invoiceable_type', ContractMilestone::class);
  }
}
