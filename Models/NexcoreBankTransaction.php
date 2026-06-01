<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;

class NexcoreBankTransaction extends Model
{
    protected $table = 'cims_gl_bank_statement_upload_transactions';

    protected $fillable = [
        'company_id', 'bank_account_id', 'transaction_date', 'description',
        'amount', 'direction', 'balance', 'reference', 'status',
        'allocated_account_id', 'vat_type', 'vat_amount', 'net_amount',
        'batch_ref', 'journal_id', 'imported_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'imported_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'company_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(NexcoreBankAccount::class, 'bank_account_id');
    }

    public function allocatedAccount()
    {
        return $this->belongsTo(NexcoreGlChartOfAccount::class, 'allocated_account_id');
    }

    public function journal()
    {
        return $this->belongsTo(NexcoreGlJournal::class, 'journal_id');
    }
}
