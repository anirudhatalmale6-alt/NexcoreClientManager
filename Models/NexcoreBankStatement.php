<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankStatement extends Model
{
    protected $table = 'cims_gl_bank_statement_upload_register';

    protected $fillable = [
        'company_id', 'bank_account_id', 'statement_name', 'statement_number',
        'statement_ref',
        'period_from', 'period_to', 'upload_date', 'original_filename',
        'transaction_count', 'opening_balance', 'closing_balance',
        'total_credits', 'total_debits', 'credit_count', 'debit_count',
        'batch_ref', 'status', 'uploaded_by', 'imported_by', 'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'period_from' => 'date',
        'period_to' => 'date',
        'upload_date' => 'datetime',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(NexcoreBankAccount::class, 'bank_account_id');
    }
}
