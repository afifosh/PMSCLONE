<?php

namespace App\Models;

use App\Traits\Approval\CompanyApprovalBaseLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyKycDoc extends Model
{
    use HasFactory;
    //  CompanyApprovalBaseLogic;

    protected $fillable = [
        'company_id',
        'kyc_doc_id',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public const FILE_PATH = 'kyc-docs/company';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function kycDoc()
    {
        return $this->belongsTo(KycDocument::class);
    }
}
