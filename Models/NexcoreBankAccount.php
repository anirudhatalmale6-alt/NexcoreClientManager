<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankAccount extends Model
{
    protected $table = 'cims_gl_bank_accounts_linked_to_coa';

    protected $fillable = [
        'company_id', 'account_id', 'bank_id', 'bank_name', 'account_number',
        'branch_code', 'account_type', 'is_active',
        'opening_balance_date', 'opening_balance_amount',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance_date' => 'date',
        'opening_balance_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'company_id');
    }

    public function glAccount()
    {
        return $this->belongsTo(NexcoreGlChartOfAccount::class, 'account_id');
    }

    public function systemBank()
    {
        return $this->belongsTo(\Modules\CIMS_PMPRO\Models\NexcorSystemBank::class, 'bank_id');
    }

    public function transactions()
    {
        return $this->hasMany(NexcoreBankTransaction::class, 'bank_account_id');
    }

    public function statements()
    {
        return $this->hasMany(NexcoreBankStatement::class, 'bank_account_id');
    }
}
