<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClient extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_clients';

    protected $fillable = [
        'client_code',
        'practice_id',
        'company_name',
        'trading_name',
        'registration_number',
        'company_type_id',
        'company_status_id',
        'industry_id',
        'sic_code_id',
        'profit_code',
        'loss_code',
        'bee_status_id',
        'tax_number',
        'vat_number',
        'is_vat_registered',
        'paye_number',
        'sdl_number',
        'uif_number',
        'coida_number',
        'financial_year_end',
        'date_incorporated',
        'date_commenced_trading',
        'client_logo',
        'watermark_logo',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_vat_registered' => 'boolean',
        'date_incorporated' => 'date',
        'date_commenced_trading' => 'date',
        'financial_year_end' => 'integer',
    ];

    public function companyType()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemCompanyType::class, 'company_type_id');
    }

    public function companyStatus()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemCompanyStatus::class, 'company_status_id');
    }

    public function industry()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemIndustry::class, 'industry_id');
    }

    public function sicCode()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSicCode::class, 'sic_code_id');
    }

    public function beeStatus()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemBeeStatusLevel::class, 'bee_status_id');
    }

    public function addresses()
    {
        return $this->hasMany(NexcoreClientAddress::class, 'client_id');
    }

    public function contacts()
    {
        return $this->hasMany(NexcoreClientContact::class, 'client_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(NexcoreClientBank::class, 'client_id');
    }

    public function directors()
    {
        return $this->hasMany(NexcoreClientDirector::class, 'client_id');
    }

    public function sarsReturns()
    {
        return $this->hasMany(NexcoreClientSarsReturn::class, 'client_id');
    }

    public function cipcReturns()
    {
        return $this->hasMany(NexcoreClientCipcReturn::class, 'client_id');
    }

    public function financials()
    {
        return $this->hasMany(NexcoreClientFinancial::class, 'client_id');
    }

    public function documents()
    {
        return $this->hasMany(NexcoreClientDocument::class, 'client_id');
    }

    public function tasks()
    {
        return $this->hasMany(NexcoreClientTask::class, 'client_id');
    }

    public function meetings()
    {
        return $this->hasMany(NexcoreClientMeeting::class, 'client_id');
    }

    public function alerts()
    {
        return $this->hasMany(NexcoreClientAlert::class, 'client_id');
    }

    public function auditTrail()
    {
        return $this->hasMany(NexcoreClientAuditTrail::class, 'client_id');
    }

    public function employees()
    {
        return $this->hasMany(NexcoreClientEmployee::class, 'client_id');
    }

    public function payPeriods()
    {
        return $this->hasMany(NexcoreClientPayPeriod::class, 'client_id');
    }

    public function payslips()
    {
        return $this->hasMany(NexcoreClientPayslip::class, 'client_id');
    }

    public function mibcoContributions()
    {
        return $this->hasMany(NexcoreClientMibcoContribution::class, 'client_id');
    }

    public function accounts()
    {
        return $this->hasMany(NexcoreClientAccount::class, 'client_id');
    }

    public function journals()
    {
        return $this->hasMany(NexcoreGlJournal::class, 'company_id');
    }

    public function budgets()
    {
        return $this->hasMany(NexcoreClientBudget::class, 'client_id');
    }
}