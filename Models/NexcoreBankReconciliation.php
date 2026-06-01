<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankReconciliation extends Model
{
    protected $table = 'cims_gl_bank_recon_master_header';

    protected $fillable = [
        'company_id', 'bank_account_id', 'statement_date',
        'statement_balance', 'gl_balance', 'reconciled_balance',
        'status', 'completed_by', 'completed_at', 'notes',
    ];

    protected $casts = [
        'statement_balance' => 'decimal:2',
        'gl_balance' => 'decimal:2',
        'reconciled_balance' => 'decimal:2',
        'statement_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(NexcoreBankAccount::class, 'bank_account_id');
    }

    public function lines()
    {
        return $this->hasMany(NexcoreBankReconLine::class, 'reconciliation_id');
    }
}
